<?php

if (!defined('_PS_VERSION_'))
exit;

class QuotesCartCore extends ObjectModel
{
    public $id_request;
    public $id_shop_group;
    public $id_shop;
    public $id_lang;
    public $id_customer;
    public $id_guest;
    public $date_add;

    protected $_products = null;

    public static $definition = array(
        'table' => 'quotes',
        'primary' => 'id_request',
        'fields' => array(
            'id_shop_group' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_address_delivery' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_address_invoice' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_carrier' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_guest' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'recyclable' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift' => 					array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift_message' => 			array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
            'mobile_theme' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'delivery_option' => 		array('type' => self::TYPE_STRING),
            'secure_key' => 			array('type' => self::TYPE_STRING, 'size' => 32),
            'allow_seperated_package' =>array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );
};