<?php

if (!defined('_PS_VERSION_'))
    exit;

class QuotesProductCart extends ObjectModel
{
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

    private $operator = 'up';
    private $addquantity = 1;

    public function setOperator($operator) {
        $this->operator = $operator;
    }
    public function setQuantity($quantity) {
        $this->addquantity = $quantity;
    }
    public static $definition = array(
        'table' => 'quotes_product',
        'primary' => 'id',
        'fields' => array(
            'id_quote'      => 	array('type' => self::TYPE_STRING, 'required' => true),
            'id_shop'       => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId'),
            'id_shop_group' => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId'),
            'id_lang'       => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId'),
            'id_product'    => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId', 'required' => true),
            'id_guest'      => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId', 'required' => true),
            'id_customer'   => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId', 'required' => true),
            'quantity'      => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId', 'required' => true),
            'date_add'      => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd'      => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id);
        if (!is_null($id_lang))
            $this->id_lang = (int)(Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');

        $this->context = Context::getContext();

    }

    public function add($autodate = true, $null_values = false )
    {
        if (!$this->id_lang)
            $this->id_lang = Configuration::get('PS_LANG_DEFAULT');
        if (!$this->id_shop)
            $this->id_shop = $this->context->shop->id;
        if(!$this->checkForContains())
            $return = parent::add($autodate);
        else {
            $return = $this->recountProduct();
        }
        return $return;
    }

    public function update($null_values = false)
    {
        $this->_products = null;
        $return = parent::update();
        return $return;
    }

    public function delete()
    {
        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'quotes_product` WHERE `id` = '.(int)$this->id.' AND `id_quote` LIKE "'.$this->id_quote.'"'))
            return false;

        return parent::delete();
    }

    public function checkForContains() {
        if (!$this->id_quote)
            return false;
        $result = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'quotes_product` qp
			WHERE qp.`id_product` = '.(int)$this->id_product.' AND qp.`id_quote` LIKE "'.$this->id_quote.'"'
        );
        if(!empty($result))
            return true;
        else
            return false;
    }
    public function recountProduct() {
        if (!$this->id_product || !$this->id_quote) {
            return false;
        }

        $row = Db::getInstance()->getRow('
			SELECT qp.`quantity`
			FROM `'._DB_PREFIX_.'quotes_product` qp
			WHERE qp.`id_product` = '.(int)$this->id_product.' AND qp.`id_quote` LIKE "'.$this->id_quote.'"'
        );

        $current_qty = (int)$row['quantity'];
        $id_product = (int)$id_product;
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $shop->id);

        if (!Validate::isLoadedObject($product))
            die(Tools::displayError());

        if ((int)$current_qty <= 0)
            return $this->deleteProduct($id_product);
        elseif (!$product->available_for_order)
            return false;
        else
        {
            switch($this->operator) {
                case 'up':
                    $current_qty = $current_qty + (int)$this->addquantity;
                    break;
                case 'down':
                    $current_qty = $current_qty - (int)$this->addquantity;
                    break;
                default:
                    break;
            }

            if ((int)$current_qty <= 0)
                return $this->deleteProduct($id_product);

            //update current product in cart
            $update = Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'cart_product`
					SET `quantity` = `quantity` '.$current_qty.', `date_upd` = NOW()
					WHERE `id_product` = '.(int)$id_product. ' AND `id_quote` = '.(int)$this->id_quote.'
					LIMIT 1'
            );
            return $update;
        }
    }

    /**
     * Return cart products
     *
     * @result array Products
     */
    public function getProducts()
    {
        if (!$this->id_quote)
            return array();

        $products_ids = array();
        $products = array();
        $result = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'quotes_product` WHERE `id_quote` = '.(int)$this->id_quote);
        if (empty($result))
            return array();

        foreach ($result as $row) {
            $products_ids[] = $row['id_product'];
            $product = array();
            $p_obj = new Product($row['id_product'], true, $this->context->language->id);
            if (Validate::isLoadedObject($p_obj)) {
                $product['id'] = $p_obj->id;
                $product['title'] = $p_obj->name;
                $product['quantity'] = $row['quantity'];
                $product['price'] = Tools::ps_round(Product::getPriceStatic($p_obj->id, true, NULL, 6),2);
                $products[] = $product;
            }
        }

        if(!empty($products_ids))
            Product::cacheProductsFeatures($products_ids);

        return $products;
    }

    public function deleteProduct($id_product)
    {
        /* Product deletion */
        $result = Db::getInstance()->execute('
		DELETE FROM `' . _DB_PREFIX_ . 'quotes_product`
		WHERE `id_product` = ' . (int)$id_product . ' AND `id_quote` = ' . (int)$this->id_quote);

        if ($result) {
            $this->update(true);
            return $this->getProducts();
        }
        return false;
    }
}