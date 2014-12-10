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

<!-- MODULE Quotes cart -->
<script type="text/javascript">
    var quotesCartEmpty  = '{l s="Your quotes cart is empty" mod="quotes"}';
</script>
{if $active_overlay == 0}
    <div class="clearfix col-sm-3">
        <div class="row quotes_cart">
            <a href="{$quotesCart|escape:'html':'UTF-8'}" rel="nofollow" id="quotes-cart-link">
                <b>{l s='Quotes' mod='quotes'}</b>
                <span class="ajax_cart_quantity{if $cartTotalProducts == 0} unvisible{/if}">{$cartTotalProducts|intval}</span>
                <span class="ajax_cart_product_txt{if $cartTotalProducts != 1} unvisible{/if}">{l s='Product' mod='quotes'}</span>
                <span class="ajax_cart_product_txt_s{if $cartTotalProducts < 2} unvisible{/if}">{l s='Products' mod='quotes'}</span>
                <span class="ajax_cart_no_product{if $cartTotalProducts > 0} unvisible{/if}">{l s='(empty)' mod='quotes'}</span>
            </a>
            <div class="col-sm-12 quotes_cart_block exclusive" id="box-body" style="display:none;">
                <div class="block_content">
                    <div class="row product-list" id="product-list">
                        {if $cartTotalProducts > 0}
                            <dl class="products" id="quotes-products">
                                {foreach $products as $key=>$product}
                                    {if is_numeric($key)}
                                        <dt class="item">
                                            <a class="cart-images" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.title|escape:'html':'UTF-8'}">
                                                <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')|escape:'html':'UTF-8'}" alt="{$product.title|escape:'html':'UTF-8'}">
                                            </a>
                                        <div class="cart-info">
                                            <div class="product-name">
                                                <span class="quantity-formated"><span class="quantity">{$product.quantity|intval}</span>&nbsp;x&nbsp;</span><a class="cart_block_product_name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.title|escape:'html':'UTF-8'}">{$product.title|truncate:20:'...'|escape:'html':'UTF-8'}</a>
                                            </div>
                                            <div class="product-attr">
                                                <small>{$product.combinations|escape:'html':'UTF-8'}</small>
                                            </div>
                                            <span class="price">
                                                {$product.unit_price|escape:'html':'UTF-8'}
                                            </span>
                                            <div class="remove-wrap">
                                                <hr/>
                                                <a href="javascript:void(0);" rel="{$product.id|intval}_{$product.id_attribute|intval}" class="remove-quote">{l s="Remove" mod='quotes'}</a>
                                            </div>
                                        </div>
                                        </dt>
                                    {/if}
                                {/foreach}
                            </dl>
                            <div class="quotes-cart-prices">
                                <div class="row">
                                    <span class="col-xs-12 col-lg-6 text-center">{l s="Total:" mod="quotes"}</span>
                                    <span class="col-xs-12 col-lg-6 text-center">{$cart.total|escape:'html':'UTF-8'}</span>

                                </div>
                            </div>
                        {else}
                            <div class="alert">
                                {l s="No products to quote" mod='quotes'}
                            </div>
                        {/if}
                    </div>
                    <p class="cart-buttons">
                        {if isset($isLogged) && $isLogged > 0}
                            <a id="button_order_cart" class="btn btn-default button button-small submit_quote" href="javascript:void(0);" title="{l s='Submit quote' mod='quotes'}" rel="nofollow">
                            <span>
                                {l s='Submit now' mod='quotes'}<i class="icon-chevron-right right"></i>
                            </span>
                            </a>
                        {else}
                            <a id="button_order_cart" class="btn btn-default button button-small" href="{$quotesCart|escape:'html':'UTF-8'}" title="{l s='Submit quote' mod='quotes'}" rel="nofollow">
                            <span>
                                {l s='Check out' mod='quotes'}<i class="icon-chevron-right right"></i>
                            </span>
                            </a>
                        {/if}
                    </p>
                </div>
            </div>
        </div>
    </div>

{elseif $active_overlay == 1}
	<div id="quotes_layer_cart">
		<div class="clearfix">
			<div class="quotes_layer_cart_product col-xs-12 col-md-6">
				<span class="cross" title="{l s='Close window' mod='quotes'}"></span>
				<h2>
					<i class="icon-ok-circle"></i>{l s='Product successfully added to your shopping cart' mod='quotes'}
				</h2>
			</div>
			<div class="quotes_layer_cart_cart col-xs-12 col-md-6">
				<br/>
				<hr/>
				<div class="button-container">
					<span class="continue btn btn-default button exclusive-medium" title="{l s='Continue shopping' mod='quotes'}">
						<span>
							<i class="icon-chevron-left left"></i>{l s='Continue shopping' mod='quotes'}
						</span>
					</span>
					{if $enablePopSubmit == 1}
						<a id="button_order_cart" class="btn btn-default button button-medium submit_quote" href="javascript:void(0);" title="{l s='Submit quote' mod='quotes'}" rel="nofollow">
							<span>
								{l s='Submit now' mod='quotes'}<i class="icon-chevron-right right"></i>
							</span>
						</a>
					{else}
						<a class="btn btn-default button button-medium"	href="{$link->getModuleLink('quotes','QuotesCart')|escape:'html':'UTF-8'}" title="{l s='Proceed to checkout' mod='quotes'}" rel="nofollow">
							<span>
								{l s='Proceed to checkout' mod='quotes'}<i class="icon-chevron-right right"></i>
							</span>
						</a>
					{/if}
				</div>
				<hr/>
			</div>
		</div>
		<div class="crossseling"></div>
	</div> <!-- #layer_cart -->
	<div class="quotes_layer_cart_overlay"></div>
{/if}
<!-- /MODULE Quotes cart -->
{strip}
	{addJsDef messagingEnabled=$MESSAGING_ENABLED|intval}
	{addJsDef quotesCart=$actionAddQuotes}
	{addJsDef catalogMode=$PS_CATALOG_MODE|intval}
{/strip}

{*<script type="text/javascript">*}
{*{if $PS_CATALOG_MODE}*}
{*var catalogMode = true;*}
{*{else}*}
{*var catalogMode = false;*}
{*{/if}*}
{*</script>*}