<?php

include_once(_PS_MODULE_DIR_ . 'quotes/classes/QuotesObj.php');

class quotesSubmitedQuotesModuleFrontController extends ModuleFrontController {

    public $ssl = true;
    public $display_column_left = false;
    public $quote_product;
    public $id_quote;
    public $id_customer;

    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();
        $this->quote = new QuotesObj;
        $this->id_quote = 0;
        $this->id_customer = (int)$this->context->cookie->id_customer;

        if (!$this->context->customer->isLogged()) {
            Tools::redirect('authentication.php');
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(array(
        ));
    }

    public function postProcess() {

        if (!Tools::getValue('id_quote'))
            $this->id_quote = 0;
        else {
            $this->id_quote = Tools::getValue('id_quote');
        }

        if (Tools::isSubmit('addClientBargain'))
            $this->addClientBargain(Tools::getValue('id_quote'));

        if (Tools::getValue('actionSubmitBargain'))
            $this->bargainCustomerSubmit();

        if (Tools::getValue('quoteRename')) {
            $this->quoteRename(Tools::getValue('id_quote'));
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

    public function assign()
    {
        // if id - get Quote information and Quote Bargains
        if ($this->id_quote != 0) {
            $quoteInfo = $this->quote->getQuoteInfo($this->id_quote);
            $quoteInfo = $this->foreachQuotes($quoteInfo);
            foreach ($quoteInfo as $quoteInf ) {
                $quoteInfo = $quoteInf;
            }
            $this->context->smarty->assign('quote', $quoteInfo);


            $bargains = $this->quote->getBargains($this->id_quote);

            foreach ($bargains as $key=>$bargain){
                //$bargain['bargain_price'] =
                $bargains[$key]['bargain_price_display'] = Tools::displayPrice(Tools::ps_round($bargain['bargain_price'],2), $this->context->currency);
            }

            $this->context->smarty->assign('bargains', $bargains);

        } else // if 0 Get quotes list
        {
            $quotes = $this->quote->getQuotesByCustomer($this->id_customer);
            $quotes = $this->foreachQuotes($quotes);

            $this->context->smarty->assign('quotes', $quotes);
        }

        $this->context->smarty->assign(array(
            'id_quote' => $this->id_quote,
            'id_customer' => $this->id_customer,
            'MESSAGING_ENABLED' => Configuration::get('MESSAGING_ENABLED')
        ));

        $this->setTemplate('submited_quotes.tpl');
    }

    /**
     * Parcing quotes products from json To Array And Get Total Price of Quote
     */
    protected function foreachQuotes($quotes) {
        foreach ($quotes as $firstkey=>$quoteInfo) {
            $quote_total_price = 0;
            foreach ($quoteInfo as $key=>$field){
                //$currency = new Currency($quoteInfo['id_currency'], null, $this->context->shop->id);
                if ($key == 'products'){
                    $quoteIn[$key] = Tools::unSerialize($field);
                    foreach($quoteIn[$key] as $k=>$product) {
                        $productObj = new Product($product['id'], true, $this->context->language->id);

                        $quoteIn[$key][$k]['name'] = $productObj->name;

                        $prod_price = Product::getPriceStatic($product['id'], true, NULL, 6);
                        $quoteIn[$key][$k]['price_total'] = Tools::displayPrice(Tools::ps_round($prod_price*$product['quantity'],2), $this->context->currency);
                        $quoteIn[$key][$k]['price'] = Tools::displayPrice(Tools::ps_round($prod_price,2), $this->context->currency);
                        $quoteIn[$key][$k]['link_rewrite'] = $productObj->link_rewrite;
                        $quoteIn[$key][$k]['link'] = $this->context->link->getProductLink($productObj, $productObj->link_rewrite, $productObj->category, null, null);

                        $quoteIn[$key][$k]['id_image'] = getProductAttributeImage($productObj->id, $product['id_attribute'], $this->context->language->id);

                        $quote_total_price = $quote_total_price + $prod_price*$product['quantity'];
                    }
                    $quoteIn['price'] = Tools::displayPrice(Tools::ps_round($quote_total_price,2), $this->context->currency);
                }
                elseif($key == 'burgain_price'){
                    $quoteIn['bargain_price'] = Tools::displayPrice(Tools::ps_round($field,2), $this->context->currency);
                }else
                    $quoteIn[$key] = $field;
            }
            $quotes[$firstkey] = $quoteIn;
        }
        return $quotes;
    }

    /**
     * Add client bargain to quote by quote id
     */
    protected function addClientBargain($id_quote = false) {
        if(!Configuration::get('MESSAGING_ENABLED') || !$id_quote)
            $this->errors[] = Tools::displayError('You can not add bargain without quote_id.');

        if(!Tools::getValue('bargain_text'))
            $this->errors[] = Tools::displayError('You can not add empty message.');

        if (!count($this->errors))
            return $this->quote->addQuoteBargain(pSQL($id_quote), pSQL(Tools::getValue('bargain_text')));
        else
            $this->context->smarty->assign('bargain_errors', $this->errors);
    }

    /**
     * Customer bargin submit
     */
    protected function bargainCustomerSubmit() {
        $action = Tools::getValue('actionSubmitBargain');

        if($this->quote->submitBargain(Tools::getValue('id_bargain'), $action, Tools::getValue('id_quote'))) {
            die(Tools::jsonEncode(array('submited' => $action)));
        }else
            die(Tools::jsonEncode(array('hasError' => true)));
    }

    /**
     * Rename quote
     */
    protected function quoteRename($id_quote = false) {
        if(Tools::getValue('quoteName')){
            $quoteName = Tools::getValue('quoteName');
            if(!Validate::isCatalogName($quoteName))
                die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Wrong quote name'))));
        }else
            die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Name is empty'))));

        if($this->quote->renameQuote(pSQL($id_quote), pSQL($quoteName))) {
            die(Tools::jsonEncode(array('renamed' => $quoteName)));
        }else
            die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Cannot rename quote'))));
    }


}