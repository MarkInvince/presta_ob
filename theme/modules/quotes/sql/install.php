<?php

$sql = array();

/*$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes` (
    `id_quotes` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY  (`id_quotes`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';*/

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `id_shop` int(11) unsigned NOT NULL DEFAULT "1",
          `id_shop_group` int(11) unsigned NOT NULL DEFAULT "1",
          `id_lang` int(10) unsigned NOT NULL,
          `id_customer` int(10) unsigned NOT NULL,
          `id_guest` int(10) unsigned NOT NULL,
          `date_add` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes_product` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `id_quote` int(10) unsigned NOT NULL,
          `id_shop_group` int(11) unsigned NOT NULL DEFAULT "1",
          `id_product` int(10) unsigned NOT NULL,
          `quantity` int(10) unsigned NOT NULL DEFAULT "0",
          `date_add` datetime NOT NULL,
          KEY `cart_product_index` (`id_quote`,`id_product`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

foreach ($sql as $query)
    if (Db::getInstance()->execute($query) == false)
        return false;
