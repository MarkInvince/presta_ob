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
    public $isLogged;
    
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
    

    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(array(
            _THEME_JS_DIR_.'tools/vatManagement.js',
            _THEME_JS_DIR_.'tools/statesManagement.js',
            _PS_JS_DIR_.'validate.js'
        ));

        $this->addJS($this->module->getLocalPath().'js/quotes_cart.js');
    }
    
    public function initContent()
    {
        // Send noindex to avoid ghost carts by bots
        header("X-Robots-Tag: noindex, nofollow", true);

        parent::initContent();
        // default template
        $this->assign();
        $this->_processAddressFormat();
    }

    public function postProcess() {

        if (Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount'))
            $this->processSubmitAccount();
        
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
        $back = $this->context->link->getModuleLink($this->module->name, 'QuotesCart', array(), true);

        $tpl_path = $this->module->getLocalPath()."views/templates/front";

        $selectedCountry = (int)(Configuration::get('PS_COUNTRY_DEFAULT'));

        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        else
            $countries = Country::getCountries($this->context->language->id, true);

        // If a rule offer free-shipping, force hidding shipping prices
        $free_shipping = false;
        foreach ($this->context->cart->getCartRules() as $rule)
            if ($rule['free_shipping'] && !$rule['carrier_restriction'])
            {
                $free_shipping = true;
                break;
            }

        $this->context->smarty->assign(array(
            'quotesNumber' => 1,
            'tpl_path' => $tpl_path,
            'back' => $back,
            'PS_GUEST_QUOTES_ENABLED' => Configuration::get('PS_GUEST_QUOTES_ENABLED'),
            'ADDRESS_ENABLED' => Configuration::get('ADDRESS_ENABLED'),
            'isGuest' => isset($this->context->cookie->is_guest) ? $this->context->cookie->is_guest : 0,
            'countries' => $countries,
            'sl_country' => isset($selectedCountry) ? $selectedCountry : 0,
            'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
            'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
            'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop')
        ));

        /* Load guest informations */
        if ($this->isLogged && $this->context->cookie->is_guest)
            $this->context->smarty->assign('guestInformations', $this->_getGuestInformations());

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

        print json_encode(array('products' => $this->quote->getProducts()));
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

    /**
     * Process submit on an account
     */
    protected function processSubmitAccount()
    {
        Hook::exec('actionBeforeSubmitAccount');
        $this->create_account = true;

        if (Tools::isSubmit('submitAccount'))
            $this->context->smarty->assign('email_create', 1);

        // New Guest customer
        if (!Tools::getValue('is_new_customer', 1) && !Configuration::get('PS_GUEST_QUOTES_ENABLED'))
            $this->errors[] = Tools::displayError('You cannot create a guest account..');

        if (!Tools::getValue('is_new_customer', 1))
            $_POST['passwd'] = md5(time()._COOKIE_KEY_);

        if (isset($_POST['guest_email']) && $_POST['guest_email'])
            $_POST['email'] = $_POST['guest_email'];

        // Checked the user address in case he changed his email address
        if (Validate::isEmail($email = Tools::getValue('email')) && !empty($email))
            if (Customer::customerExists($email))
                $this->errors[] = Tools::displayError('An account using this email address has already been registered.', false);

        // Preparing customer
        $customer = new Customer();
        $lastnameAddress = Tools::getValue('lastname');
        $firstnameAddress = Tools::getValue('firstname');
        $_POST['lastname'] = Tools::getValue('customer_lastname', $lastnameAddress);
        $_POST['firstname'] = Tools::getValue('customer_firstname', $firstnameAddress);
        $addresses_types = array('address');

//        if (!Configuration::get('PS_ORDER_PROCESS_TYPE') && Configuration::get('PS_GUEST_CHECKOUT_ENABLED') && Tools::getValue('invoice_address'))
//            $addresses_types[] = 'address_invoice';

        /*
        $error_phone = false;
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST'))
        {
            if (Tools::isSubmit('submitGuestAccount') || !Tools::getValue('is_new_customer'))
            {
                if (!Tools::getValue('phone') && !Tools::getValue('phone_mobile'))
                    $error_phone = true;
            }
            elseif (((Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Configuration::get('PS_ORDER_PROCESS_TYPE'))
                    || (Configuration::get('PS_ORDER_PROCESS_TYPE') && !Tools::getValue('email_create'))
                    || (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Tools::getValue('email_create')))
                && (!Tools::getValue('phone') && !Tools::getValue('phone_mobile')))
                $error_phone = true;
        }


        if ($error_phone)
            $this->errors[] = Tools::displayError('You must register at least one phone number.');
        */

        $this->errors = array_unique(array_merge($this->errors, $customer->validateController()));

        // Check the requires fields which are settings in the BO
        $this->errors = $this->errors + $customer->validateFieldsRequiredDatabase();


        // If simple rgistry without Address Delivery
        if (Tools::isSubmit('submitAccount') && !Tools::getValue('address_enabled'))
        {

            if (!count($this->errors))
            {
                if (Tools::isSubmit('newsletter'))
                    $this->processCustomerNewsletter($customer);

                $customer->firstname = Tools::ucwords($customer->firstname);
                $customer->birthday = (empty($_POST['years']) ? '' : (int)$_POST['years'].'-'.(int)$_POST['months'].'-'.(int)$_POST['days']);
                if (!Validate::isBirthDate($customer->birthday))
                    $this->errors[] = Tools::displayError('Invalid date of birth.');

                // New Guest customer
                $customer->is_guest = (Tools::isSubmit('is_new_customer') ? !Tools::getValue('is_new_customer', 1) : 0);
                $customer->active = 1;

                if (!count($this->errors))
                {
                    if ($customer->add())
                    {
                        if (!$customer->is_guest)
                            if (!$this->sendConfirmationMail($customer))
                                $this->errors[] = Tools::displayError('The email cannot be sent.');

                        $this->updateContext($customer);

                        $this->context->cart->update();

                        Hook::exec('actionCustomerAccountAdd', array(
                            '_POST' => $_POST,
                            'newCustomer' => $customer
                        ));

                        Tools::redirect($this->context->link->getModuleLink('quotes', 'QuotesCart'));
                    }
                    else
                        $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                }
            }
        }
        else // if address on or Guest account
        {
            //$this->errors[] = Tools::displayError('first if - else');

            $_POST['lastname'] = $lastnameAddress;
            $_POST['firstname'] = $firstnameAddress;
            $post_back = $_POST;
            // Preparing addresses
            foreach($addresses_types as $addresses_type)
            {
                $$addresses_type = new Address();
                $$addresses_type->id_customer = 1;

                if ($addresses_type == 'address_invoice')
                    foreach($_POST as $key => &$post)
                        if (isset($_POST[$key.'_invoice']))
                            $post = $_POST[$key.'_invoice'];

                $this->errors = array_unique(array_merge($this->errors, $$addresses_type->validateController()));
                if ($addresses_type == 'address_invoice')
                    $_POST = $post_back;

                if (!($country = new Country($$addresses_type->id_country)) || !Validate::isLoadedObject($country))
                    $this->errors[] = Tools::displayError('Country cannot be loaded with address->id_country');

                if (!$country->active)
                    $this->errors[] = Tools::displayError('This country is not active.');

                $postcode = Tools::getValue('postcode');
                /* Check zip code format */
                if ($country->zip_code_format && !$country->checkZipCode($postcode))
                    $this->errors[] = sprintf(Tools::displayError('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
                elseif(empty($postcode) && $country->need_zip_code)
                    $this->errors[] = Tools::displayError('A Zip / Postal code is required.');
                elseif ($postcode && !Validate::isPostCode($postcode))
                    $this->errors[] = Tools::displayError('The Zip / Postal code is invalid.');

                if ($country->need_identification_number && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni'))))
                    $this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
                elseif (!$country->need_identification_number)
                    $$addresses_type->dni = null;

                if (Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount'))
                    if (!($country = new Country($$addresses_type->id_country, Configuration::get('PS_LANG_DEFAULT'))) || !Validate::isLoadedObject($country))
                        $this->errors[] = Tools::displayError('Country is invalid');
                $contains_state = isset($country) && is_object($country) ? (int)$country->contains_states: 0;
                $id_state = isset($$addresses_type) && is_object($$addresses_type) ? (int)$$addresses_type->id_state: 0;
                if ((Tools::isSubmit('submitAccount')|| Tools::isSubmit('submitGuestAccount')) && $contains_state && !$id_state)
                    $this->errors[] = Tools::displayError('This country requires you to choose a State.');
            }
        }

        if (!count($this->errors))
        {
            if (Customer::customerExists(Tools::getValue('email')))
                $this->errors[] = Tools::displayError('An account using this email address has already been registered. Please enter a valid password or request a new one. ', false);


            if (!count($this->errors))
            {
                $customer->active = 1;
                // New Guest customer
                if (Tools::isSubmit('is_new_customer'))
                    $customer->is_guest = !Tools::getValue('is_new_customer', 1);
                else
                    $customer->is_guest = 0;
                if (!$customer->add())
                    $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                else
                {
                    foreach($addresses_types as $addresses_type)
                    {
                        $$addresses_type->id_customer = (int)$customer->id;
                        if ($addresses_type == 'address_invoice')
                            foreach($_POST as $key => &$post)
                                if (isset($_POST[$key.'_invoice']))
                                    $post = $_POST[$key.'_invoice'];

                        $this->errors = array_unique(array_merge($this->errors, $$addresses_type->validateController()));
                        if ($addresses_type == 'address_invoice')
                            $_POST = $post_back;
                        if (!count($this->errors) && (Tools::getValue('address_enabled') || $this->ajax || Tools::isSubmit('submitGuestAccount')) && !$$addresses_type->add())
                            $this->errors[] = Tools::displayError('An error occurred while creating your address.');
                    }
                    if (!count($this->errors))
                    {
                        if (!$customer->is_guest)
                        {
                            $this->context->customer = $customer;
                            $customer->cleanGroups();
                            // we add the guest customer in the default customer group
                            $customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
                            if (!$this->sendConfirmationMail($customer))
                                $this->errors[] = Tools::displayError('The email cannot be sent.');
                        }
                        else
                        {
                            $customer->cleanGroups();
                            // we add the guest customer in the guest customer group
                            $customer->addGroups(array((int)Configuration::get('PS_GUEST_GROUP')));
                        }
                        $this->updateContext($customer);
                        $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)$customer->id);
                        $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)$customer->id);
                        if (isset($address_invoice) && Validate::isLoadedObject($address_invoice))
                            $this->context->cart->id_address_invoice = (int)$address_invoice->id;

                        if ($this->ajax && Configuration::get('PS_ORDER_PROCESS_TYPE'))
                        {
                            $delivery_option = array((int)$this->context->cart->id_address_delivery => (int)$this->context->cart->id_carrier.',');
                            $this->context->cart->setDeliveryOption($delivery_option);
                        }

                        // If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
                        $this->context->cart->update();

                        // Avoid articles without delivery address on the cart
                        $this->context->cart->autosetProductAddress();

                        Hook::exec('actionCustomerAccountAdd', array(
                            '_POST' => $_POST,
                            'newCustomer' => $customer
                        ));

                        Tools::redirect($this->context->link->getModuleLink('quotes', 'QuotesCart'));
                    }
                }
            }
        }



        if (count($this->errors))
        {
            //for retro compatibility to display guest account creation form on authentication page
            if (Tools::getValue('submitGuestAccount'))
                $_GET['display_guest_checkout'] = 1;

            if (!Tools::getValue('is_new_customer'))
                unset($_POST['passwd']);

            $this->context->smarty->assign('authentification_error', $this->errors);
        }
    }

    /**
     * Process submit on a creation
     */
    protected function processSubmitCreate()
    {
        if (!Validate::isEmail($email = Tools::getValue('email_create')) || empty($email))
            $this->errors[] = Tools::displayError('Invalid email address.');
        elseif (Customer::customerExists($email))
        {
            $this->errors[] = Tools::displayError('An account using this email address has already been registered. Please enter a valid password or request a new one. ', false);
            $_POST['email'] = $_POST['email_create'];
            unset($_POST['email_create']);
        }
        else
        {
            $this->create_account = true;
            $this->context->smarty->assign('email_create', Tools::safeOutput($email));
            $_POST['email'] = $email;
        }
    }

    /**
     * Update context after customer creation
     * @param Customer $customer Created customer
     */
    protected function updateContext(Customer $customer)
    {
        $this->context->customer = $customer;
        $this->context->smarty->assign('confirmation', 1);
        $this->context->cookie->id_customer = (int)$customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        // if register process is in two steps, we display a message to confirm account creation
        if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE'))
            $this->context->cookie->account_created = 1;
        $customer->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
        // Update cart address
        $this->context->cart->secure_key = $customer->secure_key;
    }

    /**
     * sendConfirmationMail
     * @param Customer $customer
     * @return bool
     */
    protected function sendConfirmationMail(Customer $customer)
    {
        if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL'))
            return true;

        return Mail::Send(
            $this->context->language->id,
            'account',
            Mail::l('Welcome!'),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => Tools::getValue('passwd')),
            $customer->email,
            $customer->firstname.' '.$customer->lastname
        );
    }

    protected function _processAddressFormat()
    {
        $address_delivery = new Address((int)$this->context->cart->id_address_delivery);
        $address_invoice = new Address((int)$this->context->cart->id_address_invoice);

        $inv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_delivery->id_country, false, true);
        $dlv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_invoice->id_country, false, true);
        $requireFormFieldsList = AddressFormat::$requireFormFieldsList;

        // Add missing require fields for a new user susbscription form
        foreach ($requireFormFieldsList as $fieldName)
            if (!in_array($fieldName, $dlv_adr_fields))
                $dlv_adr_fields[] = trim($fieldName);

        foreach ($requireFormFieldsList as $fieldName)
            if (!in_array($fieldName, $inv_adr_fields))
                $inv_adr_fields[] = trim($fieldName);

        $inv_all_fields = array();
        $dlv_all_fields = array();

        foreach (array('inv', 'dlv') as $adr_type)
        {
            foreach (${$adr_type.'_adr_fields'} as $fields_line)
                foreach (explode(' ', $fields_line) as $field_item)
                    ${$adr_type.'_all_fields'}[] = trim($field_item);

            ${$adr_type.'_adr_fields'} = array_unique(${$adr_type.'_adr_fields'});
            ${$adr_type.'_all_fields'} = array_unique(${$adr_type.'_all_fields'});

            $this->context->smarty->assign($adr_type.'_adr_fields', ${$adr_type.'_adr_fields'});
            $this->context->smarty->assign($adr_type.'_all_fields', ${$adr_type.'_all_fields'});
        }
    }

    protected function _getGuestInformations()
    {
        $customer = $this->context->customer;
        $address_delivery = new Address($this->context->cart->id_address_delivery);

        $id_address_invoice = $this->context->cart->id_address_invoice != $this->context->cart->id_address_delivery ? (int)$this->context->cart->id_address_invoice : 0;
        $address_invoice = new Address($id_address_invoice);

        if ($customer->birthday)
            $birthday = explode('-', $customer->birthday);
        else
            $birthday = array('0', '0', '0');

        return array(
            'id_customer' => (int)$customer->id,
            'email' => Tools::htmlentitiesUTF8($customer->email),
            'customer_lastname' => Tools::htmlentitiesUTF8($customer->lastname),
            'customer_firstname' => Tools::htmlentitiesUTF8($customer->firstname),
            'newsletter' => (int)$customer->newsletter,
            'optin' => (int)$customer->optin,
            'id_address_delivery' => (int)$this->context->cart->id_address_delivery,
            'company' => Tools::htmlentitiesUTF8($address_delivery->company),
            'lastname' => Tools::htmlentitiesUTF8($address_delivery->lastname),
            'firstname' => Tools::htmlentitiesUTF8($address_delivery->firstname),
            'vat_number' => Tools::htmlentitiesUTF8($address_delivery->vat_number),
            'dni' => Tools::htmlentitiesUTF8($address_delivery->dni),
            'address1' => Tools::htmlentitiesUTF8($address_delivery->address1),
            'postcode' => Tools::htmlentitiesUTF8($address_delivery->postcode),
            'city' => Tools::htmlentitiesUTF8($address_delivery->city),
            'phone' => Tools::htmlentitiesUTF8($address_delivery->phone),
            'phone_mobile' => Tools::htmlentitiesUTF8($address_delivery->phone_mobile),
            'id_country' => (int)($address_delivery->id_country),
            'id_state' => (int)($address_delivery->id_state),
            'id_gender' => (int)$customer->id_gender,
            'sl_year' => $birthday[0],
            'sl_month' => $birthday[1],
            'sl_day' => $birthday[2],
            'company_invoice' => Tools::htmlentitiesUTF8($address_invoice->company),
            'lastname_invoice' => Tools::htmlentitiesUTF8($address_invoice->lastname),
            'firstname_invoice' => Tools::htmlentitiesUTF8($address_invoice->firstname),
            'vat_number_invoice' => Tools::htmlentitiesUTF8($address_invoice->vat_number),
            'dni_invoice' => Tools::htmlentitiesUTF8($address_invoice->dni),
            'address1_invoice' => Tools::htmlentitiesUTF8($address_invoice->address1),
            'address2_invoice' => Tools::htmlentitiesUTF8($address_invoice->address2),
            'postcode_invoice' => Tools::htmlentitiesUTF8($address_invoice->postcode),
            'city_invoice' => Tools::htmlentitiesUTF8($address_invoice->city),
            'phone_invoice' => Tools::htmlentitiesUTF8($address_invoice->phone),
            'phone_mobile_invoice' => Tools::htmlentitiesUTF8($address_invoice->phone_mobile),
            'id_country_invoice' => (int)($address_invoice->id_country),
            'id_state_invoice' => (int)($address_invoice->id_state),
            'id_address_invoice' => $id_address_invoice,
            'invoice_company' => Tools::htmlentitiesUTF8($address_invoice->company),
            'invoice_lastname' => Tools::htmlentitiesUTF8($address_invoice->lastname),
            'invoice_firstname' => Tools::htmlentitiesUTF8($address_invoice->firstname),
            'invoice_vat_number' => Tools::htmlentitiesUTF8($address_invoice->vat_number),
            'invoice_dni' => Tools::htmlentitiesUTF8($address_invoice->dni),
            'invoice_address' => $this->context->cart->id_address_invoice !== $this->context->cart->id_address_delivery,
            'invoice_address1' => Tools::htmlentitiesUTF8($address_invoice->address1),
            'invoice_address2' => Tools::htmlentitiesUTF8($address_invoice->address2),
            'invoice_postcode' => Tools::htmlentitiesUTF8($address_invoice->postcode),
            'invoice_city' => Tools::htmlentitiesUTF8($address_invoice->city),
            'invoice_phone' => Tools::htmlentitiesUTF8($address_invoice->phone),
            'invoice_phone_mobile' => Tools::htmlentitiesUTF8($address_invoice->phone_mobile),
            'invoice_id_country' => (int)($address_invoice->id_country),
            'invoice_id_state' => (int)($address_invoice->id_state),
        );
    }
    
}