<?php

if (!defined('_PS_VERSION_'))
    exit;

class QuotesObj
{

    /*
    public $id;
    public $id_quote;
    public $id_shop;
    public $id_shop_group;
    public $id_lang;
    public $id_guest;
    public $id_customer;
    public $id_product;
    public $quantity;
    public $date_add;
    public $date_upd;


    public static $definition = array(
        'table' => 'quotes_product',
        'primary' => 'id',
        'fields' => array(
            'id_quote' => array('type' => self::TYPE_STRING, 'required' => true),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );


    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id);
        if (!is_null($id_lang))
            $this->id_lang = (int)(Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');

    }
    */


    public function getQuotesByCustomer($id_customer)
    {
        if (!$id_customer)
            return false;

        $sql = "SELECT * FROM `"._DB_PREFIX_."quotes` WHERE `id_customer` = ".$id_customer;

        return $result = Db::getInstance()->executeS($sql);

    }

    public function getQuoteInfo($id_quote = false) {
        if (!$id_quote)
            return false;

        $sql = "SELECT * FROM `"._DB_PREFIX_."quotes` WHERE `id_quote` = ".$id_quote;

        return $result = Db::getInstance()->executeS($sql);
    }

    public function getBargains($id_quote = false) {
        if (!$id_quote)
            return false;
        $sql = "SELECT * FROM `"._DB_PREFIX_."quotes_bargains` WHERE `id_quote`=".$id_quote." ORDER BY `id_bargain` ASC";


        return $result = Db::getInstance()->executeS($sql);
    }

    public function addQuoteBargain($id_quote = false, $text, $whos = 'customer', $price = 0, $price_text = '') {
        if (!$id_quote)
            return false;
        $date_add = date('Y-m-d H:i:s', time());
        $sql = "INSERT INTO `"._DB_PREFIX_."quotes_bargains` SET
                    `id_quote` = ".$id_quote.",
                    `bargain_whos` = '".$whos."',
                    `bargain_text` = '".$text."',
                    `date_add` = '".$date_add."',
                    `bargain_price` = ".$price.",
                    `bargain_price_text` = '".$price_text."',
                    `bargain_customer_confirm` = 0
        ";

        return $result = Db::getInstance()->execute($sql);
    }

    public function submitBargain($id_bargain = false, $action, $id_quote) {
        if (!$id_bargain)
            return false;

        if($action == 'reject') {
            $sql = "UPDATE `"._DB_PREFIX_."quotes_bargains` SET
                    `bargain_customer_confirm` = 2
                        WHERE `id_bargain`=".$id_bargain;
            return Db::getInstance()->execute($sql);
        }
        elseif($action == 'accept') {
            $sql = "UPDATE `"._DB_PREFIX_."quotes_bargains` SET
                `bargain_customer_confirm` = 1
                    WHERE `id_bargain`=".$id_bargain;
            if(Db::getInstance()->execute($sql)) {
                $sql = "UPDATE `"._DB_PREFIX_."quotes` SET
                `submited` = 1
                    WHERE `id_quote`=".$id_quote;
                if(Db::getInstance()->execute($sql))
                    return true;
            }
        }

        return false;
    }
}