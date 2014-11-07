<div class="ask_offer clearfix">
    <form id="quote_ask_form" action="{$actionAddQuotes}" method="post">
        <input type="hidden" name="plink" value="{$plink}" />
        <input type="hidden" name="pid" value="{$product->id|intval}" />
		<input type="hidden" name="pname" value="{$product->name|escape:'htmlall':'UTF-8'}" />
		{if $PS_CATALOG_MODE}
    	    <label for="quantity_wanted_ask">{l s='Quantity' mod='quotes'}:</label>
            <input type="hidden" name="catalog_mode" value="1" />
    		<input type="text" name="pqty" value="1" size="2" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="3" />
		{else}
			<input type="hidden" name="pqty" value="1" />
		{/if}
        
        {if isset($enableAnimation) AND $enableAnimation}
            <button class="fly_to_quote_cart_button btn btn-primary">
				<span>{l s='Ask for a quote' mod='quotes'}</span>
			</button>
        {else}
            <a class="ajax_add_to_quote_cart_button btn btn-primary" href="{$link->getPageLink('cart',false, NULL, "add=1&id_product={$product->id|intval}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product->id|intval}">
				<span>{l s='Ask for a quote' mod='quotes'}</span>
			</a>
        {/if}
    </form>
</div>