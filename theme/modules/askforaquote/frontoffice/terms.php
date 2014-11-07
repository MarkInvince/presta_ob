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
	}
  else
	{
	include ('../../../config/config.inc.php');
	include ('../../../init.php');
	}

if (Tools::substr(_PS_VERSION_, 0, 3) == '1.5')
	{
	$controller = new FrontController();
	$controller->init();
	$controller->setMedia();
	}
  else
if (Tools::substr(_PS_VERSION_, 0, 3) == '1.6')
	{
	$o_controller = new FrontController();
	$o_controller->init();
	$o_controller->setMedia();
	}

$terms = DB::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'asforaquote_terms WHERE id_lang=' . $cookie->id_lang . '');
$smarty->assign('customtext', nl2br($terms['customtext']));
//$smarty->display('../views/templates/front/terms.tpl');
$smarty->display(getcwd().'/../views/templates/front/terms.tpl');
?>