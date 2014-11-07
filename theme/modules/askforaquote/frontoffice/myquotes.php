<?php
/**
 * Ask for a Quote module for PrestaShop
 *
 *  @author    Presta FABRIQUE - www.presta-shop-modules.com
 *  @copyright 2014 Presta FABRIQUE
 *  @license   Presta FABRIQUE
 */

if (substr('_PS_VERSION_', 0, 3) == '1.6')
{
	require_once (dirname(__FILE__) . '/../../../config/config.inc.php');
	require_once (dirname(__FILE__) . '/../../../init.php');
	require_once (dirname(__FILE__) . '../functions.php');
}
else
{
	include ('../../../config/config.inc.php');
	include ('../../../init.php');
	include ('../functions.php');
}

if (Tools::substr('_PS_VERSION_', 0, 3) == '1.4') include ('../../../header.php');
else
{
	$controller = new FrontController();
	$controller->init();
	$controller->setMedia();
	// fix for 1.6.0.8
	if(file_exists(_PS_THEME_DIR_.'global.tpl'))
	{
		Context::getContext()->smarty->fetch(_PS_THEME_DIR_.'global.tpl');
		Context::getContext()->smarty->assign('js_defer' , (bool)Configuration::get('PS_JS_DEFER'));
	}	
	// End
	$controller->displayHeader();
}

if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4') $id_shop = 0;
else $id_shop = (int)Context::getContext()->shop->id;

$lang = $cookie->id_lang;
$smarty->assign('error', 0);
$smarty->assign('succes', 0);

if (Tools::isSubmit('submitbargain'))
	{
	$error = 0;
	$id_product = $_POST['product'];
	$id_request = $_POST['request'];
	$currency = $_POST['currency'];
	$id_qcustomer = $_POST['customerid'];
	$comment = mysql_escape_string($_POST['comment']);
	$price = mysql_escape_string($_POST['price']);
	if (($comment == '') or ($price == '')) $error = 1;
	$user = $cookie->id_customer;
	$results = DB::getInstance()->ExecuteS("Select * FROM " . _DB_PREFIX_ . "customer WHERE id_customer=" . $user);
	foreach($results as $row)
		{
		$email = $row['email'];
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		}

	$data = array(
		'{submitter}' => $firstname . ' ' . $lastname,
		'{firstname}' => $firstname,
		'{lastname}' => $lastname,
		'{date}' => Tools::displayDate(date('Y-m-d H:i:s') , (int)($cookie->id_lang) , 1) ,
		'{price}' => $price,
		'{currency}' => $currency,
		'{comment}' => $comment
	);
	if ($error == 0)
		{
		/* notification email addresses */
		$email_list = array();
		$main = Db::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "askforaquote_emails WHERE main_email is not null");
		$c_eids = Db::getInstance()->ExecuteS("SELECT custom_emails FROM " . _DB_PREFIX_ . "askforaquote_emails WHERE custom_emails is not null");
		$eids = Db::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "askforaquote_emails WHERE employee_ids is not null");
		$eids = $eids[0]['employee_ids'];
		$employee_emails = Db::getInstance()->ExecuteS("SELECT email FROM " . _DB_PREFIX_ . "employee WHERE id_employee IN (" . $eids . ")");
		if (!empty($main)) $email_list[] = Configuration::get('PS_SHOP_EMAIL');
		if (!empty($employee_emails))
			{
			foreach($employee_emails as $e)
				{
				$email_list[] = $e['email'];
				}
			}

		if (!empty($c_eids))
			{
			$v = 1;
			foreach($c_eids as $e)
				{
				$email_list[] = $e['custom_emails'];
				}
			}

		/* emai laddresses end */
		$insertcom = DB::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "askforaquote_messages (id_request, id_product, id_shop, price, currency, comment, user, date_add) VALUES ('$id_request','$id_product','$id_shop','$price','$currency','$comment','$user',NOW())");
		foreach($email_list as $k => $v)
			{
			Mail::Send((int)($cookie->id_lang) , 'submitbargain', Mail::l('New bargain action') , $data, $v, null /* to name */
			, null, $firstname . ' ' . $lastname, null /* attachment */
			, null /* smtp */
			, dirname(__FILE__) . '/mails/', false /* die */
			, null /* idshop */
			, null /* bcc */);
			}

		Mail::Send((int)($cookie->id_lang) , 'submitbargain_confirm', Mail::l('Bargain confirmation') , $data, $email, null /* to name */
		, null, $firstname . ' ' . $lastname, null /* attachment */
		, null /* smtp */
		, dirname(__FILE__) . '/mails/', false /* die */
		, null /* idshop */
		, null /* bcc */);
		if ($insertcom)
			{
			$smarty->assign('succes', 1);
			}
		  else
			{
			$smarty->assign('error', 2);
			}
		}
	  else $smarty->assign('error', $error);
	}

