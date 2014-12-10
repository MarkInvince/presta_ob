<?php
/**
 * 2007-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2014 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

function getProductAttributeImage($id_product, $id_product_attribute, $id_lang)
{
	$mysql = '  SELECT pa.`id_product_attribute` , pa.`id_product` , pa.`price` , pac.`id_attribute` , al.`name` , paimg.`id_image`
				FROM  `'._DB_PREFIX_.'product_attribute` pa
				LEFT JOIN  `'._DB_PREFIX_.'product_attribute_combination` pac ON ( pa.`id_product_attribute` = pac.`id_product_attribute` )
				LEFT JOIN  `'._DB_PREFIX_.'product_attribute_image` paimg ON ( pac.`id_product_attribute` = paimg.`id_product_attribute` )
				LEFT JOIN  `'._DB_PREFIX_.'attribute` a ON ( pac.`id_attribute` = a.`id_attribute` )
				LEFT JOIN  `'._DB_PREFIX_.'attribute_lang` al ON ( al.`id_attribute` = a.`id_attribute` )
				WHERE pa.`id_product_attribute` = '.pSQL($id_product_attribute).'
				AND pa.`id_product` = '.pSQL($id_product).'
				AND  `id_lang` = '.pSQL($id_lang).' ORDER BY pa.`id_product_attribute` LIMIT 1';
	$row = Db::getInstance()->executeS($mysql);
	if (!$row)
		return array();

	return $row[0]['id_image'];
}
function getProductAttributesSmall($id_attribute, $id_lang) {
    $mysql = '  SELECT al.`name` AS attribute_name
    			FROM `'._DB_PREFIX_.'product_attribute_combination` pac
    			LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
    			LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
    			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
    				a.`id_attribute` = al.`id_attribute`
    				AND al.`id_lang` = '.$id_lang.'
    			)
    			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (
    				ag.`id_attribute_group` = agl.`id_attribute_group`
    				AND agl.`id_lang` = '.$id_lang.'
    			)
    			WHERE pac.`id_product_attribute` IN ('.$id_attribute.')
    			ORDER BY agl.`public_name` ASC';
    $row = Db::getInstance()->executeS($mysql);
    if (!$row)
		return '';
        
    $output = array();
    foreach($row as $item)
    $output[]= $item['attribute_name'];
    return implode(', ', $output);
}
function quoteNum($id_customer)
{
	$sql = 'SELECT COUNT(`id_quote`) FROM `'._DB_PREFIX_.'quotes` WHERE `id_customer`='.$id_customer;
	$result = Db::getInstance()->getValue($sql);
	if ($result)
		$result++;
	else
		$result = 1;

	return $result;
}

function quotesMailConfirm($template, $to, $message_vars, $subject, $module_path, $lang_id, $shop_id )
{
	if (Mail::Send($lang_id, $template, $subject, $message_vars, $to, null, null, null, null, null, $module_path, false, $shop_id))
		return true;

	return true;
}