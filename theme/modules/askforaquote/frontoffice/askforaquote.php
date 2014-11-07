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

$prdcts = array();
$errors = array();
$prices = array();
$path = '/';

if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4') $id_shop = 0;
  else $id_shop = (int)Context::getContext()->shop->id;
/* collect client data */
$email = ((isset($_POST['email'])) ? $_POST['email'] : '');
$customer_lastname = ((isset($_POST['customer_lastname'])) ? $_POST['customer_lastname'] : '');
$customer_firstname = ((isset($_POST['customer_firstname'])) ? $_POST['customer_firstname'] : '');
$company = ((isset($_POST['company'])) ? $_POST['company'] : '');
$address1 = ((isset($_POST['address1'])) ? $_POST['address1'] : '');
$address2 = ((isset($_POST['address2'])) ? $_POST['address2'] : '');
$postcode = ((isset($_POST['postcode'])) ? $_POST['postcode'] : '');
$city = ((isset($_POST['city'])) ? $_POST['city'] : '');
$phone = ((isset($_POST['phone'])) ? $_POST['phone'] : '');
$phone_mobile = ((isset($_POST['phone_mobile'])) ? $_POST['phone_mobile'] : '');
$country = ((isset($_POST['id_country'])) ? $_POST['id_country'] : '');
$state = ((isset($_POST['id_state'])) ? $_POST['id_state'] : '');
$infosupl = ((isset($_POST['other'])) ? $_POST['other'] : '');

if ($cookie->logged)
{
	$sodcus = new Customer($cookie->id_customer);
	$email = $sodcus->email;
	$customer_lastname = $sodcus->lastname;
	$customer_firstname = $sodcus->firstname;
}

if ($country != '')
{
	$countryobj = new Country($country, $cookie->id_lang, true);
	$countryname = $countryobj->name;
}
else $countryname = '';

if ($state != '')
{
	$stateobj = new State($state);
	$statename = $stateobj->name;
}
else $statename = '';

$data = '';

if ($email != '') $data.= 'Email: ' . $email . ';';
if ($customer_firstname != '') $data.= 'Firstname: ' . $customer_firstname . ';';
if ($customer_lastname != '') $data.= 'Lastname: ' . $customer_lastname . ';';
if ($company != '') $data.= 'Company: ' . $company . ';';
if ($address1 != '') $data.= 'Address: ' . $address1 . ';';
if ($address2 != '') $data.= 'Secondary address: ' . $address2 . ';';
if ($postcode != '') $data.= 'Postcode: ' . $postcode . ';';
if ($city != '') $data.= 'City: ' . $city . ';';
if ($statename != '') $data.= 'State: ' . $statename . ';';
if ($countryname != '') $data.= 'Country: ' . $countryname . ';';
if ($phone != '') $data.= 'Phone: ' . $phone . ';';
if ($phone_mobile != '') $data.= 'Mobile: ' . $phone_mobile . ';';
if ($infosupl != '') $data.= 'Additional information: ' . $infosupl . ';';
$smarty->assign('dataclientemail', $data);
/* ends collect client data */
$guestid = 0;
$submitasguest = 0;
/* submitguest */
$smarty->assign('guesterror', 0);

