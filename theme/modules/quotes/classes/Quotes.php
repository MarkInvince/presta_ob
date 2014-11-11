<?php

if (!defined('_PS_VERSION_'))
exit;

class QuotesCart extends ObjectModel
{
    public $id;
    public $id_shop_group;
    public $id_shop;
    public $id_lang;
    public $id_customer;
    public $id_guest;
    public $date_add;
    public $secure_key;

    protected $_products = null;

    public static $definition = array(
        'table' => 'quotes',
        'primary' => 'id',
        'fields' => array(
            'id_shop_group' => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId'),
            'id_shop'       => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId'),
            'id_customer'   => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId'),
            'id_guest'      => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId'),
            'id_lang'       => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId', 'required' => true),
            'date_add'      => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id);

        if (!is_null($id_lang))
            $this->id_lang = (int)(Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');

        if ($this->id_customer)
        {
            if (isset(Context::getContext()->customer) && Context::getContext()->customer->id == $this->id_customer)
                $customer = Context::getContext()->customer;
            else
                $customer = new Customer((int)$this->id_customer);
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        if (!$this->id_lang)
            $this->id_lang = Configuration::get('PS_LANG_DEFAULT');
        if (!$this->id_shop)
            $this->id_shop = Context::getContext()->shop->id;

        $return = parent::add($autodate);
        return $return;
    }

    public function update($null_values = false)
    {
        if (isset(self::$_nbProducts[$this->id]))
            unset(self::$_nbProducts[$this->id]);

        if (isset(self::$_totalWeight[$this->id]))
            unset(self::$_totalWeight[$this->id]);

        $this->_products = null;
        $return = parent::update();
        return $return;
    }

    public function delete()
    {

        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'quotes_product` WHERE `id` = '.(int)$this->id))
            return false;

        return parent::delete();
    }
};