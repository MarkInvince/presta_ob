<?php
/**
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*/

class AskForAQuote extends Module
{

	private $_tabClass = 'AdminMassUpdate';

	public function __construct()
	{
		global $cookie, $smarty;
		$this->name = 'askforaquote';
		$this->tab = 'front_office_features';
		$this->displayName = $this->l('Ask for a quote Module');
		$this->description = $this->l('Clients can ask for product quotes and bargain for a price');
		$this->author = 'Presta FABRIQUE';
		$this->version = 3.3;
		$this->module_key = '1c9fe60299f1579ffc1096d78abf8130';
		$this->bootstrap = true;
		parent::__construct();
		$this->base_url = Configuration::get('PS_SSL_ENABLED') ? preg_replace('/^http:/', 'https:', _PS_BASE_URL_) : _PS_BASE_URL_;
	}

	public function install()
	{
		if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
		{
			if (parent::install() == false
				|| !$this->registerHook('extraRight')
				|| !$this->registerHook('extraLeft')
				|| !$this->registerHook('rightColumn')
				|| !$this->registerHook('myAccountBlock')
				|| !$this->registerHook('CustomerAccount')
				|| !$this->registerHook('Header')
			)
				return false;
		}
		else
		{
			if (parent::install() == false
				|| !$this->registerHook('extraRight')
				|| !$this->registerHook('extraLeft')
				|| !$this->registerHook('myAccountBlock')
				|| !$this->registerHook('CustomerAccount')
				|| !$this->registerHook('top')
				|| !$this->registerHook('Header')
				|| !$this->registerHook('displayMyAccountBlockfooter')
			)
				return false;
		}

		DB::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'registered_requests`(
									`id_request` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
									`id_customer` INT NOT NULL,
									`id_product` VARCHAR(100) NOT NULL,
									`id_shop` INT NOT NULL,
									`qty` INT NOT NULL,
									`id_product_attribute` INT NOT NULL,
									`date` TEXT
									) ENGINE = InnoDB ;');
		DB::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'submitted_requests`(
									`id_request` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
									`id_customer` INT NOT NULL,
									`id_product` VARCHAR(100) NOT NULL,
									`id_shop` INT NOT NULL,
									`qty` INT NOT NULL,
									`id_product_attribute` INT NOT NULL,
									`date` TEXT,
									`status` INT NOT NULL,
									`id_group` INT NOT NULL
									) ENGINE = InnoDB ;');
		DB::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'registered_requests_attributes`(
									`id_request` INT NOT NULL,
									`id_customer` INT NOT NULL,
									`id_product` VARCHAR(100) NOT NULL,
									`id_shop` INT NOT NULL,
									`qty` INT NOT NULL,
									`id_attribute` INT NOT NULL,
									`value` INT NOT NULL
									) ENGINE = InnoDB ;');
		DB::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'submitted_requests_attributes`(
									`id_request` INT NOT NULL,
									`id_customer` INT NOT NULL,			
									`id_product` VARCHAR(100) NOT NULL,
									`id_shop` INT NOT NULL,
									`qty` INT NOT NULL,
									`id_attribute` INT NOT NULL,
									`value` INT NOT NULL
									) ENGINE = InnoDB ;');
		DB::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'askforaquote_emails` (
									`id` int(11) NOT NULL AUTO_INCREMENT,
									`custom_emails` text,
									`employee_ids` text,
									`main_email` bit(1) DEFAULT NULL,
									PRIMARY KEY (`id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

		DB::getInstance()->Execute('ALTER TABLE '._DB_PREFIX_.'customer ADD `quotes` INT NOT NULL');

		DB::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'submitted_requests_groups`(
									`id_group` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
									`id_customer` INT NOT NULL,
									`id_shop` INT NOT NULL,
									`original_price` TEXT,
									`bargained_price` TEXT,
									`id_currency` INT NOT NULL,
									`gname` TEXT NOT NULL,
									`comment` TEXT,
									`date` TEXT
									) ENGINE = InnoDB ;');

		$createtable = Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."askforaquote_settings (simple_checkout int(10),terms int(10),guest_checkout int(10),enable_bargain int(10),PRIMARY KEY(simple_checkout))");

		/* languages */
		Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."asforaquote_terms (
		id_lang BIGINT(4) UNSIGNED NOT NULL,
		customtext TEXT,
		PRIMARY KEY (id_lang))");

		$langs = Db::getInstance()->ExecuteS("SELECT * FROM  "._DB_PREFIX_."lang");
		foreach ($langs as $l)
		{
			$customtext = 'Terms and conditions.';
			Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."asforaquote_terms (id_lang, customtext) VALUES ('".$l['id_lang']."', '$customtext')");
		}
		/* end languages */

		DB::getInstance()->Execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."askforaquote_messages (id_message int(10) not null auto_increment, id_request int(10), id_product int(10), id_shop INT NOT NULL, price varchar(100), currency varchar(10), comment varchar(10000), user int(10), date_add datetime, PRIMARY KEY(id_message))");

		/* create categories */
		DB::getInstance()->Execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."askforaquote_categories (id int(10) not null auto_increment, id_category int(10), active int(3), PRIMARY KEY(id))");
		$cats = DB::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."category");
		foreach ($cats as $c)
			DB::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."askforaquote_categories (id_category, active) VALUES ('".$c['id_category']."','1')");

		/* create new tab on backoffice Customers - Quotes */
		$tab = new Tab();
		foreach (Language::getLanguages() as $language)
			$tab->name[$language['id_lang']] = 'Quotes';
		if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
			$tab->id_parent = 3;
		else
			$tab->id_parent = 10;
		$tab->class_name = 'AdminQuotes'; // you need to write tab code in AdminModuleName.php in module folder
		$tab->module = 'askforaquote'; // module name and folder
		$tab->position = Tab::getNewLastPosition($tab->id_parent);

		/* parent tab id */
		$r = $tab->save(); // saving your tab
		Configuration::updateValue('SODQUOTE_TAB_ID', $tab->id); // saving tab ID to remove it when uninstall
		return true;
	}

	public function uninstall()
	{
		if (parent::uninstall() == false)
			return false;

		$tab = new Tab(Configuration::get('SODQUOTE_TAB_ID'));
		$tab->delete();

		DB::getInstance()->Execute("ALTER TABLE "._DB_PREFIX_."customer DROP quotes");

		$sql = "DROP TABLE "._DB_PREFIX_."registered_requests";
		DB::getInstance()->Execute($sql);
		$sql = "DROP TABLE "._DB_PREFIX_."submitted_requests";
		DB::getInstance()->Execute($sql);
		$sql = "DROP TABLE "._DB_PREFIX_."registered_requests_attributes";
		DB::getInstance()->Execute($sql);
		$sql = "DROP TABLE "._DB_PREFIX_."submitted_requests_attributes";
		DB::getInstance()->Execute($sql);
		$sql = "DROP TABLE "._DB_PREFIX_."submitted_requests_groups";
		DB::getInstance()->Execute($sql);
		$sql = "DROP TABLE "._DB_PREFIX_."askforaquote_messages";
		DB::getInstance()->Execute($sql);
		$sql = "DROP TABLE "._DB_PREFIX_."askforaquote_categories";
		DB::getInstance()->Execute($sql);
		$sql = "DROP TABLE "._DB_PREFIX_."askforaquote_settings";
		DB::getInstance()->Execute($sql);
		$sql = "DROP TABLE "._DB_PREFIX_."asforaquote_terms";
		DB::getInstance()->Execute($sql);
		return true;
	}

	public function getContent()
	{
		global $cookie;

		if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
		{
			$id_shop = 0;
			$mintree = 2;
		}
		else
		{
			$id_shop = (int)Context::getContext()->shop->id;
			$mintree = 3;
		}

		/* added for categories tree */
		$groups = 'groups:';
		$catsintree = 'catstree:';
		$sqlcats = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."category WHERE active='1'");
		foreach ($sqlcats as $c)
		{
			$catsintree .= $c['id_category'].',';
			$cobj = new Category($c['id_category'], $cookie->id_lang, true);
			$subcats = $cobj->getSubCategories($cookie->id_lang);
			if (!empty($subcats))
				$groups .= $c['id_category'].',';
		}

		$output = '';

		/* submit terms text */
		if (Tools::isSubmit('subterms'))
		{
			$langs = Db::getInstance()->ExecuteS("SELECT * FROM  "._DB_PREFIX_."lang");
			foreach ($langs as $l)
			{
				$customtext = Tools::getValue('custom_'.$l['id_lang']);
				Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."asforaquote_terms SET customtext='$customtext' WHERE id_lang='".$l['id_lang']."'");
			}

			if ($id_shop > 0)
				$output .= "<div class='conf confirm'>".$this->l('Settings updated')."</div>";
			else
				echo $this->displayConfirmation($this->l('Settings updated'));
		}
		/* */

		DB::getInstance()->Execute(str_replace("PREFIX", _DB_PREFIX_, 'CREATE TABLE IF NOT EXISTS `PREFIXaskforaquote_settings`(
			`simple_checkout` INT NOT NULL
			) ENGINE = InnoDB ;'));

		$output .= '
			<script type="text/javascript" src="'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/js/checkboxes.js"></script>
			<link rel="stylesheet" href="'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/css/checkboxes.css" type="text/css" media="screen" charset="utf-8" />
			<script type="text/javascript" src="'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/js/ajax.js"></script>
			<script type="text/javascript">

				var ajax = new sack();

				function changeReqStatus(id_request){
					var id="img_"+id_request;
					var url="'.$this->base_url.__PS_BASE_URI__.'img/admin/";
					document.getElementById(id).src= (document.getElementById(id).src == url+"module_install.png" ? url+"module_notinstall.png" : url+"module_install.png");

					ajax.requestFile = "'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/ajax2.php?id_request="+id_request;
					ajax.runAJAX();

					return false;
				}';

		if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
			$output .= '
				$(document).ready(function() {
					$(\'#simple_checkout,#terms,#gc,#enable_bargain\').click( function(){
						var sc_val = $(\'#simple_checkout\').is(\':checked\') ? 1 : 0;
						var t_val = $(\'#terms\').is(\':checked\') ? 1 : 0;
						var gc_val = $(\'#gc\').is(\':checked\') ? 1 : 0;
						var brgn_val = $(\'#enable_bargain\').is(\':checked\') ? 1 : 0;
						ajax.requestFile = "'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/ajax2.php?simple_checkout="+sc_val+"&terms="+t_val+"&gc="+gc_val+"&bargain="+brgn_val;
						ajax.runAJAX();
					});
				});';
		else
			$output .= '
				function optionClick() {
					var sc_val = $(\'#simple_checkout\').is(\':checked\') ? 1 : 0;
					var t_val = $(\'#terms\').is(\':checked\') ? 1 : 0;
					var gc_val = $(\'#gc\').is(\':checked\') ? 1 : 0;
					var brgn_val = $(\'#enable_bargain\').is(\':checked\') ? 1 : 0;
					ajax.requestFile = "'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/ajax2.php?simple_checkout="+sc_val+"&terms="+t_val+"&gc="+gc_val+"&bargain="+brgn_val;
					ajax.runAJAX();
				};

				$(document).ready(function() {
					$(\'#simple_checkout,#terms,#gc,#enable_bargain\').iphoneStyle({
						onChange: function() {
							optionClick();
						}
					});
				});';

		$output .= '
				function showcats(id) {
					var device="sub"+id;
					var imagine="image"+id;

					if (document.getElementById(device).style.display=="none") {
						document.getElementById(device).style.display="block";
						document.getElementById(imagine).src="../modules/askforaquote/img/minus.gif";
					} else {
						document.getElementById(device).style.display="none";
						document.getElementById(imagine).src="../modules/askforaquote/img/plus.gif";
					}
				}

				function expandall(maxid) {
					var sir="'.$groups.'";
					for (i=2;i<=maxid;i++) {
						if (i==404) i=405;
						search=","+i+",";
						if (sir.indexOf(search) > 0) {
							var device="sub"+i;
							var imagine="image"+i;
							document.getElementById(device).style.display="block";
							document.getElementById(imagine).src="../modules/askforaquote/img/minus.gif";
						}
					}
				}

				function collapseall(maxid) {
					var sir="'.$groups.'";
					var min='.$mintree.';
					for (i=min;i<=maxid;i++) {
						if (i==404) i=405;
						search=","+i+",";
						if (sir.indexOf(search) > 0) {
							var device="sub"+i;
							var imagine="image"+i;
							document.getElementById(device).style.display="none";
							document.getElementById(imagine).src="../modules/askforaquote/img/plus.gif";
						}
					}
				}

				function checkall(maxid) {
					var sir="'.$catsintree.'";
					for (i=2;i<=maxid;i++) {
						if (i==405) i=411;
						search=","+i+",";
						if (sir.indexOf(search) > 0) {
							var device="acat_"+i;
							document.getElementById(device).checked=true;
						}
					}
				}

				function uncheckall(maxid) {
					var sir="'.$catsintree.'";
					for (i=2;i<=maxid;i++) {
						if (i==405) i=411;
						search=","+i+",";
						if (sir.indexOf(search) > 0) {
							var device="acat_"+i;
							document.getElementById(device).checked=false;
						}
					}
				}

			</script>
			<link href="'.__PS_BASE_URI__.'modules/askforaquote/css/adminstyle.css" rel="stylesheet" type="text/css" media="screen" />';
		if (Tools::substr(_PS_VERSION_, 0, 3) != '1.6')
			$output .= '<h2>'.$this->displayName.'</h2>';
		else
			$output .= '<link href="'.__PS_BASE_URI__.'modules/askforaquote/css/style16.css" rel="stylesheet" type="text/css" media="screen" />';

		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		global $cookie, $currentIndex;

		if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
			$id_shop = 0;
		else
			$id_shop = (int)Context::getContext()->shop->id;

		$currency = new Currency($cookie->id_lang);
		$currency = $currency->sign;
		$error = 0;
		$succes = 0;
		$output = '';

        /////////////submit notification emails
        if (Tools::isSubmit('save_email_notification')) {

            Db::getInstance()->ExecuteS("DELETE FROM "._DB_PREFIX_."askforaquote_emails");

            if (isset($_POST['main_email'])) {
                DB::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."askforaquote_emails (custom_emails, employee_ids, main_email) VALUES (NULL,NULL,true)");
            }
            if (isset($_POST['employee']) && isset($_POST['employee_email'])) {
                $temp = $_POST['employee_email'];
                $ids = '';
                foreach ($temp as $t) {
                    $ids.= $t . ',';
                }
                $ids = rtrim($ids, ',');

                DB::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."askforaquote_emails (custom_emails, employee_ids, main_email) VALUES (NULL,'".$ids."',NULL)");
            }

            if (isset($_POST['custom']) && isset($_POST['counter']) && isset($_POST['counter']) > 0) {
                $counter = $_POST['counter'];

                for ($i = 0; $i <= 100; $i++) {
                    if (isset($_POST['custom_email_' . $i])) {
                        $temp = $_POST['custom_email_' . $i];
                        if (substr(_PS_VERSION_, 0, 3) == '1.5')
                            $email = DB::getInstance()->escape($temp);
                        else
                            $email = mysql_escape_string($temp);

                        DB::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."askforaquote_emails (custom_emails, employee_ids, main_email) VALUES ('".$email."',NULL,NULL)");
                    }
                }
            }

            $output.='<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
        }
        //////////////////

		/////////////submit active categories
		if (Tools::isSubmit('submmitactivecats'))
		{

			$cats = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."category");

			foreach ($cats as $c)
			{
				$cid = $c['id_category'];
				$device = 'acat_'.$cid;
				if (isset($_POST[$device]))
					$active = 1;
				else
					$active = 0;

				if ($this->isinquote($cid))
					DB::getInstance()->Execute("UPDATE "._DB_PREFIX_."askforaquote_categories SET active='$active' WHERE id_category='$cid'");
				else
					DB::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."askforaquote_categories (id_category, active) VALUES ('$cid','$active')");
			}

			$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
		}
		//////////////////

		if (Tools::isSubmit('submitbargain'))
		{
			$error = 0;
			$succes = 0;
			$id_product = Tools::getValue('product');
			$id_request = Tools::getValue('request');
			$currencywrite = Tools::getValue('currency');
			$comment = mysql_real_escape_string(Tools::getValue('comment'));
			$price = mysql_real_escape_string(Tools::getValue('price'));
			if (($comment == '') || ($price == ''))
				echo $this->displayError($this->l('All the fields must be completed'));
			$user = 0;

			$id_customer = Tools::getValue('id_customer');
			$results = DB::getInstance()->ExecuteS("Select * FROM "._DB_PREFIX_."customer WHERE id_customer=".$id_customer);
			foreach ($results as $row)
			{
				$email = $row['email'];
				$firstname = $row['firstname'];
				$lastname = $row['lastname'];
			}

			$data = array(
				'{submitter}' => 'admin',
				'{firstname}' => $firstname,
				'{lastname}' => $lastname,
				'{date}' => Tools::displayDate(date('Y-m-d H:i:s'), (int)($cookie->id_lang), 1),
				'{price}' => $price,
				'{currency}' => $currency,
				'{comment}' => $comment
			);

			if ($error == 0)
			{
				$insertcom = DB::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."askforaquote_messages (id_request, id_product, id_shop, price, currency, comment, user, date_add) VALUES ('$id_request','$id_product','$id_shop','$price',' $currencywrite','$comment','$user',NOW())");

				/* notification email addresses */
				$email_list = array();
				$main = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."askforaquote_emails WHERE main_email is not null");
				$c_eids = Db::getInstance()->ExecuteS("SELECT custom_emails FROM "._DB_PREFIX_."askforaquote_emails WHERE custom_emails is not null");
				$eids = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."askforaquote_emails WHERE employee_ids is not null");
				$eids = $eids[0]['employee_ids'];
				$employee_emails = Db::getInstance()->ExecuteS("SELECT email FROM "._DB_PREFIX_."employee WHERE id_employee IN (".$eids.")");

				if (!empty($main))
					$email_list[] = Configuration::get('PS_SHOP_EMAIL');

				if (!empty($employee_emails))
				{
					foreach ($employee_emails as $e)
						$email_list[] = $e['email'];
				}

				if (!empty($c_eids))
				{
					$v = 1;
					foreach ($c_eids as $e)
						$email_list[] = $e['custom_emails'];
				}
				/* emai laddresses end */

				Mail::Send((int)($cookie->id_lang), 'submitbargain', Mail::l('New bargain action'), $data, $email, null /* to name */, null, $firstname.' '.$lastname, null /* attachment */, null /* smtp */, dirname(__FILE__).'/frontoffice/mails/', false /* die */, null /* idshop */, null /* bcc */);

				foreach ($email_list as $k => $v)
					Mail::Send((int)($cookie->id_lang), 'submitbargain_confirm', Mail::l('Bargain confirmation'), $data, $v, null /* to name */, null, $firstname.' '.$lastname, null /* attachment */, null /* smtp */, dirname(__FILE__).'/frontoffice/mails/', false /* die */, null /* idshop */, null /* bcc */);
					
				if ($insertcom)
					$output .= '<div class="conf confirm">'.$this->l('Your comment has been submitted').'</div>';
				else
					$output .= "<div class='alert error'>".$this->l('Database error')."</div>";
			}
		}

		// we begin listing products
		$prdcts = array();
		if ($id_customer = Tools::getValue('id_customer'))
		{

			$res = DB::getInstance()->ExecuteS('SELECT CONCAT(firstname," ",lastname) as "name" FROM '._DB_PREFIX_.'customer WHERE id_customer='.$id_customer.'');

			$output .= '
				<script type="text/javascript">

				function showbargain(request) {
					var id="bargainform" + request;
					var device="bargaindevice" + request;

					if (document.getElementById(id).style.visibility == "hidden") {
						document.getElementById(id).style.visibility = "visible";
						document.getElementById(device).style.display = "block";
					}
					else {
						document.getElementById(id).style.visibility = "hidden";
						document.getElementById(device).style.display = "none";
					}
				}

				function showdetails(request) {
					var id="details" + request;
					var device="detailsdevice" + request;

					if (document.getElementById(id).style.visibility == "hidden") {
						document.getElementById(id).style.visibility = "visible";
						document.getElementById(device).style.display = "block";
					}
					else {
						document.getElementById(id).style.visibility = "hidden";
						document.getElementById(device).style.display = "none";
					}
				}

				</script>
				<fieldset class="panel"><div class="panel-heading"><i class="icon-question-sign"></i> '.$this->l('Quotes').'</div><br /><h3>'.$this->l('Requests of ').$res[0]['name'].'.</h3>';

			/* customerdetails */
			$customer = new Customer($id_customer);
			$guestgroup = DB::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."group_lang WHERE name='Guest'");
			if ($guestgroup)
				$gpid = $guestgroup['id_group'];
			else
				$gpid = 0;
			///
			$req_b = DB::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'askforaquote_settings');
			$bargain = $req_b[0]['enable_bargain'];

			$output .= '<table class="table" cellpadding="0" cellspacing="0" width="80%" style="margin:auto">';
			$output .= '<tr>';
			if ($bargain == 1)
				$output .= '<th>'.$this->l('Status').'</th>';
			$output .= '<th>'.$this->l('Product').'</th><th>'.$this->l('Details').'</th>';
			if ($bargain == 1)
				$output .= '<th>'.(($customer->id_default_group == $gpid) ? '' : $this->l('Bargain')).'</th>';
			;
			$output .= '<th>'.$this->l('Quantity').'</th>
							  <th>'.$this->l('Date requested').'</th>
						   </tr>';
			global $cookie;

			$res = DB::getInstance()->ExecuteS("SELECT p.id_product, p.name, pd.reference, p.link_rewrite, r.id_request, CONCAT(SUBSTR(r.date,1,12), REPLACE(SUBSTR(r.date,13,10),'-',':')) AS 'date', r.status
					FROM "._DB_PREFIX_."submitted_requests r, "._DB_PREFIX_."product_lang p , "._DB_PREFIX_."product_attribute pd
					WHERE pd.id_product = p.id_product
					AND pd.id_product_attribute = r.id_product_attribute
					AND p.id_product=r.id_product
					AND p.id_lang='$cookie->id_lang'
					AND r.id_shop='$id_shop'
					AND r.id_customer='$id_customer'
					GROUP BY r.id_request
					ORDER BY r.date ASC");

			$req = DB::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."submitted_requests WHERE id_customer='$id_customer' AND id_shop='$id_shop' ORDER BY date");

			$requests = array();
			foreach ($req as $request)
			{
				$id_request = $request['id_request'];
				$realprd = Tools::substr($request['id_product'], 0, strpos($request['id_product'], '06'));
				$prouductquoted = new Product($realprd, true, (int)$cookie->id_lang);
				$request['reference'] = $prouductquoted->reference;
				$request['name'] = $prouductquoted->name;
				$hour = Tools::substr($request['date'], 11);
				$request['date'] = Tools::substr($request['date'], 0, 11)." ".str_replace('-', ':', $hour);
				$requests[] = $id_request;
				$sqlattr = 'SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`
						FROM `'._DB_PREFIX_.'attribute_group` ag
						LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$cookie->id_lang.')
						LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`
						LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.')
						INNER JOIN `'._DB_PREFIX_.'submitted_requests_attributes` rra ON (rra.id_customer = '.$id_customer.' AND rra.`value` = a.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.' AND rra.`id_request` = '.$id_request.')
						ORDER BY agl.`name` ASC, al.`name` ASC';
				$r = Db::getInstance()->ExecuteS($sqlattr);
				$request['attributes'] = $r;
				$prdcts[] = $request;
			}

			$requestsString = '('.implode(', ', $requests).')';
			/* there may be products without attributes */
			$res2 = DB::getInstance()->ExecuteS('SELECT p.id_product, p.name, p.link_rewrite, r.id_request, CONCAT(SUBSTR(r.date,1,12), REPLACE(SUBSTR(r.date,13,10),"-",":")) AS "date", r.status
									FROM '._DB_PREFIX_.'submitted_requests r, '._DB_PREFIX_.'product_lang p
									WHERE p.id_product=r.id_product
									AND p.id_lang='.$cookie->id_lang.'
									AND r.id_customer='.$id_customer.'
									AND r.id_request NOT IN '.$requestsString.'
									GROUP BY r.id_request
									ORDER BY r.date ASC');

			if (!empty($res2))
			{
				foreach ($res2 as $request)
				{
					$request['attributes'] = array();
					$request['reference'] = '';
					$prdcts[] = $request;
				}
			}

			foreach ($prdcts as $r)
			{
				$id_request = $r['id_request'];
				$realprd = Tools::substr($r['id_product'], 0, strpos($r['id_product'], '06'));
				$sqllast = DB::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."askforaquote_messages WHERE id_request='$id_request' ORDER BY date_add DESC");
				if ($sqllast)
				{
					$lastprice = $sqllast['price'];
					$lastuser = (($sqllast['user'] == 0) ? 'admin' : 'client');
					$currencycustomer = $sqllast['currency'];
				}
				else
				{
					$realprd = Tools::substr($r['id_product'], 0, strpos($r['id_product'], '06'));
					$prouductquoted = new Product($realprd, true, $cookie->id_lang);
					$lastprice = number_format($prouductquoted->getPrice(true, null), 2, '.', ' ');
				}

				$comments = array();
				$sqlcom = DB::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."askforaquote_messages WHERE id_request='$id_request' AND id_shop='$id_shop' ORDER BY date_add ASC");

				foreach ($sqlcom as $com)
					$comments[] = $com;

				$output .= '<tr>';
				if ($bargain == 1)
					$output .= '
				<td>'.($r['status'] ? '<a href="#" onclick="return changeReqStatus('.$r['id_request'].');"><img id="img_'.$r['id_request'].'" src="../img/admin/module_install.png" /></a>' : '<a href="#" onclick="return changeReqStatus('.$r['id_request'].');"><img id="img_'.$r['id_request'].'" src="../img/admin/module_notinstall.png" /></a>').'</td>';
				$output .= '<td>'.$r['name'].'<br>';

				foreach ($r['attributes'] as $ratt)
					$output .= $ratt['attribute_group'].' - '.$ratt['name'].' <br>';
				$output .= $this->l('Reff').' : '.$r['reference'];
				$output .= '</td><td>';
				$moneda = new Currency($cookie->id_currency);
				if ($sqllast)
					$output .= $lastprice.' '.$this->l('by').' '.$lastuser;
				else
					$output .= (($lastprice > 0) ? $lastprice : '-').' '.$moneda->sign;

				if ($bargain == 1)
					$output .= ' '.$this->l('by admin').'<br /><a style="cursor:pointer" onclick="showdetails('.$r['id_request'].')">'.$this->l('Details').'</a>';
				$output .= '</td>';
				if ($bargain == 1)
					$output .= '<td><input type="button" id="bargain" name="bargain" value="'.$this->l('Bargain').'" onclick="showbargain('.$r['id_request'].')" class="button_small"style="visibility:'.(($customer->id_default_group == $gpid) ? 'hidden' : 'visible').'"/></td>';

				$output .= '<td>'.$r['qty'].'</td>
									<td>'.$r['date'].'</td>
								</tr>';
				$output .= '<tr id="bargainform'.$r['id_request'].'" style="visibility:hidden;"><td colspan="6">

					<form method="post" class="std" id="bargaindevice'.$r['id_request'].'" style="display:none">
						<input type="hidden" name="request" id="request" value="'.$r['id_request'].'" />
						<input type="hidden" name="product" id="product" value="'.$realprd.'" />
						<input type="hidden" name="currency" id="currency" value="'.(($sqllast) ? $currencycustomer : $currency).'" />

						<fieldset style="border:none">
						<span><b>'.$this->l('Bargain for').'&nbsp;'.$r['name'].'</b></span><br /><br />
						<table border="0" width="100%">
						<tr>
						<td align="left" valign="top" style="border:none">
						<label style="text-align:left; margin-left:10px">'.$this->l('Comment').'</label><br />
						<textarea name="comment" id="comment" cols="60" rows="5" maxlenght="20"></textarea>
						</td>
						<td align="left" valign="top" style="border:none">
						<label style="text-align:left; margin-left:10px">'.$this->l('Price').'</label><br />
						<input type="text" name="price" id="price" maxlenght="5" style="height:20px;" value="'.(($sqllast) ? $currencycustomer : $currency).' "/>
						</td>
						<td align="left" style="border:none">
						<input type="submit" name="submitbargain" id="submitbargain" value="'.$this->l('Save').'" class="button" />
						</td>
						</tr>
						</table>
						</fieldset>
					</form>
					</td></tr>';

				$output .= '<tr id="details'.$r['id_request'].'" style="visibility:hidden;"><td colspan="6"><div id="detailsdevice'.$r['id_request'].'" style="display:none; margin-bottom:20px;">
					<h4>'.$this->l('Details for ').'<i>'.$r['name'].'</i></h4>';
				foreach ($comments as $com) {
					$output .= '<div style="background:#ccc">
			<fieldset>
			<table border="0" width="100%">
			<tr>
			<td align="left" style="border:none">
			'.(($com['user'] == 0) ? 'admin' : 'client').'
			</td>
			<td align="left" style="border:none">
			<p style="text-align:left">'.$com['comment'].'</p>
			</td>
			<td align="right" style="border:none">
			<p>'.$com['price'].' '.$com['currency'].'</p>
			</td>
			</tr>
			</table>
			</fieldset>

			</div>';
				}
				$output .= '</div>

					</td></tr>';
			}
			$output .= '</table></fieldset>';
		}
		else
		{
			// begin listing customers who have quotes
			/*
			$oBy = Tools::getValue('orderBy') ? Tools::getValue('orderBy') : '';
			$oWay = Tools::getValue('orderWay') ? Tools::getValue('orderWay') : '';

			$res = DB::getInstance()->ExecuteS('SELECT CONCAT(SUBSTR(c.firstname,1,1),". ",c.lastname ) as "customer", COUNT(r.id_request) as "nr", CONCAT(SUBSTR(MAX(r.date),1,12), REPLACE(SUBSTR(MAX(r.date),13,10),"-",":")) AS "date", r.id_request, r.id_customer FROM '._DB_PREFIX_.'submitted_requests r, '._DB_PREFIX_.'customer c WHERE c.id_customer=r.id_customer AND r.id_shop='.$id_shop.' GROUP BY r.id_customer'.(($oBy && $oWay) ? ' ORDER BY '.$oBy.' '.$oWay : ''));
			if (!empty($res)) {
				$output = '<table class="table" cellpadding=0 cellspacing=0 border="0" width="80%" style="margin:auto">';
				$output .='<tr>
								   <th>'.$this->l('New').'</th>
								   <th>'.$this->l('Customer').'&nbsp;'.( ($oBy == 'customer' && $oWay == 'asc') ? '<img src="../img/admin/up_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=customer&orderWay=asc"><img src="../img/admin/up.gif"></a>').'&nbsp;'.( ($oBy == 'customer' && $oWay == 'desc') ? '<img src="../img/admin/down_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=customer&orderWay=desc"><img src="../img/admin/down.gif"></a>').'</th>
								   <th>'.$this->l('Number of requests').'&nbsp;'.( ($oBy == 'nr' && $oWay == 'asc') ? '<img src="../img/admin/up_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=nr&orderWay=asc"><img src="../img/admin/up.gif"></a>').'&nbsp;'.( ($oBy == 'nr' && $oWay == 'desc') ? '<img src="../img/admin/down_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=nr&orderWay=desc"><img src="../img/admin/down.gif"></a>').'</th>
								   <th>'.$this->l('Date of last request').'&nbsp;'.( ($oBy == 'date' && $oWay == 'asc') ? '<img src="../img/admin/up_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=date&orderWay=asc"><img src="../img/admin/up.gif"></a>').'&nbsp;'.( ($oBy == 'date' && $oWay == 'desc') ? '<img src="../img/admin/down_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=date&orderWay=desc"><img src="../img/admin/down.gif"></a>').'</th>
							   </tr>';
				foreach ($res as $r) {
					$subres2 = DB::getInstance()->ExecuteS('SELECT id_request FROM '._DB_PREFIX_.'submitted_requests WHERE id_customer='.$r['id_customer'].' AND status !=1');

					$output .= '<tr>
										<td>
											'.(empty($subres2) ? '' : '<img src="../img/admin/news-new.gif"/>').'
										</td>
										<td>
											<a href='.$_SERVER['REQUEST_URI'].'&id_customer='.$r['id_customer'].'>'.$r['customer'].'</a>
										</td>
										<td>
										   <a href='.$_SERVER['REQUEST_URI'].'&id_customer='.$r['id_customer'].'>'.$r['nr'].'</a>
										</td>
										<td>
											<a href='.$_SERVER['REQUEST_URI'].'&id_customer='.$r['id_customer'].'>'.$r['date'].'</a>
										</td>
									</tr>';
				}
				$output .= '</table>';
			} else
				$output = '<fieldset class="panel"><div class="panel-heading"><i class="icon-question-sign"></i> '.$this->l('Quotes').'</div><p>'.$this->l('No quotes were submitted yet.').'</p></fieldset>';
			*/
			/* end of quote listing parts */

			$res = DB::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'askforaquote_settings');

			$output .= '<br /><fieldset class="panel"><div class="panel-heading"><i class="icon-cogs"></i> '.$this->l('General settings').'</div>';
			$output .= '<label class="conf_title">'.$this->l('Guest Checkout').':</label>';
			$output .= '<div class="margin-form"><input type="checkbox" name="gc" value="1" id="gc" '.((isset($res[0]['guest_checkout']) && ($res[0]['guest_checkout'] == 1)) ? "checked='checked'" : "").'/>'.$this->l('Check to enable Guest Checkout (autosave)').'</div><div class="clear"></div>';

			$output .= '<label class="conf_title">'.$this->l('Terms and conditions').':</label>';
			$output .= '<div class="margin-form"><input type="checkbox" name="terms" value="1" id="terms" '.((isset($res[0]['terms']) && ($res[0]['terms'] == 1)) ? "checked='checked'" : "").'/>'.$this->l('Check to make terms and conditions compulsory (autosave)').'</div><div class="clear"></div>';

			$output .= '<label class="conf_title">'.$this->l('Simple checkout').':</label>';
			$output .= '<div class="margin-form"><input type="checkbox" name="simple_checkout" value="1" id="simple_checkout" '.((isset($res[0]['simple_checkout']) && ($res[0]['simple_checkout'] == 1)) ? "checked='checked'" : "").'/>'.$this->l('Check to use simple checkout (autosave)').'<div class="hint" style="display:block;">'.$this->l('NOTE: If active some reuired fields will be hidden and filled with predefined bogus data (country, city, ZIP etc.).').'</div></div><div class="clear"></div>';

			$output .= '<label class="conf_title">'.$this->l('Enable bargain').':</label>';
			$output .= '<div class="margin-form"><input type="checkbox" name="enable_bargain" value="1" id="enable_bargain" '.((isset($res[0]['enable_bargain']) && ($res[0]['enable_bargain'] == 1)) ? "checked='checked'" : "").'/>'.$this->l('Check to enable bargain option (autosave)').'</div><div class="clear"></div>';

			/* email notification start */

			$employees = Db::getInstance()->ExecuteS("SELECT id_employee, firstname, lastname, email FROM "._DB_PREFIX_."employee");
			$shop_email = Db::getInstance()->ExecuteS("SELECT email FROM "._DB_PREFIX_."contact c INNER JOIN "._DB_PREFIX_."contact_shop cs ON c.id_contact = cs.id_contact LIMIT 1");
			$shop_email = $shop_email[0]['email'];

			$main = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."askforaquote_emails WHERE main_email is not null");

			$eids = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."askforaquote_emails WHERE employee_ids is not null");

			if (!empty($eids)) {
				$eids = $eids[0]['employee_ids'];
				$temp = explode(',', $eids);
				$eids = $temp;
			}

			$c_eids = Db::getInstance()->ExecuteS("SELECT custom_emails FROM "._DB_PREFIX_."askforaquote_emails WHERE custom_emails is not null");

			$output .= '</fieldset><br />';
			$output .= '<fieldset class="panel"><div class="panel-heading"><i class="icon-envelope-o"></i> '.$this->l('Send notification emails to:').'</div>';
			$output .= '<div class="margin-form"><form action="" method="post">';
			$output .= '<input '.(!empty($main) ? 'checked' : '').' type="checkbox" name="main_email" value="1">&nbsp;&nbsp;'.$this->l('Main shop email').' ('.Configuration::get('PS_SHOP_EMAIL').') <br /><br />';
			$output .= '<input '.(!empty($eids) ? 'checked' : '').' type="checkbox" name="employee" value="1">&nbsp;&nbsp;'.$this->l('Employee').'&nbsp;('.$this->l('select more by holding the CTRL button').')<br />';
			$output .= '<select name="employee_email[]" multiple style="width:332px;margin:5px 0 0 18px;padding:2px;">';
			foreach ($employees as $employee)
				$output .= '<option '.(in_array($employee['id_employee'], $eids) ? 'selected="selected"' : '').' value="'.$employee['id_employee'].'">'.$employee['firstname'].' '.$employee['lastname'].' ('.$employee['email'].')</option>';
			$output .= '</select>';
			$output .= '<input type="hidden" name="counter" id="email_counter" value="'.count($c_eids).'"><br /><br /><input '.(!empty($c_eids) ? 'checked' : '').' type="checkbox" name="custom" value="1">&nbsp;&nbsp;';
			$output .= $this->l('Custom').'&nbsp;&nbsp;<div class="clear"></div><input id="custom_email" type="text" name="custom_email" value="" class="text"><span class="plusimg"><img class="add_custom_email" style="vertical-align:middle;cursor:pointer;padding:5px;" src="' .__PS_BASE_URI__. 'modules/askforaquote/img/add.gif"></span><div class="clear"></div>';
			$output .= '<div id="custom_email_list" class="clearfix">';
			if (!empty($c_eids))
			{
				$v = 1;
				foreach ($c_eids as $e)
				{
					$output .= '<div class="customaddress" id="div_'.$v.'" ><input readonly type="text" name="custom_email_'.$v.'" value="'.$e['custom_emails'].'"><span><img id="delete_'.$v.'" class="delete_custom_email" src="' .__PS_BASE_URI__. 'modules/askforaquote/img/delete_new.gif"></span></div>';
					$v++;
				}
			}
			$output .= '</div><br />';
			$output .= '<input type="submit" style="cursor:pointer;" class="button" value="'.$this->l('Save notification emails').'" id="save_email_notification" name="save_email_notification"></form><br /><div class="hint" style="display:block;">'.$this->l('NOTE: checkbox correspong to a setting must be checked for option to be enabled!').'</div></div>';
			$output .= '<script type="text/javascript">
			$( document ).ready(function() {
				counter = $("#email_counter").val();
				$( ".add_custom_email" ).click(function() {
					var $email = $("#custom_email").val();
					var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
					if ($email == "" || !re.test($email))
						{
                            alert("'.$this->l("Please enter a valid email address").'");
                             return false;
						}else{
						counter++;
						$("#email_counter").val(counter);
					$("#custom_email_list").append("<div id=\"div_"+counter+"\" class=\"customaddress\"><input readonly type=\"text\" name=\"custom_email_"+counter+"\" value='.'"'.'+$email+"><span><img id=\"delete_"+counter+"\" class=\"delete_custom_email\" src=\"' .__PS_BASE_URI__. 'modules/askforaquote/img/delete_new.gif\"></span></div>");
				}
				});
				$( ".delete_custom_email" ).live("click",function() {
					var temp = $(this).attr("id");
					var temp2 = temp.split("_");

					$("#div_"+temp2[1]).remove();
					counter--;
					$("#email_counter").val(counter);
				});
			});

			</script>';

			/* email notification end */

			$sqlmax = Db::getInstance()->ExecuteS("SELECT id_category FROM "._DB_PREFIX_."category ORDER BY id_category DESC LIMIT 1");
			$maxid = $sqlmax[0]['id_category'];

			$restree = $this->generatetree(1);

			$output .= '</fieldset><br />';
			$output .= '<fieldset class="panel"><div class="panel-heading"><i class="icon-folder-open"></i> '.$this->l('Active in categories:').'</div><div class="margin-form"><form action="'.$currentIndex.'&configure=askforaquote&token='.Tools::getValue('token').'&tab_module=front_office_features&module_name=askforaquote" method="POST"><div class="category-filter"><a style="cursor:pointer" onclick="expandall('.$maxid.')">'.$this->l('Expand all').'</a>&nbsp;<a>|</a>&nbsp;<a style="cursor:pointer" onclick="collapseall('.$maxid.')">'.$this->l('Collapse all').'</a>&nbsp;<a>|</a>&nbsp;<a style="cursor:pointer" onclick="checkall('.$maxid.')">'.$this->l('Check all').'</a>&nbsp;<a>|</a>&nbsp;<a style="cursor:pointer" onclick="uncheckall('.$maxid.')">'.$this->l('Uncheck all').'</a></div><div style="margin:10px 0; padding-bottom:10px;border-bottom:1px solid #CCC;">'.$restree.'</div><input type="submit" name="submmitactivecats" id="submmitactivecats" value="'.$this->l('Save active categories').'" class="button" style="cursor:pointer;" /></form><br /><div class="hint" style="display:block;">'.$this->l('NOTE: main categories, as well as "Home" must be checked for sublevels to be active!').'</div></div>';

			$output .= '</fieldset><br/>';
			$output .= '<fieldset class="panel"><div class="panel-heading"><i class="icon-unlock"></i> '.$this->l('Terms and conditions text').'</div>';
			$output .= '<form action="'.$currentIndex.'&configure=askforaquote&token='.Tools::getValue('token').'&tab_module=front_office_features&module_name=askforaquote" method="POST">';

			$alllanguages = Db::getInstance()->ExecuteS("SELECT * FROM  "._DB_PREFIX_."lang");
			$languages = Db::getInstance()->ExecuteS("SELECT * FROM  "._DB_PREFIX_."lang");
			$existinglanguages = Db::getInstance()->getRow("SELECT * FROM  "._DB_PREFIX_."lang");
			$firstlang = $existinglanguages['id_lang'];

			$languagestemp = Db::getInstance()->ExecuteS("SELECT * FROM  "._DB_PREFIX_."lang ORDER BY id_lang DESC LIMIT 1");
			$lastlang = $languagestemp[0]['id_lang'];

			$output .= '<label class="conf_title">'.$this->l('Select language: ').'</label><div class="margin-form">';
			$output .= '<script type="text/javascript">
			function showlang(id,maxlang) {
			var device="emailtexts_"+id;
			document.getElementById(device).style.display="block";
			for (i=1;i<=maxlang;i=i+1) {
			newid="emailtexts_" + i;
			id2=i;
			if (id2 != id) { document.getElementById(newid).style.display="none"; }
			}
			}
			  </script>';

			foreach ($languages as $language)
				$output .= '<img id="lang_'.$language['id_lang'].'" src="../img/l/'.$language['id_lang'].'.jpg" onclick="showlang('.$language['id_lang'].','.$lastlang.')" style="cursor:pointer">';
			$output .= '</div><div class="clear"></div><br />';

			for ($i = 1; $i <= $lastlang; $i++)
			{
				$langdata = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."asforaquote_terms WHERE id_lang='".$i."'");

				if (version_compare(_PS_VERSION_, '1.4.0.0') >= 0)
					$output .= '
							<script type="text/javascript">
								var iso = \''.(file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
								var pathCSS = \''._THEME_CSS_DIR_.'\' ;
								var ad = \''.dirname($_SERVER['PHP_SELF']).'\' ;
							</script>
							<script type="text/javascript" src="' .__PS_BASE_URI__. 'js/tiny_mce/tiny_mce.js"></script>
							<script type="text/javascript" src="' .__PS_BASE_URI__. 'js/tinymce.inc.js"></script>
							<script language="javascript" type="text/javascript">
								id_language = Number('.$id_lang_default.');
								tinySetup();
							</script>';
									else {
										$output .= '
							<script type="text/javascript" src="' .__PS_BASE_URI__. 'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
							<script type="text/javascript">
								tinyMCE.init({
									mode : "textareas",
									theme : "advanced",
									plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
									theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
									theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
									theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
									theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
									theme_advanced_toolbar_location : "top",
									theme_advanced_toolbar_align : "left",
									theme_advanced_statusbar_location : "bottom",
									theme_advanced_resizing : false,
									content_css : "' .__PS_BASE_URI__. 'themes/'._THEME_NAME_.'/css/global.css",
									document_base_url : "' .__PS_BASE_URI__. '",
									width: "600",
									height: "auto",
									font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
									template_external_list_url : "lists/template_list.js",
									external_link_list_url : "lists/link_list.js",
									external_image_list_url : "lists/image_list.js",
									media_external_list_url : "lists/media_list.js",
									elements : "nourlconvert",
									entity_encoding: "raw",
									convert_urls : false,
									language : "'.(file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'"
								});
								id_language = Number('.$id_lang_default.');
							</script>';
				}

				$output .= '
				<div id="emailtexts_'.$i.'" style="display: '.(($i == $firstlang) ? 'block' : 'none').'; ">
				<label class="conf_title">'.$this->l('Current language: ').'</label><div class="margin-form"><img src="../img/l/'.$i.'.jpg"/></div><div class="clear"></div><br />
				<label class="conf_title">'.$this->l('Text:').'</label><div class="margin-form"><textarea class="rte" cols="120" rows="10" type="text" name="custom_'.$i.'">'.$langdata['customtext'].'</textarea></div></div>';
			}

			$output .= '<br /><div style="clear:both"></div><label>&nbsp;</label><input type="submit" name="subterms" id="subterms" value="'.$this->l('Save Text').'" class="button" style="cursor:pointer;" />';

			$output .= '</fieldset>';

			$output .= '<br/><fieldset class="panel"><div class="panel-heading"><i class="icon-info-circle"></i> '.$this->l('Guides and help').'</div><br /><br /><h3>'.$this->l('Product list button guide').'</h3><div style="width:80%;margin:auto;">
				<p>The following code samples will help you add the quotation button anywhere on your website (product list, featured products etc.).<br />Follow the guide carefully for each file as you might end up with errors or a non-working shop!<br /><b>AND DO ALWAYS MAKE A BACKUP FIRST OF THE FILE YOU WANT TO EDIT</b></p>
				<p>Steps:<br />
				 1. Open the file you need to edit - product_list.tpl if you want to add the button to the category / product list page<br />
				 2. Add the following code to the beginning of the file (after the comment part, before any {if} section)</p>
				<iframe width="100%" height="190" src="'._PS_BASE_URL_._MODULE_DIR_.'askforaquote/guide/add-to-head.txt"></iframe><br /><br />
				<p>3. Locate the product image insert (usually inside the link with class "product_img_link") and add a class to it like this: <strong>image{$product.id_product}</strong><br />
				So if before was:<br />
				<em>&lt;img class=&quot;replace-2x img-responsive&quot; src=</em><br />
				Now should look like this:<br />
				<em>&lt;img class=&quot;image{$product.id_product} replace-2x img-responsive&quot; src=</em><br /></p>
				<p>4. Find the "Add to cart" button and add the following code after or before!<br />
				(actually it can be inserted anywhere, but make sure it\'s inside the {foreach} cycle)</p>
				<iframe width="100%" height="200" src="'._PS_BASE_URL_._MODULE_DIR_.'askforaquote/guide/add-to-source.txt"></iframe><br /><br />
				<p>5. Save the file and you\'re done!</p>
				<p>PLEASE NOTE that the two code samples above can be found inside the "guide" directory of this module. Plus we added a "product_list_sample.tpl" file for reference!</p>
				<p><strong style="color:red;">POST SCRIPTUM 2: this will not take the category permissions of the module into consideration!</strong></p>
				</div><br /><br />';

			$output .= '<br/><h3>'.$this->l('Radio button style attribute guide').'</h3><div style="width:80%;margin:auto;">
				<p>If you are using attributes in form of radio buttons instead of dropdown menus, you must follow the guide below, otherwise these will not be detected by our module!<br />Follow the guide carefully as you might end up with errors or a non-working shop!<br /><b>AND DO ALWAYS MAKE A BACKUP FIRST OF THE FILE YOU WANT TO EDIT!!!</b></p>
				<p>Steps:<br /><br />
				 1. Open the file called "product.tpl" from the root of your current theme, and around line 306 find the following:<br />
				 <strong>&lt;input type=&quot;radio&quot; class=&quot;attribute_radio&quot; ... &gt;</strong><br /><br />
				 2. At the very end of it, just before the closing tag (&gt;), add the following:</p>
				<iframe width="100%" height="40" src="'._PS_BASE_URL_._MODULE_DIR_.'askforaquote/guide/radiobuttonfix.txt"></iframe><br /><br />
				<p>3. Save the file and you\'re done!</p>
				<p>PLEASE NOTE that the code sample above can be found inside the "guide" directory of this module. Plus we added a "product_sample.tpl" file for reference!</p>
				</div><br /><br />';

			$output .= '<br/><h3>'.$this->l('Notes').'</h3><div style="width:80%;margin:auto;"><iframe width="100%" height="200" src="'._PS_BASE_URL_._MODULE_DIR_.'askforaquote/readme_en.txt"></iframe></div></fieldset><br/><br />';
		}

		return $output;
	}

	public function hookRightColumn($params)
	{

		global $smarty, $cookie;

		$prdcts = array();
		if ($cookie->logged) {
			$res = DB::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'registered_requests WHERE id_customer='.$cookie->id_customer);

			$textattribs = isset($_COOKIE['rOatext']) ? $_COOKIE['rOatext'] : '';

			foreach ($res as $r) {
				$realprd = Tools::substr($r['id_product'], 0, strpos($r['id_product'], '06'));
				$sirattribs = explode('p', $textattribs);
				foreach ($sirattribs as $a) {
					if (strpos('x'.$a, $r['id_product'].':') > 0) {
						$texta = str_replace($r['id_product'].':', '', $a);
						break;
					}
				}

				$product = new Product((int)$realprd);
				$product->prodcode = $r['id_product'];
				$product->cant = $r['qty'];
				$product->textattribs = $texta;

				$prdcts[] = $product;
			}
		}
		if (empty($prdcts) || !$cookie->logged) {
			$c = isset($_COOKIE['rOp']) ? $_COOKIE['rOp'] : array();
			$qty = isset($_COOKIE['rOq']) ? $_COOKIE['rOq'] : '';
			$textattribs = isset($_COOKIE['rOatext']) ? $_COOKIE['rOatext'] : '';

			if (!empty($c))
				foreach ($c as $prdctId) {

					$poz = strpos($qty, '-'.$prdctId);
					$qty_item = Tools::substr($qty, $poz);
					$s = explode('-', $qty_item);
					$qty_item = (int)Tools::substr($s[1], strpos($s[1], '_') + 1);

					$sirattribs = explode('-p', $textattribs);

					foreach ($sirattribs as $a) {
						$aux = $a;
						if (strpos('x'.$aux, $prdctId.':') > 0) {
							$texta = str_replace($prdctId.':', '', $aux);
							break;
						}
					}

					$realprd = Tools::substr($prdctId, 0, strpos($prdctId, '06'));

					$product = new Product((int)$realprd);
					$product->cant = $qty_item;
					$product->textattribs = $texta;
					$product->prodcode = $prdctId;
					$prdcts[] = $product;
				}
		}

		$smarty->assign(array('prdcts' => $prdcts));

		///SEO URL
		if (Configuration::get('PS_REWRITING_SETTINGS') == 1)
		{
			$language = new Language($cookie->id_lang);
			if ($language->iso_code == 'en')
				$smarty->assign('asklink', 'en/ask-for-a-quote');
			if ($language->iso_code == 'fr')
				$smarty->assign('asklink', 'fr/demander-votre-devis');
		}
		else
			$smarty->assign('asklink', 'modules/askforaquote/frontoffice/askforaquote.php');
		///

		if ($cookie->logged)
			$smarty->assign('isLogged', '1');
		else
			$smarty->assign('isLogged', '0');

		return $this->display(__FILE__, 'ro_box.tpl');
	}

	public function hookHeader()
	{
		if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
		{
			Tools::addCSS(($this->_path).'css/ro_box.css', 'all');
			Tools::addJS(($this->_path).'js/jquery.scrollTo-1.4.2-min.js', 'all');
			Tools::addJS(($this->_path).'js/ajax.js', 'all');
			Tools::addJS(($this->_path).'js/js.js', 'all');
		}
		else
		{
			$this->context->controller->addCSS(($this->_path).'css/ro_box.css', 'all');
			$this->context->controller->addJS(($this->_path).'js/jquery.scrollTo-1.4.2-min.js', 'all');
			$this->context->controller->addJS(($this->_path).'js/ajax.js', 'all');
			$this->context->controller->addJS(($this->_path).'js/js.js', 'all');
		}
	}

	public function hookLeftColumn($params)
	{
		global $smarty, $cookie;
		return $this->hookRightColumn($params);
	}

	public function hookTop($params)
	{

		global $smarty, $cookie;

		$prdcts = array();
		if ($cookie->logged) {
			$res = DB::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'registered_requests WHERE id_customer='.$cookie->id_customer);

			$textattribs = isset($_COOKIE['rOatext']) ? $_COOKIE['rOatext'] : '';

			foreach ($res as $r) {
				$realprd = Tools::substr($r['id_product'], 0, strpos($r['id_product'], '06'));
				$sirattribs = explode('p', $textattribs);
				foreach ($sirattribs as $a) {
					if (strpos('x'.$a, $r['id_product'].':') > 0) {
						$texta = str_replace($r['id_product'].':', '', $a);
						break;
					}
				}

				$product = new Product((int)$realprd);
				$product->prodcode = $r['id_product'];
				$product->cant = $r['qty'];
				$product->textattribs = $texta;

				$prdcts[] = $product;
			}
		}
		if (empty($prdcts) || !$cookie->logged) {
			$c = isset($_COOKIE['rOp']) ? $_COOKIE['rOp'] : array();
			$qty = isset($_COOKIE['rOq']) ? $_COOKIE['rOq'] : '';
			$textattribs = isset($_COOKIE['rOatext']) ? $_COOKIE['rOatext'] : '';

			if (!empty($c))
				foreach ($c as $prdctId)
				{

					$poz = strpos($qty, '-'.$prdctId);
					$qty_item = Tools::substr($qty, $poz);
					$s = explode('-', $qty_item);
					$qty_item = (int)Tools::substr($s[1], strpos($s[1], '_') + 1);

					$sirattribs = explode('-p', $textattribs);

					foreach ($sirattribs as $a)
					{
						$aux = $a;
						if (strpos('x'.$aux, $prdctId.':') > 0)
						{
							$texta = str_replace($prdctId.':', '', $aux);
							break;
						}
					}

					$realprd = Tools::substr($prdctId, 0, strpos($prdctId, '06'));

					$product = new Product((int)$realprd);
					$product->cant = $qty_item;
					$product->textattribs = $texta;
					$product->prodcode = $prdctId;
					$prdcts[] = $product;
				}
		}

		/* we check if the quote box is present in the left or right column */
		if (Tools::substr(_PS_VERSION_, 0, 3) == '1.6')
		{
			$hookIdLeft = (int)Hook::get('displayLeftColumn');
			$hookIdRight = (int)Hook::get('displayRightColumn');
		}
		else
		{
			$hookIdLeft = (int)Hook::get('leftColumn');
			$hookIdRight = (int)Hook::get('rightColumn');
		}
		$hookIds = array($hookIdLeft, $hookIdRight);
		$module_instance = Module::getInstanceByName('askforaquote');

		foreach ($hookIds as $id)
		{
			if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
				$colmodexists = Hook::getModuleFromHook($id, $module_instance->id);
			else
				$colmodexists = Hook::getModulesFromHook($id, $module_instance->id);
		};

		if ($colmodexists)
			$smarty->assign('colmodexists', $colmodexists);
		/* checking done */

		$smarty->assign(array('prdcts' => $prdcts));
		$smarty->assign('psVersion', Tools::substr(_PS_VERSION_, 0, 3));

		/* SEO URL */
		if (Configuration::get('PS_REWRITING_SETTINGS') == 1)
		{
			$language = new Language($cookie->id_lang);
			if ($language->iso_code == 'en')
				$smarty->assign('asklink', 'en/ask-for-a-quote');
			if ($language->iso_code == 'fr')
				$smarty->assign('asklink', 'fr/demander-votre-devis');
		}
		else
			$smarty->assign('asklink', 'modules/askforaquote/frontoffice/askforaquote.php');
		/* */

		if ($cookie->logged)
			$smarty->assign('isLogged', '1');
		else
			$smarty->assign('isLogged', '0');

		return $this->display(__FILE__, 'ro_top_box.tpl');
	}

	public function hookextraRight($params)
	{
		global $smarty, $cookie;

		$smarty->assign('prodid', $_GET['id_product']);

		$proid = Tools::getValue('id_product');
		$pobj = new Product($proid, (int)$cookie->id_lang, true);
		$show = 1;
		$isQuickView = Tools::getValue('content_only');
		$treeline = $this->uptree($pobj->id_category_default);
		foreach ($treeline as $t)
		{
			if (!$this->isaskforquote($t)) {
				$show = 0;
				break;
			}
		}

		$grouppattribs = AttributeGroup::getAttributesGroups($cookie->id_lang);

		foreach ($grouppattribs as $ga)
		{
			if (($ga['is_color_group'] == 1) || (Tools::getIsset($ga['group_type']) && $ga['group_type'] == 'color'))
				$cologrupid = $ga['id_attribute_group'];
		}

		$smarty->assign('colorgroup', $cologrupid);

		$idCustomer = (($cookie->logged) ? (int)$cookie->id_customer : 0);
		$smarty->assign('customerid', $idCustomer);
		$prodlink = new Link;

		$smarty->assign('version', Tools::substr(_PS_VERSION_, 0, 3));
		$smarty->assign('quickView', Tools::getValue('content_only'));
		$smarty->assign('prodlink', $prodlink->getProductLink($pobj->id, $pobj->link_rewrite, $pobj->id_category_default));

		if (($show == 1))
			return $this->display(__FILE__, 'ro_product.tpl');
	}

	public function hookCustomerAccount($params)
	{
		global $smarty, $cookie;
		/* SEO URL */
		if (Configuration::get('PS_REWRITING_SETTINGS') == 1)
		{
			$language = new Language($cookie->id_lang);
			if ($language->iso_code == 'en')
				$smarty->assign('asklink', 'en/my-quotes');
			if ($language->iso_code == 'fr')
				$smarty->assign('asklink', 'fr/mes-devis');
		}
		else
			$smarty->assign('asklink', 'modules/askforaquote/frontoffice/myquotes.php');
		/* */

		return $this->display(__FILE__, 'my_account.tpl');
	}

	public function hookMyAccountBlock($params)
	{
		return $this->hookCustomerAccount($params);
	}
	
	// added by Khush
	public function hookDisplayMyAccountBlockfooter($params){
		return $this->hookCustomerAccount($params);
	}
	// added by Khush

	private function uptree($cat)
	{
		$treeline = array();
		$treeline[] = $cat;
		$obj = new Category($cat);
		$depth = $obj->level_depth;
		while ($depth > 1)
		{
			$cat = $obj->id_parent;
			$obj = new Category($cat);
			$depth = $obj->level_depth;
			$treeline[] = $cat;
		}

		return $treeline;
	}

	private function isinquote($cat)
	{
		$search = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."askforaquote_categories WHERE id_category='$cat'");
		if ($search)
			return true;
		else
			return false;
	}

	private function isaskforquote($cat)
	{
		$ac = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."askforaquote_categories WHERE id_category='$cat'");
		if ($ac['active'] == 1)
			return true;
		else
			return false;
	}

	private function generatetree($cat)
	{
		global $cookie, $tree;

		if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
			$minlev = 0;
		else
			$minlev = 1;

		$cobj = new Category($cat, $cookie->id_lang, true);
		$subcats = $cobj->getSubCategories($cookie->id_lang);
		$margin = (int)$cobj->level_depth * 20;
		$lines = '';
		if ($cat > 1)
		{
			$tree .= '<div style="margin-left:'.$margin.'px; margin-bottom:5px; ">';
			if (count($subcats) > 0)
				$tree .= '<img id="image'.$cat.'" src="../modules/askforaquote/img/plus.gif" onclick="showcats('.$cat.')" style="display:'.(($cobj->level_depth > $minlev) ? 'inline' : 'none').'"/>';
			else
				$tree .= '&nbsp;&nbsp;&nbsp;';
			$tree .= '<input type="checkbox" name="acat_'.$cat.'" id="acat_'.$cat.'" '.(($this->isaskforquote($cat)) ? 'checked="checked"' : '').'/>&nbsp;<span>'.$cobj->name.'</span></div>';
		}

		if (count($subcats) > 0)
		{
			$tree .= '<div id="sub'.$cat.'" style="display:'.(($cobj->level_depth > $minlev) ? 'none' : 'block').' ">';
			foreach ($subcats as $s)
				$this->generatetree($s['id_category']);
			$tree .= '</div>';
		}

		return $tree;
	}

}

?>
