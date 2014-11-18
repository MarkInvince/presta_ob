<?php

function getProductAttributeImage($id_product, $id_product_attribute, $id_lang) {
    $mysql = '  SELECT pa.`id_product_attribute` , pa.`id_product` , pa.`price` , pac.`id_attribute` , al.`name` , paimg.`id_image`
                FROM  `ps_product_attribute` pa
                LEFT JOIN  `ps_product_attribute_combination` pac ON ( pa.`id_product_attribute` = pac.`id_product_attribute` )
                LEFT JOIN  `ps_product_attribute_image` paimg ON ( pac.`id_product_attribute` = paimg.`id_product_attribute` )
                LEFT JOIN  `ps_attribute` a ON ( pac.`id_attribute` = a.`id_attribute` )
                LEFT JOIN  `ps_attribute_lang` al ON ( al.`id_attribute` = a.`id_attribute` )
                WHERE pa.`id_product_attribute` = '.pSQL($id_product_attribute).'
                AND pa.`id_product` = '.pSQL($id_product).'
                AND  `id_lang` = '.pSQL($id_lang).' ORDER BY pa.`id_product_attribute` LIMIT 1';
    $row = Db::getInstance()->executeS($mysql);
    if(!$row)
        return array();
    return $row[0]['id_image'];
}