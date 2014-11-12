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

    public function add($quantity, $operator)
    {
        if (!$this->id_lang)
            $this->id_lang = Configuration::get('PS_LANG_DEFAULT');
        if (!$this->id_shop)
            $this->id_shop = $this->context->shop->id;
        if(!$this->checkForContains())
            $return = parent::add($autodate);
        else {
            $return = $this->recountProduct($quantity, $operator);
        }
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
    public function recountProduct($quantity, $operator) {
        if (!$this->id_product || !$this->id_quote)
            return false;
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
        elseif (!$product->available_for_order || Configuration::get('PS_CATALOG_MODE'))
            return false;
        else
        {
            switch($operator) {
                case 'up':
                    $current_qty = $current_qty + (int)$quantity;
                    break;
                case 'down':
                    $current_qty = $current_qty - (int)$quantity;
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
        if (!$this->id)
            return array();

        $products_ids = array();
        $products = array();
        $result = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'quotes_product` WHERE `id_quote` = '.(int)$this->id_quote);
        if (empty($result))
            return array();

        foreach ($result as $row) {
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
        if (isset(self::$_nbProducts[$this->id]))
            unset(self::$_nbProducts[$this->id]);

        if (isset(self::$_totalWeight[$this->id]))
            unset(self::$_totalWeight[$this->id]);

        /* Product deletion */
        $result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'quotes_product`
		WHERE `id_product` = '.(int)$id_product.' AND `id_quote` = '.(int)$this->id_quote);

        if ($result)
        {
            $return = $this->update(true);
            // refresh cache of self::_products
            return $this->getProducts();
        }
        return false;
    }

    public function updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', Shop $shop = null, $auto_add_cart_rule = true)
    {
        if (!$shop)
            $shop = Context::getContext()->shop;

        $quantity = (int)$quantity;
        $id_product = (int)$id_product;
        $id_product_attribute = (int)$id_product_attribute;
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $shop->id);

        if ($id_product_attribute)
        {
            $combination = new Combination((int)$id_product_attribute);
            if ($combination->id_product != $id_product)
                return false;
        }

        /* If we have a product combination, the minimal quantity is set with the one of this combination */
        if (!empty($id_product_attribute))
            $minimal_quantity = (int)Attribute::getAttributeMinimalQty($id_product_attribute);
        else
            $minimal_quantity = (int)$product->minimal_quantity;

        if (!Validate::isLoadedObject($product))
            die(Tools::displayError());

        if (isset(self::$_nbProducts[$this->id]))
            unset(self::$_nbProducts[$this->id]);

        if (isset(self::$_totalWeight[$this->id]))
            unset(self::$_totalWeight[$this->id]);

        if ((int)$quantity <= 0)
            return $this->deleteProduct($id_product, $id_product_attribute, (int)$id_customization);
        elseif (!$product->available_for_order || Configuration::get('PS_CATALOG_MODE'))
            return false;
        else
        {
            /* Check if the product is already in the cart */
            $result = $this->containsProduct($id_product, $id_product_attribute, (int)$id_customization);

            /* Update quantity if product already exist */
            if ($result)
            {
                if ($operator == 'up')
                {
                    $sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
							FROM '._DB_PREFIX_.'product p
							'.Product::sqlStock('p', $id_product_attribute, true, $shop).'
							WHERE p.id_product = '.$id_product;

                    $result2 = Db::getInstance()->getRow($sql);
                    $product_qty = (int)$result2['quantity'];
                    // Quantity for product pack
                    if (Pack::isPack($id_product))
                        $product_qty = Pack::getQuantity($id_product, $id_product_attribute);
                    $new_qty = (int)$result['quantity'] + (int)$quantity;
                    $qty = '+ '.(int)$quantity;

                    if (!Product::isAvailableWhenOutOfStock((int)$result2['out_of_stock']))
                        if ($new_qty > $product_qty)
                            return false;
                }
                else if ($operator == 'down')
                {
                    $qty = '- '.(int)$quantity;
                    $new_qty = (int)$result['quantity'] - (int)$quantity;
                    if ($new_qty < $minimal_quantity && $minimal_quantity > 1)
                        return -1;
                }
                else
                    return false;

                /* Delete product from cart */
                if ($new_qty <= 0)
                    return $this->deleteProduct((int)$id_product, (int)$id_product_attribute, (int)$id_customization);
                else if ($new_qty < $minimal_quantity)
                    return -1;
                else
                    Db::getInstance()->execute('
						UPDATE `'._DB_PREFIX_.'quotes_product`
						SET `quantity` = `quantity` '.$qty.', `date_add` = NOW()
						WHERE `id_product` = '.(int)$id_product.
                        (!empty($id_product_attribute) ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
						AND `id` = '.(int)$this->id.' LIMIT 1'
                    );
            }
            /* Add product to the cart */
            elseif ($operator == 'up')
            {
                $sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
						FROM '._DB_PREFIX_.'product p
						'.Product::sqlStock('p', $id_product_attribute, true, $shop).'
						WHERE p.id_product = '.$id_product;

                $result2 = Db::getInstance()->getRow($sql);

                // Quantity for product pack
                if (Pack::isPack($id_product))
                    $result2['quantity'] = Pack::getQuantity($id_product, $id_product_attribute);

                if (!Product::isAvailableWhenOutOfStock((int)$result2['out_of_stock']))
                    if ((int)$quantity > $result2['quantity'])
                        return false;

                if ((int)$quantity < $minimal_quantity)
                    return -1;

                $result_add = Db::getInstance()->insert('quotes_product', array(
                    'id_product' => 			(int)$id_product,
                    'id_product_attribute' => 	(int)$id_product_attribute,
                    'id_cart' => 				(int)$this->id,
                    'id_shop' => 				$shop->id,
                    'quantity' => 				(int)$quantity,
                    'date_add' => 				date('Y-m-d H:i:s')
                ));

                if (!$result_add)
                    return false;
            }
        }

        // refresh cache of self::_products
        $this->_products = $this->getProducts(true);
        $this->update(true);
        $context = Context::getContext()->cloneContext();
        $context->cart = $this;
        Cache::clean('getContextualValue_*');

        if ($product->customizable)
            return $this->_updateCustomizationQuantity((int)$quantity, (int)$id_customization, (int)$id_product, (int)$id_product_attribute, $operator);
        else
            return true;
    }
};