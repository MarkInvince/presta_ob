<div id="order-detail-content" class="table_block table-responsive">
    {if count($products) > 0}
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
            {foreach $products as $product}
                <tr id="product_{$product.id}_{$product.id_attribute}">
                    <td class="quotes_cart_product">
                        <a href="{$product.link|escape:'html':'UTF-8'}">
                            <img src="{$link->getImageLink($product.link_rewrite, $product.id, 'cart_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                        </a>
                    </td>
                    <td class="quotes_cart_description">
                        <p class="product-name">
                            <a href="{$product.link|escape:'html':'UTF-8'}">{$product.title|escape:'html':'UTF-8'}</a>
                        </p>
                    </td>
                    <td class="quotes_cart_unit">
                        {$product.price}
                    </td>
                    <td class="quotes_cart_quantity">
                        <input type="hidden" value="{$product.quantity}" name="quantity_{$product.id}_{$product.id_attribute}" />
                        <input size="2" type="text" autocomplete="off" class="cart_quantity_input form-control grey" value="{$product.quantity}"  name="quantity_{$product.id}_{$product.id_attribute}" />
                    </td>
                    <td class="quotes_cart_total">
                        {$product.price}
                    </td>
                    <td class="quotes_cart_delete">
                        <a href="javascript:void(0);" rel="{$product.id}_{$product.id_attribute}" class="remove_quote"><i class="icon-remove"></i></a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <p class="alert alert-warning">
            {l s="No quotes" mod="quotes"}
        </p>
    {/if}
</div>