<?php
/**
 * Ask for a Quote module for PrestaShop
 *
 *  @author    Presta FABRIQUE - www.presta-shop-modules.com
 *  @copyright 2014 Presta FABRIQUE
 *  @license   Presta FABRIQUE
 */

function getDomain()
	{
	$r = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';
	preg_match($r, Tools::getHttpHost(false, false) , $out);
	if (preg_match('/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)'.'{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)'.'{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/', $out[4])) return false;
	if (!strstr(Tools::getHttpHost(false, false) , '.')) return false;
	$domain = $out[4];
	$sub_domains = SubDomain::getSubDomains();
	if ($sub_domains === false) die(Tools::displayError('Bad SubDomain SQL query.'));
	foreach($sub_domains as $sub_domain)
		{
		$subDomainLength = Tools::strlen($sub_domain) + 1;
		if (strncmp($sub_domain.'.', $domain, $subDomainLength) == 0) $domain = Tools::substr($domain, $subDomainLength);
		}

	return $domain;
	}

function _getGuestInformations()
	{
	global $cookie, $cart;
	$customer = new Customer((int)$cookie->id_customer);
	$address_delivery = new Address((int)$cart->id_address_delivery);
	if ($customer->birthday) $birthday = explode('-', $customer->birthday);
	  else $birthday = array(
		'0',
		'0',
		'0'
	);
	return array(
		'id_customer' => (int)$cookie->id_customer,
		'email' => Tools::htmlentitiesUTF8($customer->email) ,
		'customer_lastname' => Tools::htmlentitiesUTF8($customer->lastname) ,
		'customer_firstname' => Tools::htmlentitiesUTF8($customer->firstname) ,
		'newsletter' => (int)$customer->newsletter,
		'optin' => (int)$customer->optin,
		'id_address_delivery' => (int)$cart->id_address_delivery,
		'company' => Tools::htmlentitiesUTF8($address_delivery->company) ,
		'lastname' => Tools::htmlentitiesUTF8($address_delivery->lastname) ,
		'firstname' => Tools::htmlentitiesUTF8($address_delivery->firstname) ,
		'vat_number' => Tools::htmlentitiesUTF8($address_delivery->vat_number) ,
		'dni' => Tools::htmlentitiesUTF8($address_delivery->dni) ,
		'address1' => Tools::htmlentitiesUTF8($address_delivery->address1) ,
		'postcode' => Tools::htmlentitiesUTF8($address_delivery->postcode) ,
		'city' => Tools::htmlentitiesUTF8($address_delivery->city) ,
		'phone' => Tools::htmlentitiesUTF8($address_delivery->phone) ,
		'phone_mobile' => Tools::htmlentitiesUTF8($address_delivery->phone_mobile) ,
		'id_country' => (int)$address_delivery->id_country,
		'id_state' => (int)$address_delivery->id_state,
		'id_gender' => (int)$customer->id_gender,
		'sl_year' => $birthday[0],
		'sl_month' => $birthday[1],
		'sl_day' => $birthday[2]
	);
	}

function _processAddressFormat()
	{
	global $cookie, $cart, $smarty;
	$selectedCountry = (int)Configuration::get('PS_COUNTRY_DEFAULT');
	$address_delivery = new Address((int)$cart->id_address_delivery);
	$address_invoice = new Address((int)$cart->id_address_invoice);
	$inv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_delivery->id_country);
	$dlv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_invoice->id_country);
	$inv_all_fields = array();
	$dlv_all_fields = array();
	foreach(array(
		'inv',
		'dlv'
	) as $adr_type)
		{
		foreach($
			{
			$adr_type.'_adr_fields'
			} as $fields_line)
		foreach(explode(' ', $fields_line) as $field_item) $
			{
			$adr_type.'_all_fields'
			}

		[] = trim($field_item);
		$smarty->assign($adr_type.'_adr_fields', $
			{
			$adr_type.'_adr_fields'
			});
		$smarty->assign($adr_type.'_all_fields', $
			{
			$adr_type.'_all_fields'
			});
		}
	}

