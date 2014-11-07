<?php
/**
 * Ask for a Quote module for PrestaShop
 *
 *  @author    Presta FABRIQUE - www.presta-shop-modules.com
 *  @copyright 2014 Presta FABRIQUE
 *  @license   Presta FABRIQUE
 */
class AdminQuotes extends AdminTab
{
    
	/** @var string The field we are sorting on */
    protected $_sortBy = 'date';
    private $_tabClass = 'AdminQuotes';
    private $_module = 'askforaquote';
    private $_modulePath = '';
    private $_html = '';
    private $_id_lang;
    private $_defaultLanguage;
    private $_iso;
    public function __construct()
    {
        $this->table                   = _DB_PREFIX_.'submitted_requests';
        $this->className               = 'Quotes';
        $this->displayName             = $this->l('Ask for a quote Module');
        $this->_list                   = true;
        $this->multishop_context_group = true;
        $this->multishop_context       = 1;
        $this->base_url                = Configuration::get('PS_SSL_ENABLED') ? preg_replace('/^http:/', 'https:', _PS_BASE_URL_) : _PS_BASE_URL_;
        $this->_modulePath             = _PS_MODULE_DIR_.$this->_module.'/backoffice/';
        $this->bootstrap               = true;
        parent::__construct();
    }
    
    protected function loadObject($opt = false)
    {
        if($id = Tools::getValue($this->identifier))
            return new $this->className($id);
        return new $this->className();
    }
    
    public function getContent()
    {
    }
    
    public function displayList()
    {
        global $cookie;
        if(Tools::substr(_PS_VERSION_, 0, 3) == '1.4')
            $id_shop = 0;
        else
            $id_shop = (int) Context::getContext()->shop->id;

        if(Tools::getValue('deletequote')) {
            
            $res = DB::getInstance()->ExecuteS("SELECT id_request FROM "._DB_PREFIX_."submitted_requests WHERE id_group=".(int) Tools::getValue('deletequote'));
            DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."submitted_requests_groups where id_group=".(int) Tools::getValue('deletequote'));
            DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."submitted_requests where id_group=".(int) Tools::getValue('deletequote'));
            
