<?php

class QuotesTransform extends ParentOrderController
{
    public function init()
    {
        global $orderTotal;

        parent::init();

        // Check minimal amount
        //$currency = Currency::getCurrency((int)$this->context->cart->id_currency);

        $orderTotal = $this->context->cart->getOrderTotal();
        //$minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);



    }









}