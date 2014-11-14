<?php

include_once(_PS_MODULE_DIR_ . 'quotes/classes/QuotesObj.php');

class quotesSubmitedQuotesModuleFrontController extends ModuleFrontController {

    public $ssl = true;
    public $display_column_left = false;
    public $quote_product;
    public $id_quote;
    public $id_customer;
    private $user_token;

    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();

        $this->quote = new QuotesObj;

        $this->id_quote = 0;

        $this->id_customer = (int)$this->context->cookie->id_customer;

    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(array(

        ));

    }

    public function postProcess() {

        if (!Tools::getValue('id_quote', 1))
            $this->id_quote = 0;
        else {
            $this->id_quote = Tools::getValue('id_quote');
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
            $this->context->smarty->assign('quote', $quoteInfo);

            $bargains = $this->quote->getQuoteInfo($this->id_quote);
            $this->context->smarty->assign('bargains', $bargains);
        } else // if 0 Get quotes list
        {
            $quotes = $this->quote->getQuotesByCustomer($this->id_customer);
            $this->context->smarty->assign('quotes', $quotes);
        }

        $this->context->smarty->assign(array(
            'id_quote' => $this->id_quote,
            'id_customer' => $this->id_customer
        ));

        $this->setTemplate('submited_quotes.tpl');
    }



}