function getProductsCookie($id_product, $id_product_code)
	{
	global $cookie;
	$prdcts = array();
	$sql = 'SELECT p.id_product, pl.name, p.reference, pl.description_short, pl.description, i.id_image, il.legend, pl.link_rewrite

		FROM '._DB_PREFIX_.'product p, '._DB_PREFIX_.'product_lang pl, '._DB_PREFIX_.'image i, '._DB_PREFIX_.'image_lang il

		WHERE p.id_product = pl.id_product

		AND pl.id_product = i.id_product

		AND i.id_image = il.id_image

		AND pl.id_lang= '.(int)$cookie->id_lang.'

		AND il.id_lang= '.(int)$cookie->id_lang.'

		AND p.id_product = '.(int)$id_product;
	$result = Db::getInstance()->ExecuteS($sql);
	if (!$result)
		{
		$sql = "SELECT p.id_product, pl.name, p.reference, pl.description_short, pl.description, pl.link_rewrite FROM "._DB_PREFIX_."product p, "._DB_PREFIX_."product_lang pl WHERE p.id_product = pl.id_product AND p.id_product=".(int)$id_product." AND pl.id_lang=".(int)$cookie->id_lang;
		$result = Db::getInstance()->ExecuteS($sql);
		}

	$productsIds = array();
	foreach($result as $row) $productsIds[] = $row['id_product'];
	Product::cacheProductsFeatures($productsIds);
	if (empty($result)) return array();
	$attr = Attribute::getAttributes($cookie->id_lang);
	$i = isset($_COOKIE['rOi']) ? $_COOKIE['rOi'] : array();
	if (!empty($i))
		{
		$i2 = str_replace('\"', '"', $i);
		$datai = unserialize($i2);
		}

	$prodid = $datai[$id_product_code];
	$resultref = Db::getInstance()->ExecuteS("SELECT reference FROM "._DB_PREFIX_."product_attribute pa WHERE pa.id_product_attribute = '$prodid'");
	foreach($result as $k => $row)
		{
		foreach($resultref as $kk => $rowreff)
			{
			if ($rowreff['reference'] <> '') $row['reference'] = $rowreff['reference'];
			}

		$row['attributes'] = getProductsAttrCookie((int)$row['id_product'], (int)$id_product_code);
		$row['id_image'] = Product::defineProductImage($row, $cookie->id_lang);
		$row['features'] = Product::getFeaturesStatic((int)$row['id_product']);
		$prdcts[] = $row;
		break;
		}

	return $prdcts[0];
	}

