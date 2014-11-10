<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class quotesQuotesCartModuleFrontController extends ModuleFrontController {
    
    public $ssl = true;
	public $display_column_left = true;

	public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
	}
    
    public function initContent()
	{
		parent::initContent();
        $this->postProcess();
        $this->assign();
    }

	public function assign()
	{
        if ($this->context->customer->isLogged())
            $this->context->smarty->assign('isLogged', '1');
        else
            $this->context->smarty->assign('isLogged', '0');

        $this->context->smarty->assign('empty','true');

        $this->setTemplate('quotes_cart.tpl');
    }
    public function postProcess()
    {
        $action = Tools::getIsset('action') ? Tools::getValue('action') : false;
        if($action) {
            switch($action) {
                case 'add':
                    $this->ajaxAddToQuotesCart();
                    break;
                case 'delete':
                default:
                    break;
            }
        }
    }
    protected function ajaxAddToQuotesCart() {
        if (Tools::getValue('pqty') <= 0)
            $this->errors[] = Tools::displayError($this->l('Null quantity!!'), !Tools::getValue('ajax'));
        elseif (!$this->id_product)
            $this->errors[] = Tools::displayError('Product not found', !Tools::getValue('ajax'));

        $product = new Product($this->id_product, true, $this->context->language->id);
        if (!$product->id || !$product->active)
        {
            $this->errors[] = Tools::displayError('This product is no longer available.', !Tools::getValue('ajax'));
            return;
        }

        if ($this->context->customer->isLogged()) {
            // add basket to DB

        }
        else {
            // add basket into session

        }

    }
    private function updateQuantityForProduct($id_product, $pqty) {

    }
}