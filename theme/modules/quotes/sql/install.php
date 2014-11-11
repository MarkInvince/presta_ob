<?php

$sql = array();

/*$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes` (
    `id_quotes` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY  (`id_quotes`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';*/

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'quotes_product` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `id_quote` varchar(100) NOT NULL,
          `id_shop` int(11) unsigned NOT NULL DEFAULT "1",
          `id_shop_group` int(11) unsigned NOT NULL DEFAULT "1",
          `id_lang` int(11) unsigned NOT NULL DEFAULT "1",
          `id_product` int(10) unsigned NOT NULL,
          `id_curreny` int(10) unsigned NOT NULL,
          `id_guest` int(11) unsigned NOT NULL DEFAULT "0",
          `id_customer` int(11) unsigned NOT NULL DEFAULT "0",
          `quantity` int(10) unsigned NOT NULL DEFAULT "0",
          `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL DEFAULT "0",
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

foreach ($sql as $query)
    if (Db::getInstance()->execute($query) == false)
        return false;
