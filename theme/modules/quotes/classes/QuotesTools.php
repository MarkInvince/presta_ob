<?php
require('../../../config/config.inc.php');

function getProductAttributeImage($id_product, $id_product_attribute, $id_lang) {
    $mysql = '  SELECT pa.`id_product_attribute` , pa.`id_product` , pa.`price` , pac.`id_attribute` , al.`name` , paimg.`id_image`
                FROM  `ps_product_attribute` pa
                LEFT JOIN  `ps_product_attribute_combination` pac ON ( pa.`id_product_attribute` = pac.`id_product_attribute` )
                LEFT JOIN  `ps_product_attribute_image` paimg ON ( pac.`id_product_attribute` = paimg.`id_product_attribute` )
                LEFT JOIN  `ps_attribute` a ON ( pac.`id_attribute` = a.`id_attribute` )
                LEFT JOIN  `ps_attribute_lang` al ON ( al.`id_attribute` = a.`id_attribute` )
                WHERE pa.`id_product_attribute` = '.$id_product_attribute.'
                AND pa.`id_product` = '.$id_product.'
                AND  `id_lang` = '.$id_lang.'
                ORDER BY a.`id_attribute`
                LIMIT 1';
    $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($mysql);
    if(!$row)
        return array();
    return $row;
}