$lastprices = array();
$last = array();
$prdcts = array();
$comments = array();
$errors = array();

if (!$cookie->logged) Tools::redirect('index.php');
$res = DB::getInstance()->ExecuteS('SELECT p.id_product, p.name, pd.reference, p.link_rewrite, r.id_request, CONCAT(SUBSTR(r.date,1,12), REPLACE(SUBSTR(r.date,13,10),"-",":")) AS "date", r.qty, r.status,rg.id_currency

				    FROM ' . _DB_PREFIX_ . 'submitted_requests r, ' . _DB_PREFIX_ . 'product_lang p , ' . _DB_PREFIX_ . 'product_attribute pd,' . _DB_PREFIX_ . 'submitted_requests_groups rg

				    WHERE pd.id_product = p.id_product   AND rg.id_group = r.id_group

				    AND pd.id_product_attribute = r.id_product_attribute 

				    AND p.id_product=r.id_product 

				    AND p.id_lang=' . $cookie->id_lang . ' 

				    AND r.id_customer=' . $cookie->id_customer . ' 

				    GROUP BY r.id_request 

				    ORDER BY r.date ASC');
$id_customer = $cookie->id_customer;
$lang = $cookie->id_lang;
$req = DB::getInstance()->ExecuteS("SELECT *,srg.id_currency FROM " . _DB_PREFIX_ . "submitted_requests sr left join " . _DB_PREFIX_ . "submitted_requests_groups srg ON srg.id_group = sr.id_group WHERE sr.id_customer='$id_customer' ORDER BY sr.date ASC");
$groups = DB::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "submitted_requests_groups WHERE id_customer='$id_customer'");
$requests = array();

foreach($req as $request)
	{
	$id_request = $request['id_request'];
	$realprd = Tools::substr($request['id_product'], 0, strpos($request['id_product'], '06'));
	$request['real_id'] = $realprd;
	$id_product = $realprd;
	$prouductquoted = new Product($realprd, true, (int)$cookie->id_lang);
	$request['reference'] = $prouductquoted->reference;
	$request['name'] = $prouductquoted->name;
	$hour = substr($request['date'], 11);
	$request['date'] = substr($request['date'], 0, 11) . "<br/>(" . str_replace('-', ':', $hour) . ")";
	$requests[] = $id_request;

	// echo $id_request;
	// $sqlqty=DB::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."submitted_requests WHERE id_request='3'");

	$sqlattr = 'SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`

		FROM `' . _DB_PREFIX_ . 'attribute_group` ag

		LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)$cookie->id_lang . ')

		LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

		LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)$cookie->id_lang . ')

		INNER JOIN `' . _DB_PREFIX_ . 'submitted_requests_attributes` rra ON (rra.id_customer = ' . $cookie->id_customer . ' AND rra.`value` = a.`id_attribute` AND al.`id_lang` = ' . (int)$cookie->id_lang . ' AND rra.`id_request` = ' . $id_request . ') 

		ORDER BY agl.`name` ASC, al.`name` ASC';
	$r = Db::getInstance()->ExecuteS($sqlattr);
	$request['attributes'] = $r;

	// check for comments

	$sqlcomment = Db::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "askforaquote_messages WHERE id_request='$id_request' AND id_product='$id_product' ORDER BY date_add ASC");
	if ($sqlcomment)
		{
		$request['comments'] = 1;
		$request['currency'] = $sqlcomment[0]['currency'];
		}
	  else
		{
		$request['comments'] = 0;

		// $request['lastprice'] = number_format($prouductquoted->getPrice(true, NULL), 2, '.', ' ');
		// added by khush

		$request['lastprice'] = number_format(Product::getPriceStatic($id_product, false, $request['id_product_attribute']) , 2, '.', ' ');

		// end

		if (!$request['lastprice']) $request['lastprice'] = '-';
		$lastuser = 'admin';
		$smarty->assign('lastuser', $lastuser);
		}

	// added by khush

	$request['lastprice'] = str_replace(' ', '', $request['lastprice']);

	$currency_to = new Currency((int)$request['id_currency']);
	if ((int)Context::getContext()->currency->id != (int)$request['id_currency'])
		{
		$currency_from = new Currency((int)Context::getContext()->currency->id);
		$request['lastprice'] = Tools::convertPriceFull($request['lastprice'], $currency_from, $currency_to);
		}

	$request['curr_sign'] = $currency_to->sign;

	// end

	$prdcts[] = $request;

	// collecting comments

	if ($sqlcomment)
		{
		foreach($sqlcomment as $com)
			{
			array_push($comments, $com);
			}
		}

	if ($sqlcomment)
		{
		foreach($sqlcomment as $com)
			{
			$key = $com['id_request'];
			$last[$key]['lastprice'] = $com['price'];
			$last[$key]['lastuser'] = (($com['user'] == $cookie->id_customer) ? 'me' : 'admin');
			}
		}
	}

