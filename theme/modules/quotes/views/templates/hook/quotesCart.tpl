<!-- MODULE Quotes cart -->
<script type="text/javascript">
    var quotesCart = "{$actionAddQuotes}";
    var catalogMode = "{$catalogMode}";
</script>
<div class="clearfix col-sm-3">
    <div class="row quotes_cart">
        <a href="{$quotesCart}" rel="nofollow" id="quotes-cart-link">
            <b>{l s='Quotes' mod='quotes'}</b>
            <span class="ajax_cart_quantity{if $cartTotalProducts == 0} unvisible{/if}">{$cartTotalProducts}</span>
            <span class="ajax_cart_product_txt{if $cartTotalProducts != 1} unvisible{/if}">{l s='Product' mod='quotes'}</span>
            <span class="ajax_cart_product_txt_s{if $cartTotalProducts < 2} unvisible{/if}">{l s='Products' mod='quotes'}</span>
            <span class="ajax_cart_no_product{if $cartTotalProducts > 0} unvisible{/if}">{l s='(empty)' mod='quotes'}</span>
        </a>
        <div class="col-sm-12 quotes_cart_block exclusive" id="box-body" style="display:none;">
            <div class="block_content">
                <div class="row product-list" id="product-list">
                    {if $cartTotalProducts > 0}
                        <dl class="products" id="quotes-products">
                        {foreach from=$products item='product' name='myLoop'}
                            {assign var='productId' value=$product.id}
                            <dt class="item">
                                <a class="cart-images" href="{$product.link}" title="{$product.title}">
                                    <img src="{$link->getImageLink($product.link_rewrite, $product.id_image['id_image'], 'cart_default')}" alt="{$product.title}">
                                </a>
                                <div class="cart-info">
                                    <div class="product-name">
										<span class="quantity-formated"><span class="quantity">{$product.quantity}</span>&nbsp;x&nbsp;</span><a class="cart_block_product_name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.title|escape:'html':'UTF-8'}">{$product.title|truncate:20:'...'|escape:'html':'UTF-8'}</a>
									</div>
									<span class="price">
                                        {$product.price}
                                    </span>
                                    <div class="remove-wrap">
                                        <hr/>
                                        <a href="javascript:void(0);" rel="{$product.id}_{$product.id_attribute}" class="remove-quote">{l s="Remove"}</a>
                                    </div>
                                </div>
                            </dt>
                        {/foreach}
                        </dl>
                    {else}
                        <div class="alert">
                            {l s="No products to quote"}
                        </div>
                    {/if}
                </div>
                <p class="cart-buttons">
                    <a id="button_order_cart" class="btn btn-default button button-small" href="{$quotesCart}" title="{l s='Submit quote' mod='quotes'}" rel="nofollow">
				    <span>
						{l s='Check out' mod='quotes'}<i class="icon-chevron-right right"></i>
					</span>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- /MODULE Quotes cart -->