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
//
//        $sql = "SELECT * FROM `"._DB_PREFIX_."quotes` WHERE `id_customer` = ".$id_customer;
//
//        $result = Db::getInstance()->execute($sql);


        $quotes = array();

        for ($i=0; $i<5; $i++) {
            $quotes[$i]['id_quote'] = $i;
            $quotes[$i]['id_customer'] = 31;
            $quotes[$i]['date_add'] = time();
            $quotes[$i]['reference'] = Tools::passwdGen(10);
            $quotes[$i]['total_paid'] = rand(100, 9999);
            $quotes[$i]['id_currency'] = 1;
        }

        return $quotes;

    }

    public function getQuoteInfo($id_quote = false) {
        if (!$id_quote)
            return false;

        $quote = array(
            'id' => 1,
            'name' => 'dfhjedhfeg',
            'totalPrice' => '12330',
            'bargainPrice' => '12100',
            'reference' => '5445sd4s45',
            'products' => array(
                'id' => '1',
                'quantity' => '5'
            )
        );

        return $quote;
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
}