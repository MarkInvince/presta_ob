<div class="ask_offer clearfix">
    <form id="quote_ask_form" action="{$actionAddQuotes}" method="post">
        <input type="hidden" name="action" value="add" />
        <input type="hidden" name="ajax" value="true" />
        <input type="hidden" name="pid" value="{$product->id|intval}" />
		{if $PS_CATALOG_MODE}
    	    <label for="quantity_wanted_ask">{l s='Quantity' mod='quotes'}:</label>
            <input type="hidden" name="catalog_mode" value="1" />
    		<input type="text" name="pqty" id="pqty" value="1" size="2" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="3" />
		{else}
			<input type="hidden" id="pqty" name="pqty" value="1" />
		{/if}
        
        {if isset($enableAnimation) AND $enableAnimation}
            <button class="fly_to_quote_cart_button btn btn-primary">
				<span>{l s='Ask for a quote' mod='quotes'}</span>
			</button>
        {else}
            <a class="ajax_add_to_quote_cart_button btn btn-primary" title="{l s='Ask for a quote' mod='quotes'}" >
				<span>{l s='Ask for a quote' mod='quotes'}</span>
			</a>
        {/if}
    </form>
</div>