function getProducts($id_product, $id_product_code, $customerid)
	{
	global $cookie;
	$prdcts = array();
	$sql = 'SELECT p.id_product, pl.name, p.reference, pl.description_short, pl.description, i.id_image, il.legend, pl.link_rewrite

		FROM '._DB_PREFIX_.'product p, '._DB_PREFIX_.'product_lang pl, '._DB_PREFIX_.'image i, '._DB_PREFIX_.'image_lang il

		WHERE p.id_product = pl.id_product

		AND pl.id_product = i.id_product

		AND i.id_image = il.id_image

		AND pl.id_lang= '.(int)$cookie->id_lang.'

		AND il.id_lang= '.(int)$cookie->id_lang.'

		AND p.id_product = '.(int)$id_product;
	$result = Db::getInstance()->ExecuteS($sql);
	if (!$result)
		{
		$sql = "SELECT p.id_product, pl.name, p.reference, pl.description_short, pl.description, pl.link_rewrite FROM "._DB_PREFIX_."product p, "._DB_PREFIX_."product_lang pl WHERE p.id_product = pl.id_product AND p.id_product=".(int)$id_product." AND pl.id_lang=".(int)$cookie->id_lang;
		$result = Db::getInstance()->ExecuteS($sql);
		}

	$productsIds = array();
	foreach($result as $row) $productsIds[] = $row['id_product'];
	Product::cacheProductsFeatures($productsIds);
	if (empty($result)) return array();
	$attr = Attribute::getAttributes($cookie->id_lang);
	$resultref = Db::getInstance()->ExecuteS('SELECT reference FROM '._DB_PREFIX_.'product_attribute pa

		WHERE pa.id_product_attribute = (SELECT id_product_attribute FROM '._DB_PREFIX_.'registered_requests WHERE id_customer ='.$customerid.' AND id_product = '.(int)$id_product_code.' )');
	foreach($result as $k => $row)
		{
		foreach($resultref as $kk => $rowreff)
			{
			if ($rowreff['reference'] <> '') $row['reference'] = $rowreff['reference'];
			}

		$row['attributes'] = getProductsAttr((int)$row['id_product'], (int)$id_product_code);
		$row['id_image'] = Product::defineProductImage($row, $cookie->id_lang);
		$row['features'] = Product::getFeaturesStatic((int)$row['id_product']);
		$prdcts[] = $row;
		break;
		}

	return $prdcts[0];
	}

function getProductsMail($id_product, $id_product_code, $customerid)
	{
	global $cookie;
	$prdcts = array();
	$sql = 'SELECT p.id_product, pl.name, p.reference, pl.description_short, pl.description, i.id_image, il.legend, pl.link_rewrite

		FROM '._DB_PREFIX_.'product p, '._DB_PREFIX_.'product_lang pl, '._DB_PREFIX_.'image i, '._DB_PREFIX_.'image_lang il

		WHERE p.id_product = pl.id_product

		AND pl.id_product = i.id_product

		AND i.id_image = il.id_image

		AND pl.id_lang= '.(int)$cookie->id_lang.'

		AND il.id_lang= '.(int)$cookie->id_lang.'

		AND p.id_product = '.(int)$id_product;
	$result = Db::getInstance()->ExecuteS($sql);
	$productsIds = array();
	foreach($result as $row) $productsIds[] = $row['id_product'];
	Product::cacheProductsFeatures($productsIds);
	if (empty($result)) return array();
	$attr = Attribute::getAttributes($cookie->id_lang);
	foreach($result as $k => $row)
		{
		$row['attributes'] = getProductsAttrMail((int)$row['id_product'], (int)$id_product_code, $customerid);
		$row['id_image'] = Product::defineProductImage($row, $cookie->id_lang);
		$row['features'] = Product::getFeaturesStatic((int)$row['id_product']);
		$prdcts[] = $row;
		break;
		}

	return $prdcts[0];
	}

function getProductsAttr($id_product, $id_product_code)
	{
	global $cookie;
	$prdctsattr = array();
	$sqlattr2 = '

		SELECT al.`name`, agl.`name` as `attribute_group`

		FROM `'._DB_PREFIX_.'attribute_group` ag

		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$cookie->id_lang.')

		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.')

		'.((isset($notNull) && $notNull) ? 'WHERE a.`id_attribute` IS NOT NULL AND al.`name` IS NOT NULL' : '').'

		

		WHERE a.id_attribute IN 

		(

		SELECT rra.value FROM '._DB_PREFIX_.'registered_requests_attributes rra WHERE id_customer='.$cookie->id_customer.' AND rra.id_product = '.$id_product_code.'

		)

		ORDER BY agl.`name` ASC, al.`name` ASC';
	$sqlattr = 'SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` as `attribute_group`

		FROM `'._DB_PREFIX_.'attribute_group` ag

		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$cookie->id_lang.')

		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.')

		INNER JOIN `'._DB_PREFIX_.'registered_requests_attributes` rra ON (rra.id_customer = '.$cookie->id_customer.' AND rra.`value` = a.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.' AND rra.`id_product` = '.$id_product_code.') 

		ORDER BY agl.`name` ASC, al.`name` ASC';
	$resultattr = Db::getInstance()->ExecuteS($sqlattr);
	return $resultattr;
	}

function getProductsAttrMail($id_product, $id_product_code, $customerid)
	{
	global $cookie;
	$prdctsattr = array();
	$sqlattr2 = '

		SELECT al.`name`, agl.`name` as `attribute_group`

		FROM `'._DB_PREFIX_.'attribute_group` ag

		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$cookie->id_lang.')

		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.')

		'.($notNull ? 'WHERE a.`id_attribute` IS NOT NULL AND al.`name` IS NOT NULL' : '').'

		

		WHERE a.id_attribute IN 

		(

		SELECT rra.value FROM '._DB_PREFIX_.'submitted_requests_attributes rra WHERE id_customer='.$cookie->id_customer.' AND rra.id_product = '.$id_product_code.'

		)

		ORDER BY agl.`name` ASC, al.`name` ASC';
	$sqlattr = 'SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` as `attribute_group`

		FROM `'._DB_PREFIX_.'attribute_group` ag

		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$cookie->id_lang.')

		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.')

		INNER JOIN `'._DB_PREFIX_.'submitted_requests_attributes` sra ON (sra.id_customer = '.$customerid.' AND sra.`value` = a.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.' AND sra.`id_product` = '.$id_product_code.') 

		ORDER BY agl.`name` ASC, al.`name` ASC';

	// echo $sqlattr."<br /><br />";

	$resultattr = Db::getInstance()->ExecuteS($sqlattr);
	return $resultattr;
	}

function getProductsAttrCookie($id_product, $id_product_code)
	{
	global $cookie;
	$a = isset($_COOKIE['rOa']) ? $_COOKIE['rOa'] : array();
	$a2 = str_replace('\"', '"', $a);
	$data2 = unserialize($a2);
	$prdctsattr = array();
	$sqlattr = 'SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` as `attribute_group`

		FROM `'._DB_PREFIX_.'attribute_group` ag

		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$cookie->id_lang.')

		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.')

		WHERE a.id_attribute IN 

		(

		'.implode(",", $data2[$id_product_code]) ;
   if(count($data2[$id_product_code])==0 ) $sqlattr.= '  "" '; /* Fixing: Fatal error: Uncaught You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ')	ORDER BY agl.`name` ASC, al.`name` ASC' at line 17*/
    
    $sqlattr.= '

		)	

			ORDER BY agl.`name` ASC, al.`name` ASC';
	$resultattr = Db::getInstance()->ExecuteS($sqlattr);
	return $resultattr;
	}

function getProductsAttrMyQuote($id_product, $id_product_code)
	{
	global $cookie;
	$prdctsattr = array();
	$sqlattr2 = '

		SELECT al.`name`, agl.`name` as `attribute_group`

		FROM `'._DB_PREFIX_.'attribute_group` ag

		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$cookie->id_lang.')

		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.')

		'.((isset($notNull) && $notNull) ? 'WHERE a.`id_attribute` IS NOT NULL AND al.`name` IS NOT NULL' : '').'

		

		WHERE a.id_attribute IN 

		(

		SELECT rra.value FROM '._DB_PREFIX_.'registered_requests_attributes rra WHERE id_customer='.$cookie->id_customer.' AND rra.id_product = '.$id_product_code.'

		)

		ORDER BY agl.`name` ASC, al.`name` ASC';
	$sqlattr = 'SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` as `attribute_group`

		FROM `'._DB_PREFIX_.'attribute_group` ag

		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$cookie->id_lang.')

		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`

		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.')

		INNER JOIN `'._DB_PREFIX_.'submitted_requests_attributes` rra ON (rra.id_customer = '.$cookie->id_customer.' AND rra.`value` = a.`id_attribute` AND al.`id_lang` = '.(int)$cookie->id_lang.' AND rra.`id_product` = '.$id_product_code.') 

		ORDER BY agl.`name` ASC, al.`name` ASC';

	// echo $sqlattr."<br /><br />";

	$resultattr = Db::getInstance()->ExecuteS($sqlattr);
	return $resultattr;
	}

?>
