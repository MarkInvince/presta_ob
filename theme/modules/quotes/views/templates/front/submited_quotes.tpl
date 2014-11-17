

{$id_customer}
{capture name=path}{l s='Your quotes' mod='quotes'}{/capture}
{if $id_quote == 0}
    <h1 class="page-heading bottom-indent">{l s='Submited quotes' mod='quotes'}</h1>
    <p class="info-title">{l s='Here are the quotes you\'ve submited since your account was created.' mod='quotes'}</p>


    <div class="block-center" id="block-quotes">
            <pre>
                {print_r($quotes)}
            </pre>
        {if $quotes && count($quotes)}
            <table id="quotes-list" class="table table-bordered footab">
                <thead>
                <tr>
                    <th class="first_item" data-sort-ignore="true">{l s='Quote reference'}</th>
                    <th class="item">{l s='Date' mod='quotes'}</th>
                    <th class="item">{l s='Total price' mod='quotes'}</th>
                    <th class="item">{l s='Bargain price' mod='quotes'}</th>
                    <th class="last_item">{l s='Details' mod='quotes'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$quotes item=quote name=myLoop}
                    <tr class="item">
                        <td class="">
                            {$quote.reference}
                        </td>
                        <td data-value="{$quote.date_add|regex_replace:"/[\-\:\ ]/":""}" class="">
                            {dateFormat date=$quote.date_add full=0}
                        </td>
                        <td class="" data-value="{$quote.total_paid}">
                                <span class="price">
                                    {displayPrice price=$quote.total_paid currency=$quote.id_currency no_utf8=false convert=false}
                                </span>
                        </td>
                        <td class="" data-value="{$quote.total_paid}">
                                <span class="price">
                                    {displayPrice price=$quote.total_paid currency=$quote.id_currency no_utf8=false convert=false}
                                </span>
                        </td>
                        <td>
                            <a href="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}?id_quote={$quote.id_quote}">{l s='See bargains' mod='quotes'}</a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            <div id="block-order-detail" class="unvisible">&nbsp;</div>
        {else}
            <p class="alert alert-warning">{l s='You have not placed any quotes yet.' mod='quotes'}</p>
        {/if}
    </div>

    <ul class="footer_links clearfix">
        <li>
            <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='quotes'}
			</span>
            </a>
        </li>
        <li>
            <a class="btn btn-default button button-small" href="{$base_dir}">
                <span><i class="icon-chevron-left"></i> {l s='Home' mod='quotes'}</span>
            </a>
        </li>
    </ul>
{else}

    <h1 class="page-heading">{$quote.name}</h1>

    <h1 class="page-heading bottom-indent">{l s='Quote information' mod='quotes'}</h1>

    <pre>
        {print_r($quote)}
    </pre>



    <table id="quote_info" class="table table-bordered ">
        <thead>
        <tr>
            <th class="quotes_cart_product first_item">{l s='Product' mod="quotes"}</th>
            <th class="quotes_cart_description item">{l s='Description' mod="quotes"}</th>
            <th class="quotes_cart_unit item">{l s='Unit price' mod="quotes"}</th>
            <th class="quotes_cart_quantity item">{l s='Qty' mod="quotes"}</th>
            <th class="quotes_cart_total item">{l s='Total' mod="quotes"}</th>
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

    <h1 class="page-heading bottom-indent">{l s='Quote bargains' mod='quotes'}</h1>

    <p>Messagin - {$MESSAGING_ENABLED}</p>

    <pre>
        {print_r($bargains)}
    </pre>
    <ul class="bargains_list">
        {foreach from=$bargains item=bargain}
            {if $bargain.bargain_whos == 'customer'}
                <li class="customer_bargain">
                    <div class="date col-xs-3">
                        {$bargain.date_add}
                    </div>
                    <div class="col-xs-9">{$bargain.bargain_text}</div>
                </li>
            {/if}
        {/foreach}
        <li>

        </li>
    </ul>

    {if $MESSAGING_ENABLED}
        <form action="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}" method="post" id="client_bargain_txt" class="std">
            <input type="hidden" id="id_quote" name="id_quote" value="{$id_quote}" />
            <fieldset>
                <div class="box">
                    {if isset($bargain_errors)}
                        <div class="alert alert-danger">
                            <ol>
                                {foreach from=$bargain_errors item=v}
                                    <li>{$v}</li>
                                {/foreach}
                            </ol>
                        </div>
                    {/if}
                    <h3 class="page-subheading">{l s='New bargain message' mod='quotes'}</h3>
                    <div class="form-group is_customer_param">
                        <label for="other_invoice">{l s='Additional information' mod='quotes'}</label>
                        <textarea class="form-control" name="bargain_text" id="bargain_text" cols="26" rows="3"></textarea>
                    </div>
                    <button type="submit" name="addClientBargain" id="addClientBargain" class="btn btn-default button button-medium"><span>{l s='Send' mod='quotes'}<i class="icon-chevron-right right"></i></span></button>
                </div>

            </fieldset>
        </form>
    {/if}

    <ul class="footer_links clearfix">
        <li>
            <a class="btn btn-default button button-small" href="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to quotes list' mod='quotes'}
			</span>
            </a>
        </li>
    </ul>

{/if}

