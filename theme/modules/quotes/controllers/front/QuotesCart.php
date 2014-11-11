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
include_once(_PS_MODULE_DIR_.'quotes/classes/QuotesProduct.php');
class quotesQuotesCartModuleFrontController extends ModuleFrontController {

    public $ssl = true;
    public $display_column_left = true;

    public $quote;
    public $quote_product;

    private $user_token;

    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();

        $this->quote = new QuotesProductCart;
        $this->user_token = uniqid();
        //set user unique key
        if(!$this->context->cookie->__isset('request_id')) {
            $this->context->cookie->__set('request_id', $this->user_token);
        }
    }

    public function initContent()
    {
        // Send noindex to avoid ghost carts by bots
        header("X-Robots-Tag: noindex, nofollow", true);

        parent::initContent();
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

        // update model if user is logged in system
        if ($this->context->customer->isLogged()) {
            $this->quote->update();
        }
        if($this->context->cookie->__isset('request_id')) {
            //add product to shop cart
            $this->quote->id_quote = $this->context->cookie->__get('request_id');
            $this->quote->id_shop = $this->context->shop->id;
            $this->quote->id_shop_group = $this->context->shop->id_shop_group;
            $this->quote->id_lang = $this->context->language->id;
            $this->quote->id_product = $product->id;
            $this->quote->id_guest = (int)$this->context->cookie->id_guest;
            $this->quote->id_customer = (int)$this->context->customer->id;
            $this->quote->quantity = (int)pSQL(Tools::getValue('pqty'));
            $this->quote->date_add = date('Y-m-d H:i:s', time());
            $this->quote->add();
        }


        /*// process add quote request to cart
        if(!isset($_SESSION['current_request'])) {
            $this->quote->id_shop_group = $this->context->shop->id_shop_group;
            $this->quote->id_shop = $this->context->shop->id;
            $this->quote->id_lang = $this->context->language->id;
            $this->quote->id_customer = (int)$this->context->customer->id;
            $this->quote->id_guest = (int)$this->context->cookie->id_guest;
            $this->quote->date_add = date('Y-m-d H:i:s', time());
            $this->quote->secure_key = '';
            // save new quote request into db and save into session current request_id
            $_SESSION['current_request'] = $this->quote->save();
        }

        // add product to cart table
        $sql = 'SELECT `id` FROM `'._DB_PREFIX_.'quotes` WHERE `id_customer` = '.(isset($this->context->customer->id) ? $this->context->customer->id : 0).' AND `id_guest` = '.(isset($this->context->cookie->id_guest) ? $this->context->cookie->id_guest : 0).' LIMIT 0';
        if ($request_id = Db::getInstance()->getValue($sql)) {
            $this->quote_product->id_quote = $request_id;
            $this->quote_product->id_shop = $this->context->shop->id;
            $this->quote_product->id_product = $product->id;
            $this->quote_product->id_customer = (int)$this->context->customer->id;
            $this->quote_product->quantity = 1;
            $this->quote_product->date_add = date('Y-m-d H:i:s', time());
            //add product
            if($this->quote_product->containsProduct($product->id)) {
                // update product qty
                $this->quote_product->updateQty((int)Tools::getValue('pqty'), $product->id);
            }
            else {
                $this->quote_product->save();
            }
        }*/
        // Add cart if no cart found
        /*if (!$this->context->cart->id)
        {
            $guest = new Guest(Context::getContext()->cookie->id_guest);
            if ($this->context->cart->id)
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
        }
        $update_quantity = $this->context->cart->updateQty(Tools::getValue('pqty'), $product->id, 0, 0, Tools::getValue('op', 'up'));
        if ($update_quantity < 0)
        {
            // If product has attribute, minimal quantity is set with minimal quantity of attribute
            $minimal_quantity = ($this->id_product_attribute) ? Attribute::getAttributeMinimalQty($this->id_product_attribute) : $product->minimal_quantity;
            print json_encode(array('message' => Tools::displayError($this->module->l('You must add %d minimum quantity')),'hasError' => true));
            return;
        }
        elseif (!$update_quantity) {
            print json_encode(array('message' => Tools::displayError($this->module->l('You already have the maximum quantity available for this product.')),'hasError' => true));
            return;
        }*/

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
    private function generateAnswer($message = '', $hasError = false) {
        print json_encode(array('hasError' => $hasError, 'message' => $message));
    }
}