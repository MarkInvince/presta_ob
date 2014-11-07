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
	require_once (dirname(__FILE__) . '/../../config/config.inc.php');

	require_once (dirname(__FILE__) . '/../../init.php');

	require_once (dirname(__FILE__) . '/functions.php');

	}
  else
	{
	include (dirname(__FILE__) . '/../../config/config.inc.php');

	include (dirname(__FILE__) . '/../../init.php');

	include (dirname(__FILE__) . '/functions.php');

	}

$idProduct = (int)Tools::getValue('pid') ? (int)Tools::getValue('pid') : 0;
$idIpa = (int)Tools::getValue('ipa') ? (int)Tools::getValue('ipa') : 0;
$qty = (int)Tools::getValue('qty') ? (int)Tools::getValue('qty') : 0;
$textattrib = Tools::getValue('textattrib');
$nrrow = Tools::getValue('nrrows');

if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4') $id_shop = 0;
  else $id_shop = (int)Context::getContext()->shop->id;
global $cookie;
$attributes = array();

foreach($_GET as $k => $v)
	{
	if (is_numeric($k)) $attributes[$k] = $v;
	}

$tm = date('Y-m-d H-i-s');
$path = '/';
$attribvalues = '';
$old_textattrib = (isset($_COOKIE['rOatext']) ? $_COOKIE['rOatext'] : '');
setcookie('rOatext', $old_textattrib . '-p' . $idProduct . ':' . $textattrib, time() + 1728000, $path);
/* runs when we modify the quantity only */

if (Tools::getValue('modqqty'))
	{
	$modcustomer = (int)Tools::getValue('modcustomer');
	$modproduct = (int)Tools::getValue('modproduct');
	$modqty = (int)Tools::getValue('modqty');
	if ($modcustomer == 0)
		{
		$sq = explode('-', $_COOKIE['rOq']);
		$newqs = array();
		foreach($sq as $sv)
			{
			if ($sv != '')
				{
				$elq = explode('_', $sv);
				if ($elq[0] == $modproduct)
					{
					$row = $elq[0] . '_' . $modqty;
					$newqs[] = $row;
					}
				  else
					{
					$row = $elq[0] . '_' . $elq[1];
					$newqs[] = $row;
					}
				}
			}

		$newqtycookie = implode('-', $newqs);
		setcookie('rOq', '-' . $newqtycookie . '-', time() + 1728000, $path);
		}
	  else
		{
		DB::getInstance()->Execute("UPDATE " . _DB_PREFIX_ . "registered_requests SET qty='$modqty' WHERE id_product='$modproduct' AND id_customer='$modcustomer'");
		}
	}
  else