if (Tools::isSubmit('submitasguest'))
	{
	$submitasguest = 1;
	/* check if guest group exists */
	$guestgroup = DB::getInstance()->getRow("SELECT * FROM " . _DB_PREFIX_ . "group_lang WHERE name='Guest'");
	if ($guestgroup) $gpid = $guestgroup['id_group'];
	  else
		{
		$g = new Group();
		$languages = DB::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "lang");
		foreach($languages as $l) $g->name[$l['id_lang']] = 'Guest';
		$g->price_display_method = 1;
		$res = $g->add();
		$guestgroup = DB::getInstance()->getRow("SELECT * FROM " . _DB_PREFIX_ . "group_lang WHERE name='Guest'");
		if ($guestgroup) $gpid = $guestgroup['id_group'];
		}

	/* check if guest or customer and submit accordingly */
	$customerexists = DB::getInstance()->getRow("SELECT * FROM " . _DB_PREFIX_ . "customer WHERE email='" . $_POST['email'] . "'");
	if ($customerexists)
		{
		$guestid = $customerexists['id_customer'];
		$cus = new Customer($guestid);
		if ($cus->id_default_group == $gpid)
			{
			$cus->lastname = $_POST['customer_lastname'];
			$cus->firstname = $_POST['customer_firstname'];
			$password = $_POST['passwd'];
			$cus->passwd = Tools::encrypt($password);
			$cus->update();
			}
		  else
			{
			$smarty->assign('guesterror', 1);
			$submitasguest = 0;
			}
		}
	  else
		{
		$cus = new Customer();
		$cus->lastname = $_POST['customer_lastname'];
		$cus->firstname = $_POST['customer_firstname'];
		$cus->email = $_POST['email'];
		$password = $_POST['passwd'];
		$cus->passwd = Tools::encrypt($password);
		$cus->active = true;
		$cus->id_default_group = $gpid;
		$cus->add();
		$customerexists = DB::getInstance()->getRow("SELECT * FROM " . _DB_PREFIX_ . "customer WHERE email='" . $_POST['email'] . "'");
		$guestid = $customerexists['id_customer'];
		}
	}

/* end of submitguest */
/* check if logged or not. else statement in line ~265 */

