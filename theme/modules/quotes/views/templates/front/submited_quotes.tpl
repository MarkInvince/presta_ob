{capture name=path}{l s='Your quotes' mod='quotes'}{/capture}
{if $id_quote == 0}
    <h1 class="page-heading bottom-indent">{l s='Submited quotes' mod='quotes'}</h1>
    <p class="info-title">{l s='Here are the quotes you\'ve submited since your account was created.' mod='quotes'}</p>


    <div class="block-center" id="block-quotes">
            {*<pre>*}
                {*{print_r($quotes)}*}
            {*</pre>*}
        {if $quotes && count($quotes)}
            <table id="quotes-list" class="table table-bordered footab">
                <thead>
                <tr>
                    <th class="first_item" data-sort-ignore="true">{l s='Quote name'}</th>
                    <th class="item">{l s='Date' mod='quotes'}</th>
                    <th class="item">{l s='Total price' mod='quotes'}</th>
                    <th class="item">{l s='Bargain price' mod='quotes'}</th>
                    <th class="last_item">{l s='Details' mod='quotes'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$quotes item=quote}
                    <tr class="item">
                        <td data-value="{$quote.id_quote}" class="quote_name"><i class="icon-pencil"></i>{$quote.quote_name}</td>
                        <td data-value="{$quote.date_add|regex_replace:"/[\-\:\ ]/":""}" class="">
                            {dateFormat date=$quote.date_add full=0}
                        </td>
                        <td class="" data-value="{}">
                            <span class="price">
                                {$quote.price}
                            </span>
                        </td>
                        <td class="" data-value="{}">
                            <span class="price">
                                {$quote.price}
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

    {*<pre>*}
        {*{print_r($quote)}*}
    {*</pre>*}

    <h1 class="page-heading bottom-indent">{l s='Quote information' mod='quotes'}</h1>


    <table  class="table table-bordered footab">
        <tr>
            <th class="first_item" data-sort-ignore="true">{l s='Quote name'}</th>
            <th class="item">{l s='Date' mod='quotes'}</th>
            <th class="item">{l s='Total price' mod='quotes'}</th>
            <th class="item">{l s='Bargain price' mod='quotes'}</th>
        </tr>
        <tr class="item">
            <td data-value="{$quote.id_quote}" class="quote_name"><i class="icon-pencil"></i>{$quote.quote_name}</td>
            <td data-value="{$quote.date_add|regex_replace:"/[\-\:\ ]/":""}" class="">{dateFormat date=$quote.date_add full=0}</td>
            <td class="">
                <span class="price">
                    {$quote.price}
                </span>
            </td>
            <td class="">
                <span class="price">
                    {$quote.price}
                </span>
            </td>
        </tr>
    </table>

    <p><a href="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}" id="show_quote_products_info">&raquo; {l s='Click to show quote products info'}</a></p>



    <table id="quote_products_info" class="table table-bordered">
        <tr>
            <th class="quotes_cart_product first_item">{l s='Product' mod="quotes"}</th>
            <th class="quotes_cart_description item">{l s='Name' mod="quotes"}</th>
            <th class="quotes_cart_unit item">{l s='Unit price' mod="quotes"}</th>
            <th class="quotes_cart_quantity item">{l s='Qty' mod="quotes"}</th>
            <th class="quotes_cart_total item">{l s='Total' mod="quotes"}</th>
        </tr>
    {foreach from=$quote.products item=product}

        <tr id="product_{$product.id}_{$product.id_attribute}">
            <td class="quotes_cart_product">
                <a href="{$product.link|escape:'html':'UTF-8'}">
                    <img src="{$link->getImageLink($product.link_rewrite, $product.id_attribute, 'cart_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                </a>
            </td>
            <td class="quotes_cart_description">
                <p class="product-name">
                    <a href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
                </p>
            </td>
            <td class="quotes_cart_unit">
                {$product.price}
            </td>
            <td class="quotes_cart_quantity">
                {$product.quantity}
            </td>
            <td class="quotes_cart_total">
                {$product.price_total}
            </td>

        </tr>
    {/foreach}
    </table>


    <h1 class="page-heading bottom-indent">{l s='Quote bargains' mod='quotes'}</h1>
    {*<pre>*}
        {*{print_r($bargains)}*}
    {*</pre>*}

    {if $bargains && count($bargains) > 0}
        <ul class="bargains_list">
            {foreach from=$bargains item=bargain}
                {if $bargain.bargain_whos == 'customer'}
                    <li class="customer_bargain clearfix">
                        <div class="row">
                            <div class="bargain_heading clearfix">
                                <div class="date col-xs-9">
                                    <p class="bargain_whos">{l s='Your bargain message:' mod='quotes'}</p>
                                </div>
                                <div class="date col-xs-3">
                                    <strong>{l s='Added:' mod='quotes'}</strong> {$bargain.date_add}
                                </div>
                            </div>
                            <div class="bargain_message col-xs-12 box">{$bargain.bargain_text}</div>
                        </div>
                    </li>
                {else}
                    <li class="admin_bargain clearfix">
                        <div class="row">
                            <div class="bargain_heading clearfix">
                                <div class="date col-xs-9">
                                    <p class="bargain_whos">{l s='Administrator bargain message:' mod='quotes'}</p>
                                </div>
                                <div class="date col-xs-3">
                                    <strong>{l s='Added:' mod='quotes'}</strong> {$bargain.date_add}
                                </div>
                            </div>
                            {if $bargain.bargain_text}
                                <div class="bargain_message col-xs-12 box">{$bargain.bargain_text}</div>
                            {/if}
                            {if $bargain.bargain_price != 0}
                                <div class="col-xs-6 bargain_price_container clearfix">
                                    <table class="table">
                                        <tr>
                                            <td>{l s='Admins price' mod="quotes"}</td>
                                            <td class="price">{$bargain.bargain_price_display}</td>
                                        </tr>
                                        {if $bargain.bargain_price_text}
                                            <tr>
                                                <td>{l s='The offer' mod="quotes"}</td>
                                                <td>{$bargain.bargain_price_text}</td>
                                            </tr>
                                        {/if}
                                    </table>
                                    {if !$bargain.bargain_customer_confirm}
                                        <form  action="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}" method="post" class="burgainSubmitForm std">
                                            <fieldset>
                                                <a  data-action="reject" data-id="{$bargain.id_bargain}" data-quote="{$id_quote}" class="btn btn-default button button-medium rejectBargainOffer">
                                                    <span>{l s='Reject offer' mod='quotes'}</span>
                                                </a>
                                                <a  data-action="accept" data-id="{$bargain.id_bargain}" data-quote="{$id_quote}" class="btn btn-default button button-medium acceptBargainOffer">
                                                    <span>{l s='Accept offer' mod='quotes'}</span>
                                                </a>
                                            </fieldset>
                                        </form>
                                    {/if}
                                    <div class="bargain_alerts">
                                        <div id="success_bargain_{$bargain.id_bargain}" {if $bargain.bargain_customer_confirm == 1}style="display: block"{/if} class="alert alert-success">
                                            {l s='Bargain offer accepted' mod='quotes'}
                                        </div>
                                        <div id="reject_bargain_{$bargain.id_bargain}" {if $bargain.bargain_customer_confirm == 2}style="display: block"{/if} class="alert alert-warning">
                                            {l s='Bargain offer rejected' mod='quotes'}
                                        </div>
                                        <div id="danger_bargain_{$bargain.id_bargain}" class="alert alert-danger">
                                            {l s='Submit error, try again' mod='quotes'}
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </li>
                {/if}
            {/foreach}
        </ul>
    {else}
        <p class="alert alert-warning">{l s='There are no any bargains yet' mod='quotes'}</p>
    {/if}

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

{strip}
    {addJsDef submitedQuotes=$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}
{/strip}

