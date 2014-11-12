<!-- MODULE Quotes cart -->
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
                    {else}
                        <div class="alert">
                            {l s="No products to quote"}
                        </div>
                    {/if}
                </div>
                <p class="cart-buttons">
                    <a id="button_order_cart" class="btn btn-default button button-small" href="{$quotesCart}" title="{l s='Check out' mod='quotes'}" rel="nofollow">
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