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

	}
  else
	{
	include ('../../config/config.inc.php');

	include ('../../init.php');

	}

if ($id_request = Tools::getValue('id_request'))
	{
	$res = DB::getInstance()->ExecuteS('SELECT status FROM ' . _DB_PREFIX_ . 'submitted_requests WHERE id_request=' . $id_request);
	DB::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'submitted_requests SET status=' . ($res[0]['status'] ? '0' : '1') . ' WHERE id_request=' . $id_request);
	exit;
	}
  else
	{
	$simple_checkout = Tools::getValue('simple_checkout');
	$terms = Tools::getValue('terms');
	$gc = Tools::getValue('gc');
	$bargain = Tools::getValue('bargain');
	DB::getInstance()->Execute('TRUNCATE table ' . _DB_PREFIX_ . 'askforaquote_settings');
	DB::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'askforaquote_settings(simple_checkout,terms,guest_checkout,enable_bargain) VALUES (' . $simple_checkout . ',' . $terms . ',' . $gc . ',' . $bargain . ')');
	exit;
	}

?>