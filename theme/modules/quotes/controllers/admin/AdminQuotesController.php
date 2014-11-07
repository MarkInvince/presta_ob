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

class AdminQuotesController extends ModuleAdminController
{
	public function __construct()
	{
	    $this->bootstrap = true;
        
		$this->display = 'view';
        
		parent::__construct();
		if (!$this->module->active)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        
        parent::init();
	    parent::setMedia();
    	// fix for 1.6.0.8
    	if(file_exists(_PS_THEME_DIR_.'global.tpl'))
    	{
    		Context::getContext()->smarty->fetch(_PS_THEME_DIR_.'global.tpl');
    		Context::getContext()->smarty->assign('js_defer' , (bool)Configuration::get('PS_JS_DEFER'));
    	}	
    	// End
    	parent::displayHeader();
        
		$this->postProcess();
	}

    private function ndisplayQuotes() {
        
    }
	public function postProcess()
	{
		
	}
}