if (Tools::getValue('modgname'))
	{
	$modgroup = Tools::getValue('modgroup');
	$modgname = Tools::getValue('modgname');
	Db::getInstance()->Execute("UPDATE " . _DB_PREFIX_ . "submitted_requests_groups SET gname='" . $modgname . "' WHERE id_group='" . $modgroup . "'");
	}
  else
	{
	/* sent by submitReq() - saving the checkout form */
	if (!$idProduct && Tools::getValue('sbmit'))
		{
		$idCustomer = (int)Tools::getValue('customer');
		$customer = new Customer($idCustomer);
		$gname = Tools::getValue('gname');
		$comment = Tools::getValue('comment');
		$newTotal = Tools::getValue('newTotal');
		$priceTotal = Tools::getValue('priceTotal');
		Db::getInstance()->Execute("INSERT INTO " . _DB_PREFIX_ . "submitted_requests_groups (id_group, id_customer, id_shop, original_price, bargained_price, gname, comment, date) VALUES ('0', '" . $idCustomer . "', '" . $id_shop . "', '" . ($priceTotal) . "', '-', '" . $gname . "', '" . $comment . "', '" . $tm . "')");
		/* update the total of submitted quotes for this client */
		Db::getInstance()->Execute("UPDATE " . _DB_PREFIX_ . "customer SET quotes='" . $newTotal . "' WHERE id_customer=" . $idCustomer . "");
		$res = DB::getInstance()->ExecuteS('SELECT id_product,qty,id_product_attribute FROM ' . _DB_PREFIX_ . 'registered_requests WHERE id_customer=' . $idCustomer);
		$gres = DB::getInstance()->ExecuteS('SELECT MAX(id_group) AS "id_group" FROM ' . _DB_PREFIX_ . 'submitted_requests_groups');
		$latest_group = $gres[0]['id_group']; /*get the latest request id */
		$producTotPrice = 0;
		foreach($res as $r)
			{

			// Added By Khush

			$usetax = false;
			if (Tax::excludeTaxeOption()) $usetax = false;
			list($id_product, $scrap) = explode('06', $r['id_product']);
			$productPrice = Product::getPriceStatic($id_product, $usetax, $r['id_product_attribute']);
			$producTotPrice+= $productPrice;

			// End

			DB::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'submitted_requests(id_request, id_customer, id_product, id_shop, id_group, qty, date, status, id_product_attribute) VALUES(0, ' . $idCustomer . ', ' . $r['id_product'] . ', ' . $id_shop . ', ' . $latest_group . ', ' . $r['qty'] . ', "' . $tm . '", 0,' . $r['id_product_attribute'] . ')');
			DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests WHERE id_customer= ' . $idCustomer);
			$res3 = DB::getInstance()->ExecuteS('SELECT MAX(id_request) AS "id_request" FROM ' . _DB_PREFIX_ . 'submitted_requests');
			$latest_id = $res3[0]['id_request']; /* get the latest request id */
			$resattrib = DB::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'registered_requests_attributes WHERE id_customer=' . $idCustomer . ' AND id_product = ' . $r['id_product'] . ' ');
			foreach($resattrib as $attribvalue) /* add attributes for this particular product */
			DB::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'submitted_requests_attributes(id_request, id_customer, id_product, id_shop, qty, id_attribute, value) VALUES(' . $latest_id . ', ' . $attribvalue['id_customer'] . ', ' . $attribvalue['id_product'] . ', ' . $id_shop . ', ' . $attribvalue['qty'] . ', ' . $attribvalue['id_attribute'] . ', ' . $attribvalue['value'] . ')');
			}

		// Added By Khush

		DB::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'submitted_requests_groups

						SET id_currency = "' . (int)Context::getContext()->currency->id . '" ,

						original_price = "' . $producTotPrice . '" WHERE id_group= "' . $latest_group . '"');

		// End

		DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests_attributes WHERE id_customer= ' . $idCustomer);
		$prdcts = array();
		foreach($res as $r)
			{
			$realprd = Tools::substr($r['id_product'], 0, strpos($r['id_product'], '06'));
			$product = getProductsMail((int)$realprd, $r['id_product'], $idCustomer);
			$nRes = DB::getInstance()->ExecuteS('SELECT name FROM ' . _DB_PREFIX_ . 'product_lang WHERE id_product=' . $realprd . ' AND id_lang = ' . ($cookie->id_lang) . ' ');
			$product['name'] = $nRes[0]['name'];
			$product['qty'] = $r['qty'];
			$prdcts[] = $product;
			}

		$productsList = '';
		$key = 0;
		foreach($prdcts as $product)
			{
			$key++;
			$productsList.= '<tr style="background-color: ' . ($key % 2 ? '#DDE2E6' : '#EBECEE') . ';">

					<td style="padding: 0.6em 0.4em;"><strong>' . $product['qty'] . '&nbsp;x&nbsp;' . $product['name'] . '</strong>

					<br />';
			$uniqueattrib = array();
			foreach($product['attributes'] as $prdattr)
				{
				if (!in_array($prdattr['attribute_group'], $uniqueattrib))
					{
					$productsList.= $prdattr['attribute_group'] . ' - ' . $prdattr['name'] . '<br />';
					$uniqueattrib[] = $prdattr['attribute_group'];
					}
				}

			if ($product['reference'] != '') $productsList.= 'Reff :' . $product['reference'];
			$productsList.= '</td></tr>';
			}

		$clientemaildata = Tools::getValue('clientemaildata');
		$clientemaildata = str_replace(';', '<br />', $clientemaildata);
		$results1 = DB::getInstance()->ExecuteS("Select * FROM " . _DB_PREFIX_ . "address WHERE id_customer=" . $idCustomer);
		foreach($results1 as $row)
			{
			$company = $row['company'];
			$vat = $row['vat_number'];
			$dni = $row['dni'];
			$address1 = $row['address1'];
			$address2 = $row['address2'];
			$city = $row['city'];
			$postcode = $row['postcode'];
			$phone = $row['phone'];
			$phone_mobile = $row['phone_mobile'];
			$idCountry = $row['id_country'];
			}

		$results2 = DB::getInstance()->ExecuteS("Select * FROM " . _DB_PREFIX_ . "country_lang WHERE id_country=" . $idCountry . " AND id_lang=" . ($cookie->id_lang));
		foreach($results2 as $row) $countryname = $row['name'];
		if (empty($company)) $company = '-';
		if (empty($vat)) $vat = '-';
		if (empty($dni)) $dni = '-';
		if (empty($address2)) $address2 = '-';
		if (empty($phone)) $phone = '-';
		if (empty($phone_mobile)) $phone_mobile = '-';
		$data = array(
			'{firstname}' => $customer->firstname,
			'{lastname}' => $customer->lastname,
			'{email}' => $customer->email,
			'{postcode}' => $postcode,
			'{company}' => $company,
			'{vat}' => $vat,
			'{dni}' => $dni,
			'{address1}' => $address1,
			'{address2}' => $address2,
			'{city}' => $city,
			'{phone}' => $phone,
			'{phone_mobile}' => $phone_mobile,
			'{country}' => $countryname,
			'{date}' => Tools::displayDate(date('Y-m-d H:i:s') , (int)($cookie->id_lang) , 1) ,
			'{products}' => $productsList,
			'{clientdata}' => $clientemaildata,
			'{total_products}' => $key
		);
		
		/* notification email addresses */
		$email_list = array();
		$main = Db::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "askforaquote_emails WHERE main_email is not null");
		$c_eids = Db::getInstance()->ExecuteS("SELECT custom_emails FROM " . _DB_PREFIX_ . "askforaquote_emails WHERE custom_emails is not null");
		$eids = Db::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "askforaquote_emails WHERE employee_ids is not null");
		$eids = $eids[0]['employee_ids'];
		$employee_emails = Db::getInstance()->ExecuteS("SELECT email FROM " . _DB_PREFIX_ . "employee WHERE id_employee IN (" . $eids . ")");
		if (!empty($main)) $email_list[] = Configuration::get('PS_SHOP_EMAIL');
		if (!empty($employee_emails))
		foreach($employee_emails as $e) $email_list[] = $e['email'];
		if (!empty($c_eids))
			{
			$v = 1;
			foreach($c_eids as $e) $email_list[] = $e['custom_emails'];
			}

		/* email addresses end */
		Mail::Send((int)($cookie->id_lang) , 'askforaquote', Mail::l('Request confirmation') , $data, $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null, dirname(__FILE__) . '/frontoffice/mails/');
		
		foreach($email_list as $k => $v) Mail::Send((int)($cookie->id_lang) , 'askforaquote_staff', Mail::l('New request') , $data, $v, null /* to name */
		, $customer->email, $customer->firstname . ' ' . $customer->lastname, null /* attachment */
		, null /* smtp */
		, dirname(__FILE__) . '/frontoffice/mails/', false /* die */
		, null /* idshop */
		, null /* bcc */);
		exit;
		}

	/* add request into basket */
	if (Tools::getValue('op') == 1)
		{
		/* if user is logged in */
		if ($cookie->logged)
			{
			$ipavalue = (int)Tools::getValue('ipa') ? (int)Tools::getValue('ipa') : 0;
			$idCustomer = (int)$cookie->id_customer;
			$qty = (int)Tools::getValue('qty') ? (int)Tools::getValue('qty') : 0;

			// $reqRes = DB::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'registered_requests WHERE id_product ='.$idProduct.' and id_product_attribute = '.$ipavalue.' and
			//							id_shop = '.$id_shop.' and id_customer ='.$idCustomer);
			// if($reqRes){

			DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests WHERE id_product =' . $idProduct . ' and id_product_attribute = ' . $ipavalue . ' and 
										id_shop = ' . $id_shop . ' and id_customer =' . $idCustomer);

			// }

			DB::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'registered_requests(id_request, id_customer, id_product, id_shop, qty, id_product_attribute, date) VALUES(0, ' . $idCustomer . ', ' . $idProduct . ', ' . $id_shop . ', ' . $qty . ', ' . $ipavalue . ', "' . $tm . '")');
			$res = DB::getInstance()->ExecuteS('SELECT MAX(id_request) AS "id_request" FROM ' . _DB_PREFIX_ . 'registered_requests');
			$latest_id = $res[0]['id_request']; /* get the latest request id */
			if (!empty($attributes))
				{
				/* add attributes for this particular product */
				foreach($attributes as $id_attribute => $value)
					{
					DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests_attributes WHERE id_product =' . $idProduct . ' and id_attribute = ' . $id_attribute . ' and 
										id_shop = ' . $id_shop . ' and id_customer =' . $idCustomer . ' and value ="' . $value . '"');
					DB::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'registered_requests_attributes(id_request, id_customer, id_product, id_shop, qty, id_attribute, value) VALUES(' . $latest_id . ', ' . $idCustomer . ', ' . $idProduct . ', ' . $id_shop . ', ' . $qty . ', ' . $id_attribute . ', ' . $value . ')');
					}
				}
			  else
				{ /* inserting without attributes */
				DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests_attributes WHERE id_product =' . $idProduct . ' and id_attribute = 0 and 
										id_shop = ' . $id_shop . ' and id_customer =' . $idCustomer);
				DB::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'registered_requests_attributes(id_request, id_customer, id_product, id_shop, qty, id_attribute, value) VALUES(' . $latest_id . ', ' . $idCustomer . ', ' . $idProduct . ', ' . $id_shop . ', ' . $qty . ', 0, 0)');
				}
			}
		  else
		/* if user's not logged just cookie the requests. if they register, add to db */
			{
			$i = isset($_COOKIE['rOi']) ? $_COOKIE['rOi'] : array();
			$ipavalue = Tools::getValue('ipa');
			/* quantity */
			$qty = isset($_COOKIE['rOq']) ? $_COOKIE['rOq'] : '';
			$qty.= "-" . $idProduct . '_' . Tools::getValue('qty');
			setcookie('rOq', $qty, time() + 1728000, $path);
			/* set the attributes for the current product */
			if (empty($i))
				{
				$i = array(
					$idProduct => $ipavalue
				);
				setcookie('rOi', serialize($i) , time() + 1728000, $path); /* 60*60*24 */
				}
			  else
				{
				$fromcookie = str_replace('\"', '"', $i);
				/* or change to the selected attributes .. not sure if this is ever used though */
				$i = unserialize($fromcookie);
				$i[$idProduct] = $ipavalue;
				setcookie('rOi', serialize($i) , time() + 1728000, $path); /* 60*60*24 */
				}

			$c = isset($_COOKIE['rOp']) ? $_COOKIE['rOp'] : array();
			if (empty($c)) setcookie('rOp[0]', Tools::getValue('pid') , time() + 1728000, $path); /* 60*60*24 */
			  else
				{
				$maxK = 0;
				$already = false;
				foreach($c as $k => $v)
					{
					if ($k > $maxK) $maxK = $k;
					if ($v == Tools::getValue('pid')) $already = true;
					}

				if (!$already) setcookie('rOp[' . ($maxK + 1) . ']', Tools::getValue('pid') , time() + 1728000, $path);
				}

			$a = isset($_COOKIE['rOa']) ? $_COOKIE['rOa'] : array();
			/* set the attributes for the current product */
			if (empty($a))
				{
				$a = array(
					$idProduct => $attributes
				);
				setcookie('rOa', serialize($a) , time() + 1728000, $path); /* 60*60*24 */
				}
			  else
				{
				$fromcookie = str_replace('\"', '"', $a);
				$a = unserialize($fromcookie); /* or change to the selected attributes .. not sure if this is ever used though */
				$a[$idProduct] = $attributes;;
				setcookie('rOa', serialize($a) , time() + 1728000, $path); /* 60*60*24 */
				}
			}
		}
	elseif (Tools::getValue('op') == 0) /* remove request */
		{
		/* quantity */
		$qty = isset($_COOKIE['rOq']) ? $_COOKIE['rOq'] : '';
		$s = explode('-', $qty);
		foreach($s as $key => $item)
			{
			if (Tools::strlen(strstr($item, strval($idProduct))) > 0) $s[$key] = '';
			}

		$qty = implode('-', $s);
		setcookie('rOq', $qty, time() + 1728000, $path);
		if ($cookie->logged)
			{
			$idCustomer = (int)$cookie->id_customer;
			DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests WHERE id_customer= ' . $idCustomer . ' AND id_product = ' . $idProduct);
			DB::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'registered_requests_attributes WHERE id_customer= ' . $idCustomer . ' AND id_product = ' . $idProduct);
			}
		  else
			{
			$i = isset($_COOKIE['rOi']) ? $_COOKIE['rOi'] : array();
			if (!empty($i))
				{
				foreach($i as $indx => $ipaId)
					{
					if ($ipaId == $idIpa)
						{
						setcookie('rOi[' . $indx . ']', '', time() - 1728000, $path);
						break;
						}
					}
				}

			$c = isset($_COOKIE['rOp']) ? $_COOKIE['rOp'] : array();
			if (!empty($c))
				{
				foreach($c as $indx => $prodId)
					{
					if ($prodId == $idProduct)
						{
						setcookie('rOp[' . $indx . ']', '', time() - 1728000, $path);
						break;
						}
					}
				}

			/* textattrib */
			$old_textattrib = (isset($_COOKIE['rOatext']) ? $_COOKIE['rOatext'] : '');
			if ($old_textattrib != '')
				{
				$saveold = $old_textattrib;
				$texttoreplace = Tools::substr($old_textattrib, strpos($old_textattrib, '-p' . $idProduct . ':') + 2);
				if (strpos($texttoreplace, '-p') > 0) $texttoreplace = Tools::substr($texttoreplace, 0, strpos($texttoreplace, '-p'));
				$newtextattrib = str_replace('-p' . $texttoreplace, '', $saveold);
				setcookie('rOatext', $newtextattrib, time() + 1728000, $path);
				}

			$a = isset($_COOKIE['rOa']) ? $_COOKIE['rOa'] : array();
			if (empty($a))
				{
				}
			  else
			/* empty the attributes for this particular product */
				{
				$fromcookie = str_replace('\"', '"', $a);
				$a = unserialize($fromcookie);
				$a[$idProduct] = array();
				setcookie('rOa', serialize($a) , time() + 1728000, $path); /* 60*60*24 */
				}
			}
		}
	}

?>