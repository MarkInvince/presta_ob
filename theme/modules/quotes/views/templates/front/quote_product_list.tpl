{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="order-detail-content" class="table_block table-responsive">
    <table id="quotes_cart_summary" class="table table-bordered ">
        <thead>
        <tr>
            <th class="quotes_cart_product first_item">{l s='Product' mod="quotes"}</th>
            <th class="quotes_cart_description item">{l s='Description' mod="quotes"}</th>
            <th class="quotes_cart_unit item">{l s='Unit price' mod="quotes"}</th>
            <th class="quotes_cart_quantity item">{l s='Qty' mod="quotes"}</th>
            <th class="quotes_cart_total item">{l s='Total' mod="quotes"}</th>
            <th class="quotes_cart_delete last_item">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        {foreach $products as $key=>$product}
            {if is_numeric($key)}
                <tr id="product_{$product.id}_{$product.id_attribute}">
                    <td class="quotes_cart_product">
                        <a href="{$product.link|escape:'html':'UTF-8'}">
                            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')|escape:'html':'UTF-8'}" alt="{$product.title|escape:'html':'UTF-8'}" />
                        </a>
                    </td>
                    <td class="quotes_cart_description">
                        <p class="product-name">
                            <a href="{$product.link|escape:'html':'UTF-8'}">{$product.title|escape:'html':'UTF-8'}</a>
                        </p>
                    </td>
                    <td class="quotes_cart_unit">
                        {$product.unit_price}
                    </td>
                    <td class="quotes_cart_quantity">
                        <div class="row">
                            <div class="col-lg-8">
                                <input size="3" maxlength="3" rel="{$product.id}_{$product.id_attribute}" type="text" onkeypress="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')" onkeyup="if(this.value.match(/\D/)) this.value=this.value.replace(/\D/g,'')" autocomplete="off" class="cart_quantity_input form-control grey" value="{$product.quantity}"  name="quantity_{$product.id}_{$product.id_attribute}" />
                            </div>
                            <div class="col-lg-2">
                                <div class="quantity-block">
                                    <a href="javascript:void(0);" class="quote-plus-button btn btn-default" rel="{$product.id}_{$product.id_attribute}"><i class="icon-chevron-up"></i></a>
                                    <a href="javascript:void(0);" class="quote-minus-button btn btn-default" rel="{$product.id}_{$product.id_attribute}"><i class="icon-chevron-down"></i></a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="quotes_cart_total">
                        {$product.total_price}
                    </td>
                    <td class="quotes_cart_delete">
                        <a href="javascript:void(0);" rel="{$product.id}_{$product.id_attribute}" class="remove_quote"><i class="icon-remove"></i></a>
                    </td>
                </tr>
            {/if}
        {/foreach}
        <tr class="quote_row_total">
            <td><h5>{l s="Cart total:"}</h5></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{$cart.total}</td>
        </tr>
        </tbody>
    </table>
</div>