$requestsString = '(' . implode(', ', $requests) . ')';

// there may be products without attributes

$res2 = DB::getInstance()->ExecuteS('SELECT p.id_product, p.name, p.link_rewrite, r.id_request, CONCAT(SUBSTR(r.date,1,12), REPLACE(SUBSTR(r.date,13,10),"-",":")) AS "date", r.status,rg.id_currency

				    FROM ' . _DB_PREFIX_ . 'submitted_requests r,' . _DB_PREFIX_ . 'submitted_requests_groups rg, ' . _DB_PREFIX_ . 'product_lang p 

				    WHERE p.id_product=r.id_product AND rg.id_group = r.id_group

				    AND p.id_lang=' . $cookie->id_lang . ' 

				    AND r.id_customer=' . $cookie->id_customer . ' 

				    ' . ($requestsString != '()' ? 'AND r.id_request NOT IN ' . $requestsString . ' ' : ' ') . '

				    GROUP BY r.id_request 

				    ORDER BY r.date ASC');

if (!empty($res2))
	{
	foreach($res2 as $request)
		{
		$request['attributes'] = array();
		$realprd = Tools::substr($request['id_product'], 0, Tools::strpos($request['id_product'], '06'));
		$prouductquoted = new Product($realprd, true, $lang);
		$request['reference'] = $prouductquoted->reference;

		// added by khush

		$currency_to = new Currency((int)$request['id_currency']);
		$request['lastprice'] = str_replace(' ', '', $request['lastprice']);
		if ((int)Context::getContext()->currency->id != (int)$request['id_currency'])
			{
			$currency_from = new Currency((int)Context::getContext()->currency->id);
			$request['lastprice'] = Tools::convertPriceFull($request['lastprice'], $currency_from, $currency_to);
			}

		$request['curr_sign'] = $currency_to->sign;

		// end

		$prdcts[] = $request;
		}
	}

// echo '<pre>';print_r($prdcts);echo'</pre>';

$curency = new Currency($cookie->id_currency);
$res3 = DB::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'askforaquote_settings');
$smarty->assign(array(
	'quotes' => $prdcts,
	'groups' => $groups,
	'comments' => $comments,
	'currency' => $curency->sign,
	'last' => $last,
	'lastprices' => $lastprices,
	'current_user' => $cookie->id_customer,
	'enable_bargain' => $res3[0]['enable_bargain']
));
$smarty->assign('version', Tools::substr(_PS_VERSION_, 0, 3));
//$smarty->display('../views/templates/front/myquotes.tpl');
$smarty->display(getcwd().'/../views/templates/front/myquotes.tpl');

if (substr(_PS_VERSION_, 0, 3) == '1.4')
	include ('../../../footer.php');
else
	$controller->displayFooter();

?>