if (($cookie->logged) || ($submitasguest == 1))
	{
	$c = isset($_COOKIE['rOp']) ? $_COOKIE['rOp'] : array();
	$qty = isset($_COOKIE['rOq']) ? $_COOKIE['rOq'] : '';
	if (!empty($c))
		{
		$id_customer = (($guestid == 0) ? (int)$cookie->id_customer : $guestid);
		$tm = date('Y-m-d H-i-s');
		foreach($c as $k => $id_product)
			{
			$i = isset($_COOKIE['rOi']) ? $_COOKIE['rOi'] : array();
			if (!empty($i))
				{
				$i2 = str_replace('\"', '"', $i);
				$datai = unserialize($i2);
				}

			if ($datai[$id_product] <> '') $ipaval = $datai[$id_product];
			  else $ipaval = 0;
			$poz = strpos($qty, '-' . $id_product);
			$qty_item = Tools::substr($qty, $poz);
			$s = explode('-', $qty_item);
			$qty_item = (int)Tools::substr($s[1], strpos($s[1], '_') + 1);
			DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests WHERE id_product =' . $id_product . ' and id_product_attribute = ' . $ipaval . ' and 
										id_shop = ' . $id_shop . ' and id_customer =' . $id_customer);
			DB::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'registered_requests(

			id_request, id_customer, id_product, id_shop, qty, date, id_product_attribute) VALUES(

			0, ' . $id_customer . ', ' . $id_product . ', ' . $id_shop . ', ' . $qty_item . ', "' . $tm . '", ' . $ipaval . ')');
			setcookie('rOp[' . $k . ']', '', 1, $path); /* unset after adding to db */
			if (!empty($i))
				{
				$i2 = unserialize($i);
				@$a2[$idprods] = array();
				}

			setcookie('rOi', serialize($i2) , 1, $path); /* 60*60*24 */
			}
		}

	setcookie('rOq', '', time() - 1728000, $path); /* 60*60*24 */
	$a = isset($_COOKIE['rOa']) ? $_COOKIE['rOa'] : array();
	if (!empty($a))
		{
		$a2 = str_replace('\"', '"', $a);
		$data2 = unserialize($a2);
		}
	  else $data2 = array();
	$rescustomer = (($guestid == 0) ? (int)$cookie->id_customer : $guestid);
	$res = DB::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'registered_requests WHERE id_customer=' . $rescustomer . ' order by id_request');
	$resmax = DB::getInstance()->ExecuteS('SELECT MAX(id_request) AS "id_request" FROM ' . _DB_PREFIX_ . 'registered_requests');
	$latest_id = $resmax[0]['id_request'];
	$id_customer = (($guestid == 0) ? (int)$cookie->id_customer : $guestid);
	/* we check if the user had ever submitted quotes or not. we need this for the automatic quote group numbering */
	$qres2 = DB::getInstance()->getRow("SELECT quotes FROM " . _DB_PREFIX_ . "customer WHERE id_customer=" . $id_customer . "");
	$group_nr = $qres2[quotes] + 1;
	$valori = '';
	foreach($res as $r)
		{
		$pa = $r['id_product_attribute'];
		$img_ids = DB::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute_image WHERE id_product_attribute=' . $r['id_product_attribute']);
		$img_query = 'SELECT pai.id_image FROM   ' . _DB_PREFIX_ . 'category_product cp LEFT JOIN ' . _DB_PREFIX_ . 'product p  ON p.`id_product` = cp.`id_product` LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON ( p.`id_product` = pa.`id_product` AND id_product_attribute = ' . $pa . ' ) INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_image pai ON ( pai.id_product_attribute = pa.`id_product_attribute`) WHERE  p.`active` = 1  ';
		$idprods = $r['id_product'];
		if (!empty($data2[$idprods]))
		foreach($data2[$idprods] as $idattr => $attrvalue)
			{
			DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests_attributes WHERE id_product =' . $idprods . ' and id_attribute = ' . $idattr . ' and 
										id_shop = ' . $id_shop . ' and id_customer =' . $id_customer . ' and value ="' . $attrvalue . '"');
			DB::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "registered_requests_attributes (id_request, id_customer, id_product, id_shop, qty, id_attribute, value) VALUES ('$latest_id', '$id_customer', '$idprods', '$id_shop', '" . $r['qty'] . "', '$idattr', '$attrvalue')");
			if (!empty($a))
				{
				$a2 = unserialize($a);
				$a2[$idprods] = array();
				}

			setcookie('rOa', serialize($a2) , time() - 1728000, $path); /* 60*60*24 */
			}

		$realprd = Tools::substr($r['id_product'], 0, strpos($r['id_product'], '06'));
		$product = getProducts((int)$realprd, $r['id_product'], $id_customer);
		$product['prodcode'] = $r['id_product'];
		$product['qty'] = $r['qty'];
		$prodob = new Product($realprd, $cookie->id_lang, true);
		$product['name'] = $prodob->name;
		$priceres = Product::getPriceStatic($realprd, true, null, 6);
		$prodprice = $priceres;
		foreach($product['attributes'] as $aa)
			{
			if (!empty($img_ids) && $img_ids[0]['id_image'] != 0) $product['id_image'] = $img_ids[0]['id_image'];
			}

		$product['link_rewrite'] = $prodob->link_rewrite;
		$prdcts[] = $product;
		$prices[] = $prodprice;
		}

	setcookie('rOc', $id_customer, time() + 1728000, $path); /* unset after adding to db */
	}
  else
	{
	$a = isset($_COOKIE['rOa']) ? $_COOKIE['rOa'] : array();
	$qty = isset($_COOKIE['rOq']) ? $_COOKIE['rOq'] : 0;
	$data2 = "";
	if (!empty($a))
		{
		$a2 = str_replace('\"', '"', $a);
		$data2 = unserialize($a2);
		}

	$i = isset($_COOKIE['rOi']) ? $_COOKIE['rOi'] : array();
	if (!empty($i))
		{
		$i2 = str_replace('\"', '"', $i);
		$datai = unserialize($i2);
		}

	$idcust = isset($_COOKIE['rOc']) ? $_COOKIE['rOc'] : array();
	if (!empty($idcust))
		{
		DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests WHERE id_customer = ' . $idcust . '');
		DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests_attributes WHERE id_customer = ' . $idcust . '');
		}

	$c = isset($_COOKIE['rOp']) ? $_COOKIE['rOp'] : array();
	if (!empty($c))
	foreach($c as $prdct_id)
		{
		$poz = strpos($qty, '-' . $prdct_id);
		$qty_item = Tools::substr($qty, $poz);
		$s = explode('-', $qty_item);
		$qty_item = (int)Tools::substr($s[1], strpos($s[1], '_') + 1);
		$realprd = Tools::substr($prdct_id, 0, strpos($prdct_id, '06'));
		$product = getProductsCookie((int)$realprd, $prdct_id);
		$product['prodcode'] = $prdct_id;
		$product['qty'] = $qty_item;
		$prodob = new Product($realprd, $cookie->id_lang, true);
		$product['name'] = $prodob->name;
		$product['link_rewrite'] = $prodob->link_rewrite;
		$pa = $r['id_product_attribute'];
		foreach($product['attributes'] as $aa)
			{
			$img_query = 'SELECT pai.id_image FROM   ' . _DB_PREFIX_ . 'category_product cp LEFT JOIN ' . _DB_PREFIX_ . 'product p  ON p.`id_product` = cp.`id_product` LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON ( p.`id_product` = pa.`id_product` AND id_product_attribute = ' . $datai[$prdct_id] . ' ) INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_image pai ON ( pai.id_product_attribute = pa.`id_product_attribute`) WHERE  p.`active` = 1  ';
			$img_ids = DB::getInstance()->ExecuteS($img_query);
			if (!empty($img_ids) && $img_ids[0]['id_image'] != 0) $product['id_image'] = $img_ids[0]['id_image'];
			}

		$prdcts[] = $product;
		}
	}

