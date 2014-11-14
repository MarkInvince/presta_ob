<?php

if (!defined('_PS_VERSION_'))
    exit;

class QuotesSubmitCore extends ObjectModel
{
    public $id_quote;
    public $quote_name;
    public $id_shop;
    public $id_shop_group;
    public $id_lang;
    public $id_customer;
    public $products;
    public $burgain_price;
    public $date_add;
    public $submited;

    public static $definition = array(
        'table' => 'quotes',
        'primary' => 'id_quote',
        'fields' => array(
            'quote_name'    => 	array('type' => self::TYPE_STRING,  'validate' => 'isAnything'),
            'id_shop'       => 	array('type' => self::TYPE_INT,     'validate' => 'isUnsignedId'),
            'id_shop_group' => 	array('type' => self::TYPE_INT,     'validate' => 'isUnsignedId'),
            'id_lang'       => 	array('type' => self::TYPE_INT,     'validate' => 'isUnsignedId'),
            'id_customer'   => 	array('type' => self::TYPE_INT,     'validate' => 'isUnsignedId'),
            'products'      => 	array('type' => self::TYPE_STRING,  'validate' => 'isAnything'),
            'burgain_price' => 	array('type' => self::TYPE_INT,     'validate' => 'isUnsignedId'),
            'date_add'      => 	array('type' => self::TYPE_DATE,    'validate' => 'isDateFormat'),
            'submited'      => 	array('type' => self::TYPE_INT,     'validate' => 'isUnsignedId'),
        ),
    );

    public function __construct($id_quote = null, $id_lang = null)
    {
        parent::__construct($id_quote);

        $this->context = Context::getContext();
        if (!is_null($id_lang))
            $this->id_lang = (int)(Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');
        $this->id_shop = (int)$this->context->shop->id;
        $this->id_shop_group = (int)$this->context->shop->id_shop_group;
        $this->id_customer = (int)$this->context->customer->id;
        $this->date_add = date('Y-m-d H:i:s', time());
        $this->quote_name = strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
        $this->submited = 0;
    }

    public function add($autodate = true, $null_values = false )
    {
        return parent::add($autodate);
    }

    public function update($null_values = false)
    {
        $return = parent::update();
        return $return;
    }

    public function delete()
    {
        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'quotes` WHERE `id_quote` = '.(int)$this->id_quote))
            return false;

        return parent::delete();
    }
}