            foreach($res as $val)
                DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."submitted_requests_attributes where id_request=".(int) $val['id_request']);
        }
        
        // get guest group //
        $guestgroup = DB::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."group_lang WHERE name='Guest'");
        if($guestgroup)
            $gpid = $guestgroup['id_group'];
        else
            $gpid = 0;
        // //
        
        $currency = new Currency($cookie->id_lang);
        $currency = $currency->sign;
        
        // /addcomment
        $error  = 0;
        $succes = 0;
        if(Tools::isSubmit('submitbargain')) {
            $error         = 0;
            $succes        = 0;
            $id_product    = $_POST['product'];
            $id_request    = $_POST['request'];
            $currencywrite = $_POST['currency'];
            $comment       = mysql_escape_string($_POST['comment']);
            $price         = mysql_escape_string($_POST['price']);
            if(($comment == '') or ($price == '')) {
                echo "<center><h2 style='color:red'>".(Tools::displayError('All the fields must be completed'))."</h2></center>";
                $error = 1;
            }
            
            $user        = 0;
            $id_customer = Tools::getValue('id_customer');
            $results     = DB::getInstance()->ExecuteS("Select * FROM "._DB_PREFIX_."customer WHERE id_customer=".$id_customer);
            foreach($results as $row) {
                $email     = $row['email'];
                $firstname = $row['firstname'];
                $lastname  = $row['lastname'];
            }
            
            $data = array(
                '{submitter}' => 'admin',
                '{firstname}' => $firstname,
                '{lastname}' => $lastname,
                '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), (int) ($cookie->id_lang), 1),
                '{price}' => $price,
                '{currency}' => $currency,
                '{comment}' => $comment
            );
            if($error == 0) {
                /* notification email addresses */
                $email_list      = array();
                $main            = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."askforaquote_emails WHERE main_email is not null");
                $c_eids          = Db::getInstance()->ExecuteS("SELECT custom_emails FROM "._DB_PREFIX_."askforaquote_emails WHERE custom_emails is not null");
                $eids            = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."askforaquote_emails WHERE employee_ids is not null");
                $eids            = $eids[0]['employee_ids'];
                $employee_emails = Db::getInstance()->ExecuteS("SELECT email FROM "._DB_PREFIX_."employee WHERE id_employee IN (".$eids.")");
                if(!empty($main))
                    $email_list[] = Configuration::get('PS_SHOP_EMAIL');
                if(!empty($employee_emails)) {
                    foreach($employee_emails as $e)
                        $email_list[] = $e['email'];
                }
                
                if(!empty($c_eids)) {
                    $v = 1;
                    foreach($c_eids as $e)
                        $email_list[] = $e['custom_emails'];
                }
                /* email addresses end */
                
                $insertcom = DB::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."askforaquote_messages (id_request, id_product, id_shop, price, currency, comment, user, date_add) VALUES ('$id_request','$id_product','$id_shop','$price','$currencywrite','$comment','$user',NOW())");
                Mail::Send((int) ($cookie->id_lang), 'submitbargain', Mail::l('New bargain action'), $data, $email, null /* to name */ , null, $firstname.' '.$lastname, null /* attachment */ , null /* smtp */ , dirname(__FILE__).'/frontoffice/mails/', false /* die */ , null /* idshop */ , null /* bcc */ );
                foreach($email_list as $k => $v) {
                    Mail::Send((int) ($cookie->id_lang), 'submitbargain_confirm', Mail::l('Bargain confirmation'), $data, $v, null /* to name */ , null, $firstname.' '.$lastname, null /* attachment */ , null /* smtp */ , dirname(__FILE__).'/frontoffice/mails/', false /* die */ , null /* idshop */ , null /* bcc */ );
                }
                if(!$insertcom)
                    echo "<center><h2 style='color:red'>".Tools::displayError('Database error')."</h2></center>";
            }
        }
        $irow   = 0;
        $output = '<script type="text/javascript" src="'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/js/ajax.js"></script>
			<script type="text/javascript" src="'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/js/adminutils.js"></script>';
        if(Tools::substr(_PS_VERSION_, 0, 3) != '1.6')
            $output .= '<script type="text/javascript" src="'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/js/jquery-ui-1.10.4.custom.js"></script>';
        $output .= '<script type="text/javascript" src="'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/js/jquery.ui.draggable.min.js"></script>
            <script type="text/javascript">
                var ajax = new sack();
                function changeReqStatus(id_request){
                    var id="img_"+id_request;
                    var url="'.$this->base_url.__PS_BASE_URI__.'img/admin/";
                    document.getElementById(id).src= (document.getElementById(id).src == url+"module_install.png" ? url+"module_notinstall.png" : url+"module_install.png");

                    ajax.requestFile = "'.$this->base_url.__PS_BASE_URI__.'modules/askforaquote/ajax2.php?id_request="+id_request;
                    ajax.runAJAX();

                    return false;
                }
				$(document).ready(function() {
					if ($(\'#content\').hasClass(\'nobootstrap\')) {
						$(\'#content\').removeClass(\'nobootstrap\').addClass(\'bootstrap\');
					}
				});
            </script>
			<link href="'.__PS_BASE_URI__.'modules/askforaquote/css/adminstyle.css" rel="stylesheet" type="text/css" media="screen" />';
        
        if(Tools::substr(_PS_VERSION_, 0, 3) != '1.6')
            $output .= '<h2>'.$this->displayName.'</h2>';
        else
            $output .= '<link href="'.__PS_BASE_URI__.'modules/askforaquote/css/style16.css" rel="stylesheet" type="text/css" media="screen" />';
        
        // this part is displayed if we list a customer's quotes. else part is in line 489
        if($id_customer = Tools::getValue('id_customer')) {
            global $currentIndex, $cookie;
            $prdcts          = array();
            $customer        = new Customer((int) Tools::getValue('id_customer'));
            $configurations  = Configuration::getMultiple(array(
                'PS_LANG_DEFAULT',
                'PS_CURRENCY_DEFAULT'
            ));
            $defaultLanguage = (int) ($configurations['PS_LANG_DEFAULT']);
            $addresses       = $customer->getAddresses($defaultLanguage);
            $admindir        = substr($currentIndex, 0, strpos($currentIndex, 'index.php') - 1);
            $admindir        = explode('/', $admindir);
            $currentIndex    = $this->base_url.__PS_BASE_URI__.$admindir[count($admindir) - 1].'/index.php?tab=AdminCustomers';
            $customer        = new Customer($id_customer);
            
            // customer info block
            $output .= '<fieldset class="panel"><div class="panel-heading"><i class="icon-question-sign"></i> '.$this->l('Requests of ').$customer->firstname.' '.$customer->lastname.'</div>
						<button class="clear" onclick="window.history.back()">&laquo; '.$this->l('Back to request list').'</button><br />
						<h2>'.$this->l('Customer details').'</h2>
						<fieldset class="well list-detail col-md-6 customer_details"><div style="float: right"><a href="'.((Tools::substr(_PS_VERSION_, 0, 3) == 1.6) ? 'index.php?tab=AdminCustomers' : $currentIndex).'&addcustomer&id_customer='.$customer->id.'&token='.Tools::getAdminTokenLite('AdminCustomers').'&back='.urlencode($_SERVER["REQUEST_URI"]).'"><img src="../img/admin/edit.gif" /></a></div>
						<span style="font-weight: bold; font-size: 14px;">'.$customer->firstname.' '.$customer->lastname.'</span>
						<img src="../img/admin/'.($customer->id_gender == 2 ? 'female' : ($customer->id_gender == 1 ? 'male' : 'unknown')).'.gif" style="margin-bottom: 5px" /><br />
						<a href="mailto:'.$customer->email.'" style="text-decoration: underline; color: blue">'.$customer->email.'</a><br /><br /><br /> 						'.$this->l('ID:').' '.sprintf('%06d', $customer->id).'<br />';
            $output .= $this->l('Registration date:').' '.Tools::displayDate($customer->date_add, (int) ($cookie->id_lang), true).'<br />';
            $output .= $this->l('Last visit:').' '.((isset($customerStats) && $customerStats['last_visit']) ? Tools::displayDate($customerStats['last_visit'], (int) ($cookie->id_lang), true) : $this->l('never')).'<br />

					'.((isset($countBetterCustomers) && $countBetterCustomers != '-') ? $this->l('Rank: #').' '.(isset($countBetterCustomers) ? (int) $countBetterCustomers : '').'<br />' : '').'
				<br /></fieldset>
				<fieldset class="well list-detail col-md-5">
					<div style="float: right">
						<a href="'.((Tools::substr(_PS_VERSION_, 0, 3) == 1.6) ? 'index.php?tab=AdminCustomers' : $currentIndex).'&addcustomer&id_customer='.$customer->id.'&token='.Tools::getAdminTokenLite('AdminCustomers').'&back='.urlencode($_SERVER["REQUEST_URI"]).'"><img src="../img/admin/edit.gif" /></a>
					</div>
					'.$this->l('Newsletter:').' '.($customer->newsletter ? '<img src="../img/admin/enabled.gif" />' : '<img src="../img/admin/disabled.gif" />').'<br />
					'.$this->l('Opt-in:').' '.($customer->optin ? '<img src="../img/admin/enabled.gif" />' : '<img src="../img/admin/disabled.gif" />').'<br />
					'.$this->l('Age:').' '.(isset($customerStats) ? $customerStats['age'] : '').' '.((!empty($customer->birthday['age'])) ? '('.Tools::displayDate($customer->birthday, (int) ($cookie->id_lang)).')' : $this->l('unknown')).'<br /><br />
					'.$this->l('Last update:').' '.Tools::displayDate($customer->date_upd, (int) ($cookie->id_lang), true).'<br />
					'.$this->l('Status:').' '.($customer->active ? '<img src="../img/admin/enabled.gif" />' : '<img src="../img/admin/disabled.gif" />');
            if($customer->isGuest() or $customer->id_default_group == $gpid)
                $output .= '
					<br /><br /><div>
				  	  '.$this->l('This customer is registered as').' <b>'.$this->l('guest').'</b>
				  	  	</div></fieldset>
					';
            $output .= '
				</fieldset>
				<div class="clear">&nbsp;</div>';
            if($customer->id_default_group != $gpid) {
                $output .= '<h2>'.$this->l('Addresses').' ('.sizeof($addresses).')</h2>';
                if(sizeof($addresses)) {
                    $output .= '<table cellspacing="0" cellpadding="0" class="table">
						<tr>
							<th>'.$this->l('Company').'</th>
							<th>'.$this->l('Name').'</th>
							<th>'.$this->l('Address').'</th>
							<th>'.$this->l('Country').'</th>
							<th>'.$this->l('Phone number(s)').'</th>
							<th>'.$this->l('Actions').'</th>
						</tr>';
                    $tokenAddresses = Tools::getAdminToken('AdminAddresses'.(int) (Tab::getIdFromClassName('AdminAddresses')).(int) ($cookie->id_employee));
                    foreach($addresses AS $address)
                        $output .= '
						<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
							<td>'.($address['company'] ? $address['company'] : '--').'</td>
							<td>'.$address['firstname'].' '.$address['lastname'].'</td>
							<td>'.$address['address1'].($address['address2'] ? ' '.$address['address2'] : '').' '.$address['postcode'].' '.$address['city'].'</td>
							<td>'.$address['country'].'</td>
							<td>'.($address['phone'] ? ($address['phone'].($address['phone_mobile'] ? '<br />'.$address['phone_mobile'] : '')) : ($address['phone_mobile'] ? '<br />'.$address['phone_mobile'] : '--')).'</td>
							<td align="center">
								<a href="?tab=AdminAddresses&id_address='.$address['id_address'].'&addaddress&token='.$tokenAddresses.'&back='.urlencode($_SERVER["REQUEST_URI"]).'"><img src="../img/admin/edit.gif" /></a>
								<a href="?tab=AdminAddresses&id_address='.$address['id_address'].'&deleteaddress&token='.$tokenAddresses.'&back='.urlencode($_SERVER["REQUEST_URI"]).'"><img src="../img/admin/delete.gif" /></a>
							</td>
						</tr>';
                    $output .= '

			</table><div class="clear">&nbsp;</div>';
                } else
                    $output .= $customer->firstname.' '.$customer->lastname.' '.$this->l('has not registered any addresses yet').'.<div class="clear">&nbsp;</div>';
            }
            
            $req_b   = DB::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'askforaquote_settings');
            $bargain = $req_b[0]['enable_bargain'];
            $res     = DB::getInstance()->ExecuteS('SELECT p.id_product, p.name, pd.reference, p.link_rewrite, r.id_request, CONCAT(SUBSTR(r.date,1,12), REPLACE(SUBSTR(r.date,13,10),"-",":")) AS "date", r.status,rg.id_currency

				    FROM '._DB_PREFIX_.'submitted_requests r, '._DB_PREFIX_.'product_lang p , '._DB_PREFIX_.'product_attribute pd,'._DB_PREFIX_.'submitted_requests_groups rg

				    WHERE pd.id_product = p.id_product  AND rg.id_group = r.id_group

				    AND pd.id_product_attribute = r.id_product_attribute 

				    AND p.id_product=r.id_product 

				    AND p.id_lang='.$cookie->id_lang.' 

				    AND r.id_customer='.$id_customer.' 

				    GROUP BY r.id_request 

				    ORDER BY r.date ASC');
            
            //  $req = DB::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."submitted_requests WHERE id_customer='$id_customer' AND id_shop='$id_shop' ORDER BY date");
            
            $req    = DB::getInstance()->ExecuteS("SELECT *,srg.id_currency FROM "._DB_PREFIX_."submitted_requests sr left join "._DB_PREFIX_."submitted_requests_groups srg ON srg.id_group = sr.id_group WHERE sr.id_customer='$id_customer' ORDER BY sr.date ASC");
            $groups = DB::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."submitted_requests_groups WHERE id_customer='$id_customer'");
            $output .= '<h2>'.$this->l('Requests').'</h2>';
            
            // let's prepare requests for the groups
            
            $requests = array();
            foreach($req as $request) {
                $id_request            = $request['id_request'];
                $realprd               = substr($request['id_product'], 0, strpos($request['id_product'], '06'));
                $prouductquoted        = new Product($realprd, true, (int) $cookie->id_lang);
                $request['reference']  = $prouductquoted->reference;
                $request['name']       = $prouductquoted->name;
                $hour                  = substr($request['date'], 11);
                $request['date']       = substr($request['date'], 0, 11)." ".str_replace('-', ':', $hour);
                $requests[]            = $id_request;
                $sqlattr               = 'SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`

				FROM `'._DB_PREFIX_.'attribute_group` ag

				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int) $cookie->id_lang.')

				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int) $cookie->id_lang.')

				INNER JOIN `'._DB_PREFIX_.'submitted_requests_attributes` rra ON (rra.id_customer = '.$id_customer.' AND rra.`value` = a.`id_attribute` AND al.`id_lang` = '.(int) $cookie->id_lang.' AND rra.`id_request` = '.$id_request.') 

				ORDER BY agl.`name` ASC, al.`name` ASC';
                $r                     = Db::getInstance()->ExecuteS($sqlattr);
                $request['attributes'] = $r;
                $prdcts[]              = $request;
            }
            
            $requestsString = '('.implode(', ', $requests).')';
            
            // there may be products without attributes
            
            $res2 = DB::getInstance()->ExecuteS('SELECT p.id_product, p.name, p.link_rewrite, r.id_request, CONCAT(SUBSTR(r.date,1,12), REPLACE(SUBSTR(r.date,13,10),"-",":")) AS "date", r.status,rg.id_currency

							FROM '._DB_PREFIX_.'submitted_requests r, '._DB_PREFIX_.'product_lang p ,'._DB_PREFIX_.'submitted_requests_groups rg

							WHERE p.id_product=r.id_product AND rg.id_group = r.id_group

							AND p.id_lang='.$cookie->id_lang.' 

							AND r.id_customer='.$id_customer.' 

							AND r.id_request NOT IN '.$requestsString.'

							GROUP BY r.id_request 

							ORDER BY r.date ASC');
            if(!empty($res2)) {
                foreach($res2 as $request) {
                    $request['attributes'] = array();
                    $request['reference']  = '';
                    $prdcts[]              = $request;
                }
            }
            
            // begin quote groups foreach loop
            
            foreach($groups as $grp) {
                
                global $cookie;
                $newprice = array();
                $output .= '<div class="quotegroup">';
                $output .= '<div class="delete_quote"><a href="'.$_SERVER['REQUEST_URI'].'&deletequote='.$grp['id_group'].'"><img src="../img/admin/delete.gif" /> '.$this->l('Delete quote').'</a></div>';
                $output .= '<h4>'.$grp[gname].'<span class="date">'.$this->l('Submitted on').': '.$grp[date].'</span></h4>';
                $output .= '<div class="comment"><strong>'.$this->l('Comment').'</strong>: '.$grp[comment].'</div>';
                
                $output .= '<table class="quote_summary table" cellpadding="0" cellspacing="0">';
                $output .= '<thead><tr>';
                if($bargain == 1)
                    $output .= '<th class="first_item">'.$this->l('Status').'</th>';
                $output .= '<th>'.$this->l('Product').'</th><th class="lastprice">'.$this->l('Details').'</th>';
                if($bargain == 1) {
                    $output .= '<th class="bargaincol">'.(($customer->id_default_group == $gpid) ? '' : $this->l('Bargain')).'</th>';
                }
                ;
                $output .= '<th class="last_item">'.$this->l('Quantity').'</th></tr></thead><tbody>';
                foreach($prdcts as $r) {
                    if($grp[id_group] == $r[id_group]) {
                        $id_request = $r['id_request'];
                        $realprd    = substr($r['id_product'], 0, strpos($r['id_product'], '06'));
                        $sqllast    = DB::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."askforaquote_messages WHERE id_request='$id_request' ORDER BY date_add DESC");
                        if($sqllast) {
                            $lastprice        = $sqllast['price'];
                            $lastuser         = (($sqllast['user'] == 0) ? 'admin' : 'client');
                            $currencycustomer = $sqllast['currency'];
                        } else {
                            $realprd        = substr($r['id_product'], 0, strpos($r['id_product'], '06'));
                            $prouductquoted = new Product($realprd, true, $cookie->id_lang);
                            
                            // $lastprice = number_format($prouductquoted->getPrice(true, NULL), 2, '.', ' ');
                            // added by khush
                            
                            $lastprice   = Product::getPriceStatic($realprd, false, $r['id_product_attribute']);
                            $lastprice   = str_replace(' ', '', $lastprice);
                            $currency_to = new Currency((int) $grp['id_currency']);
                            if((int) Context::getContext()->currency->id != (int) $grp['id_currency']) {
                                $currency_from = new Currency((int) Context::getContext()->currency->id);
                                $lastprice     = Tools::convertPriceFull($lastprice, $currency_from, $currency_to);
                            }
                            
                            $curr_sign        = $currency_to->sign;
                            $currencycustomer = $currency_to->sign;
                            $currency         = $currency_to->sign;
                            
                            // end
                            
                        }
                        
                        $lastprice  = str_replace(' ', '', $lastprice);
                        $lastprice  = round($lastprice, 2);
                        $newprice[] = $lastprice;
                        $comments   = array();
                        $sqlcom     = DB::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."askforaquote_messages WHERE id_request='$id_request' ORDER BY date_add ASC");
                        foreach($sqlcom as $com)
                            $comments[] = $com;
                        $output .= '<tr>';
                        if($bargain == 1) {
                            $output .= '<td>

		

									<div id="details'.$r['id_request'].'" style="display:none;" class="hiddenforms detailsform">

									<a class="close" onclick="closeDetails(details'.$r['id_request'].')">'.$this->l('CLOSE').'</a>

									<h4>'.$this->l('Details for ').'<i>'.$r['name'].'</i></h4>

									<div style="background:#ccc"><fieldset><table border="0" width="100%">';
                            if(sizeof($comments)) {
                                foreach($comments as $com) {
                                    $output .= '<tr>

											<td align="left" style="border:none">

											'.(($com['user'] == 0) ? 'admin' : 'client').'

											</td>

											<td align="left" style="border:none">

											<p style="text-align:left">'.$com['comment'].'</p>

											</td>

											<td align="right" style="border:none">

											<p>'.$com['price'].' '.$com['currency'].'</p>

											</td>

											</tr>';
                                }
                            } else {
                                $output .= '<div class="empty">'.$this->l('There are no comments and bargains submitted').'</div>';
                            }
                            
                            $output .= '</table></fieldset></div></div>
									<div id="bargain'.$r['id_request'].'" style="display:none;" class="hiddenforms">
									<form method="post" class="std" id="bargaindevice'.$r['id_request'].'">
									<input type="hidden" name="request" id="request" value="'.$r['id_request'].'" />
									<input type="hidden" name="product" id="product" value="'.$realprd.'" />
									<input type="hidden" name="currency" id="currency" value="'.(($sqllast) ? $currencycustomer : $currency).'" />
									<fieldset>
									<a class="close" onclick="closeBargain(bargain'.$r['id_request'].')">'.$this->l('CLOSE').'</a>
									<h4>'.$this->l('Bargain for').'&nbsp;'.$r['name'].'</h4>
									<label>'.$this->l('Comment').'</label><br />
									<textarea name="comment" id="comment" cols="60" rows="5" maxlenght="20"></textarea><br />
									<label>'.$this->l('Price').' '.(($sqllast) ? $currencycustomer : $currency).'</label><br />
									<input type="text" name="price" id="price" maxlenght="5" style="height:20px;" value=""/>
									<input type="submit" name="submitbargain" id="submitbargain" value="'.$this->l('Save').'" class="button" />
									</fieldset>
									</form>
									</div>'.($r['status'] ? '<a href="#" onclick="return changeReqStatus('.$r['id_request'].');"><img id="img_'.$r['id_request'].'" src="../img/admin/module_install.png" /></a>' : '<a href="#" onclick="return changeReqStatus('.$r['id_request'].');"><img id="img_'.$r['id_request'].'" src="../img/admin/module_notinstall.png" /></a>').'</td>';
                        }
                        
                        $output .= '<td>'.$r['name'].'<br />';
                        foreach($r['attributes'] as $ratt)
                            $output .= $ratt['attribute_group'].' - '.$ratt['name'].' <br />';
                        
                        if(!empty($r['reference']))
                            $output .= $this->l('Reff').' : '.$r['reference'];
                        $output .= '</td>';
                        $output .= '<td>';
                        
                        if($sqllast)
                            $output .= $lastprice.$currency.' '.$this->l('by').' '.$lastuser;
                        else
                            $output .= (($lastprice > 0) ? $lastprice : '-').' '.$currency;
                        
                        if($bargain == 1)
                            $output .= ' '.$this->l('by admin').'<br /><a style="cursor:pointer" onclick="showDetails(details'.$r['id_request'].')">'.$this->l('Details').' ('.sizeof($comments).')</a>';
                        $output .= '</td>';
                        if($bargain == 1)
                            $output .= '<td><input type="button" id="bargain" name="bargain" value="'.$this->l('Bargain').'" onclick="showBargain(bargain'.$r['id_request'].')" class="button_small"style="visibility:'.(($customer->id_default_group == $gpid) ? 'hidden' : 'visible').'"/></td>';
                        
                        $output .= '<td>'.$r['qty'].'</td></tr>';
                    }
                }
                
                $output .= '</tbody>';
                $output .= '<tfoot>';
                $output .= '<tr><td class="footer_price" colspan="'.(($bargain == 1) ? '3' : '2').'">'.$this->l('Total products').'</td><td class="price" colspan="2">'.round($grp[original_price], 2).$currency.'</td></tr>';
                if($bargain == 1)
                    $output .= '<tr><td class="footer_price" colspan="'.(($bargain == 1) ? '3' : '2').'">'.$this->l('Bargained price').'</td><td class="price" colspan="2">'.round(array_sum($newprice), 2).$currency.'</td></tr>';
                $output .= '</tfoot>';
                $output .= '</table>';
                $output .= '</div>'; // quotegroup div end
            } // end groups foreach loop
            $output .= '</fieldset>';
			
        } else { //this is displayed when we access the tab. begins in line 171

            $oBy  = Tools::getValue('orderBy') ? Tools::getValue('orderBy') : '';
            $oWay = Tools::getValue('orderWay') ? Tools::getValue('orderWay') : '';
            $res  = DB::getInstance()->ExecuteS('SELECT CONCAT(SUBSTR(c.firstname,1,1),". ",c.lastname ) as "customer", COUNT(r.id_request) as "nr", CONCAT(SUBSTR(MAX(r.date),1,12), REPLACE(SUBSTR(MAX(r.date),13,10),"-",":")) AS "date", r.id_request, r.id_customer FROM '._DB_PREFIX_.'submitted_requests r, '._DB_PREFIX_.'customer c WHERE c.id_customer=r.id_customer AND r.id_shop='.$id_shop.' GROUP BY r.id_customer'.(($oBy && $oWay) ? ' ORDER BY '.$oBy.' '.$oWay : ''));
            
            // if we have quotes we list the users
            if(!empty($res)) {
                $output .= '<fieldset class="panel"><div class="panel-heading"><i class="icon-question-sign"></i> '.$this->l('Quotes').'</div><table class="table" cellpadding=0 cellspacing=0 border="0" width="80%" style="margin:auto">';
                $output .= '<tr>
		                           <th>'.$this->l('New').'</th>
		                           <th>'.$this->l('Customer').'&nbsp;'.(($oBy == 'customer' && $oWay == 'asc') ? '<img src="../img/admin/up_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=customer&orderWay=asc"><img src="../img/admin/up.gif"></a>').'&nbsp;'.(($oBy == 'customer' && $oWay == 'desc') ? '<img src="../img/admin/down_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=customer&orderWay=desc"><img src="../img/admin/down.gif"></a>').'</th>
		                           <th>'.$this->l('Number of requests').'&nbsp;'.(($oBy == 'nr' && $oWay == 'asc') ? '<img src="../img/admin/up_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=nr&orderWay=asc"><img src="../img/admin/up.gif"></a>').'&nbsp;'.(($oBy == 'nr' && $oWay == 'desc') ? '<img src="../img/admin/down_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=nr&orderWay=desc"><img src="../img/admin/down.gif"></a>').'</th>
		                           <th>'.$this->l('Date of last request').'&nbsp;'.(($oBy == 'date' && $oWay == 'asc') ? '<img src="../img/admin/up_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=date&orderWay=asc"><img src="../img/admin/up.gif"></a>').'&nbsp;'.(($oBy == 'date' && $oWay == 'desc') ? '<img src="../img/admin/down_d.gif">' : '<a href="'.$_SERVER['REQUEST_URI'].'&orderBy=date&orderWay=desc"><img src="../img/admin/down.gif"></a>').'</th></tr>';
                foreach($res as $r) {
                    $subres2 = DB::getInstance()->ExecuteS('SELECT id_request FROM '._DB_PREFIX_.'submitted_requests WHERE id_customer='.$r['id_customer'].' AND status !=1');
                    $output .= '<tr onclick="location.href=\''.$_SERVER['REQUEST_URI'].'&id_customer='.$r['id_customer'].'\'">

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
		                                <td>
		                                    <a href='.$_SERVER['REQUEST_URI'].'&id_customer='.$r['id_customer'].'>'.$this->l('List quotes').'</a>
		                                </td>

		                            </tr>';
                }
                
                $output .= '</table></fieldset>';
            } else // we don't have any quotes added yet
                $output = '<fieldset class="panel"><div class="panel-heading"><i class="icon-question-sign"></i> '.$this->l('Quotes').'</div><p>'.$this->l('No quotes were submitted yet.').'</p></fieldset>';
        }
        
        echo $output;
    }
}