$years = Tools::dateYears();
$months = Tools::dateMonths();
$days = Tools::dateDays();
/* Load guest informations */

if ($cookie->logged && $cookie->is_guest) $smarty->assign('guestInformations', _getGuestInformations());
_processAddressFormat();
$res_set = DB::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'askforaquote_settings');

if (($cookie->logged) || ($submitasguest == 1)) $islogged = true;
  else $islogged = false;
$id_customer = (($guestid == 0) ? (int)$cookie->id_customer : $guestid);
$is_final = Tools::getValue('gofinal');

if (isset($is_final)) $gofinal = 1;
  else $gofinal = 0;
$smarty->assign(array(
	'isLogged' => $islogged,
	'gofinal' => (($is_final == 1) ? true : false) ,
	'simple_checkout' => (isset($res_set[0]['simple_checkout']) && $res_set[0]['simple_checkout'] == 1) ? 1 : 0,
	'terms' => (isset($res_set[0]['terms']) && $res_set[0]['terms'] == 1) ? 1 : 0,
	'guestcheckout' => (isset($res_set[0]['guest_checkout']) && $res_set[0]['guest_checkout'] == 1) ? 1 : 0,
	'sl_country' => isset($selectedCountry) ? $selectedCountry : 0,
	'countries' => Country::getCountries((int)($cookie->id_lang) , true) ,
	'PS_GUEST_CHECKOUT_ENABLED' => 0,
	'isGuest' => isset($cookie->is_guest) ? $cookie->is_guest : 0,
	'prdcts' => $prdcts,
	'tprice' => (array_sum($prices)) ,
	'customerid' => $id_customer,
	'prdctsattr' => isset($prdctsattr) ? $prdctsattr : array() ,
	'lastProductAdded' => (!empty($prdcts) ? $prdcts[count($prdcts) - 1] : '') ,
	'years' => $years,
	'months' => $months,
	'shopid' => $id_shop,
	'days' => $days,
	'groupNr' => $group_nr
));
$smarty->assign('newsletter', (int)Module::getInstanceByName('blocknewsletter')->active);
$smarty->assign('version', Tools::substr(_PS_VERSION_, 0, 3));
//$smarty->display('../views/templates/front/askforaquote.tpl');
$smarty->display(getcwd().'/../views/templates/front/askforaquote.tpl');

if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4') include ('../../../footer.php');
  else $controller->displayFooter();
?>