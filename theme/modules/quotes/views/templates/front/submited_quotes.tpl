

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
    <h1 class="page-heading bottom-indent">{l s='Quote information' mod='quotes'}</h1>

    <pre>
        {print_r($quote)}
    </pre>

    {$quote.name}

    <h1 class="page-heading bottom-indent">{l s='Quote bargain' mod='quotes'}</h1>

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

