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
        // post process
        $this->postProcess();
        // default template
        $this->assign();
    }

    public function postProcess() {
        if(Tools::getValue('action')) {
            if(Tools::getValue('action') == 'add') {
                echo $this->ajaxAddToQuotesCart();
            }
        }
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

    protected function ajaxAddToQuotesCart() {
        if (Tools::getValue('pqty') <= 0) {
            print json_encode(array('message' => Tools::displayError($this->module->l('Null quantity!!')),'hasError' => true));
            return;
        }
        elseif (!Tools::getValue('pid')) {
            print json_encode(array('message' => Tools::displayError($this->module->l('Product not found')),'hasError' => true));
            return;
        }

        $product = new Product(Tools::getValue('pid'), true, $this->context->language->id);
        if (!$product->id || !$product->active)
        {
            print json_encode(array('message' => Tools::displayError($this->module->l('This product is no longer available.')),'hasError' => true));
            return;
        }

        //check if product already in box


        /*if ($this->context->customer->isLogged()) {
            // add basket to DB
            if(!$this->context->cookie->__get('id_request')) {
                Db::getInstance()->insert('quotes', array(
                    'id_shop'      => $this->context->shop->id,
                    'id_lang'      => $this->context->language->id,
                    'id_customer'  => $this->context->customer->id,
                    'id_guest'     => 0,
                    'date_add'     => date('Y-m-d H:i:s'),
                ));
                $this->context->cookie->__set('id_request',Db::getInstance()->Insert_ID());
                //insert product for current basket
                $quantity = $this->getProductQuantity(Tools::getValue('pid'), Tools::getValue('pqty'), Db::getInstance()->Insert_ID());
                Db::getInstance()->insert('quotes_product', array(
                    'id_cart'      => Db::getInstance()->Insert_ID(),
                    'id_product'   => Tools::getValue('pid'),
                    'id_shop'      => $this->context->shop->id,
                    'quantity'     => $quantity,
                    'date_add'     => date('Y-m-d H:i:s'),
                ));
            }
            else {
                $quantity = $this->getProductQuantity(Tools::getValue('pid'), Tools::getValue('pqty'), Db::getInstance()->Insert_ID());
                Db::getInstance()->insert('quotes_product', array(
                    'id_cart'      => $this->context->cookie->__get('id_request'),
                    'id_product'   => Tools::getValue('pid'),
                    'id_shop'      => $this->context->shop->id,
                    'quantity'     => $quantity,
                    'date_add'     => date('Y-m-d H:i:s'),
                ));
            }
            return $this->generateAnswer($this->module->l('Your product was successfuly added to quote'), false);
        }
        elseif($this->context->cookie->id_guest) {
            // add basket from guest
            if(!$this->context->cookie->__get('id_request')) {
                Db::getInstance()->insert('quotes', array(
                    'id_shop'      => $this->context->shop->id,
                    'id_lang'      => $this->context->language->id,
                    'id_customer'  => 0,
                    'id_guest'     => $this->context->cookie->id_guest,
                    'date_add'     => date('Y-m-d H:i:s'),
                ));
                $this->context->cookie->__set('id_request',Db::getInstance()->Insert_ID());

                //insert product for current basket
                $quantity = $this->getProductQuantity(Tools::getValue('pid'), Tools::getValue('pqty'), Db::getInstance()->Insert_ID());
                Db::getInstance()->insert('quotes_product', array(
                    'id_cart'      => Db::getInstance()->Insert_ID(),
                    'id_product'   => Tools::getValue('pid'),
                    'id_shop'      => $this->context->shop->id,
                    'quantity'     => $quantity,
                    'date_add'     => date('Y-m-d H:i:s'),
                ));
            }
            else {
                $quantity = $this->getProductQuantity(Tools::getValue('pid'), Tools::getValue('pqty'), Db::getInstance()->Insert_ID());
                Db::getInstance()->insert('quotes_product', array(
                    'id_cart'      => $this->context->cookie->__get('id_request'),
                    'id_product'   => Tools::getValue('pid'),
                    'id_shop'      => $this->context->shop->id,
                    'quantity'     => $quantity,
                    'date_add'     => date('Y-m-d H:i:s'),
                ));
            }
            return $this->generateAnswer($this->module->l('Your product was successfuly added to quote1'), false);
        }*/
    }
    private function getProductQuantity($pid, $quantity, $id_request) {
        if($row = Db::getInstance()->ExecuteS('SELECT `quantity` FROM '._DB_PREFIX_.'quotes_product WHERE `id_cart` = '.$id_request.' AND `id_product` ='.$pid)) {
            $rw = Db::getInstance()->getRow($row);
                return (int)($rw['quantity'] + $quantity);
        }
        else
            return $quantity;

    }
    private function generateAnswer($message = '', $hasError = false) {
        print json_encode(array('hasError' => $hasError, 'message' => $message));
    }
}