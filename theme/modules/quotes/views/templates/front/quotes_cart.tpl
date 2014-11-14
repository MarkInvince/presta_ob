{capture name=path}{l s='Ask for a Quote' mod='quotes'}{/capture}
{$isLogged}
{$isGuest}

<h2 class="page-heading">{l s='Your quotes cart' mod='quotes'}</h2>

{if isset($authentification_error)}
    <div class="alert alert-danger">
        {*{if {$authentification_error|@count} == 1}*}
        {*<p>{l s='There\'s at least one error'} :</p>*}
        {*{else}*}
        {*<p>{l s='There are %s errors' sprintf=[$account_error|@count]} :</p>*}
        {*{/if}*}
        <ol>
            {foreach from=$authentification_error item=v}
                <li>{$v}</li>
            {/foreach}
        </ol>
    </div>
{/if}

{if isset($quotesNumber) && $quotesNumber > 0}

    {include file="$tpl_path./quote_product_list.tpl"}

    {if isset($isLogged) && $isLogged == 1}
        <a id="qsubmitnow" class="button" href="{$base_dir}modules/askforaquote/frontoffice/askforaquote.php?gofinal=1" title="{l s='Submit without preview' mod='quotes'}">{l s='Submit now' mod='quotes'}</a>
    {else}
        {include file="$tpl_path./quotes_new_account.tpl"}
    {/if}

{else}
    <p class="alert alert-warning">{l s='No quotes' mod='quotes'}</p>
{/if}

{strip}
    {addJsDef authenticationUrl=$link->getPageLink("authentication", true)|escape:'quotes':'UTF-8'}
    {addJsDefL name=txtThereis}{l s='There is' js=1}{/addJsDefL}
    {addJsDefL name=txtErrors}{l s='Error(s)' js=1}{/addJsDefL}
    {addJsDef quoteCartUrl=$link->getModuleLink('quotes', 'QuotesCart', array(), true)|escape:'html':'UTF-8'}
    {addJsDef guestCheckoutEnabled=$PS_GUEST_QUOTES_ENABLED|intval}
    {addJsDef isGuest=$isGuest|intval}
    {addJsDef addressEnabled=$ADDRESS_ENABLED}
{/strip}