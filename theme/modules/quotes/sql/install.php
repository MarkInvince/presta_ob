<?php

$sql = array();

/*$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes` (
    `id_quotes` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY  (`id_quotes`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';*/

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes` (
          `id_request` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `id_shop` int(11) unsigned NOT NULL DEFAULT "1",
          `id_carrier` int(10) unsigned NOT NULL,
          `id_lang` int(10) unsigned NOT NULL,
          `id_customer` int(10) unsigned NOT NULL,
          `id_guest` int(10) unsigned NOT NULL,
          `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL,
          PRIMARY KEY (`id_request`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes_product` (
          `id_cart` int(10) unsigned NOT NULL,
          `id_product` int(10) unsigned NOT NULL,
          `id_shop` int(10) unsigned NOT NULL DEFAULT "1",
          `quantity` int(10) unsigned NOT NULL DEFAULT "0",
          `id_lang` int(10) unsigned NOT NULL,
          `id_customer` int(10) unsigned NOT NULL,
          `id_guest` int(10) unsigned NOT NULL,
          `date_add` datetime NOT NULL,
          KEY `cart_product_index` (`id_cart`,`id_product`),
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

foreach ($sql as $query)
    if (Db::getInstance()->execute($query) == false)
        return false;
