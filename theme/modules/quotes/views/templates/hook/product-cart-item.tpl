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
*}{
if count($products) > 0}
    <dl class="products" id="quotes-products">
        {foreach $products as $key=>$product}
            {if is_numeric($key)}
                {assign var='productId' value=$product.id}
                <dt class="item">
                    <a class="cart-images" href="{$product.link}" title="{$product.title}">
                        <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')}" alt="{$product.title}">
                    </a>
                <div class="cart-info">
                    <div class="product-name">
                        <span class="quantity-formated"><span class="quantity">{$product.quantity}</span>&nbsp;x&nbsp;</span><a class="cart_block_product_name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.title|escape:'html':'UTF-8'}">{$product.title|truncate:20:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                        <span class="price">
                            {$product.unit_price}
                        </span>
                    <div class="remove-wrap">
                        <hr/>
                        <a href="javascript:void(0);" rel="{$product.id}_{$product.id_attribute}" class="remove-quote">{l s="Remove"}</a>
                    </div>
                </div>
                </dt>
            {/if}
        {/foreach}
    </dl>
    <div class="quotes-cart-prices">
        <div class="row">
            <span class="col-xs-12 col-lg-6 text-center">{l s="Total:" mod="quotes"}</span>
            <span class="col-xs-12 col-lg-6 text-center">{$cart.total}</span>

        </div>
    </div>
{else}
    <div class="alert">
        {l s="No products to quote" mod="quotes"}
    </div>
{/if}