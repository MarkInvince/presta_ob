{capture name=path}{l s='Ask for a Quote' mod='quotes'}{/capture}
<div class="block">
    <h4 class="title_block">
        {l s='Your quotes cart' mod='quotes'}
    </h4>
</div>
<div id="quotes-cart-wrapper">
    {if isset($products) && count($products) > 0}
        {include file="$tpl_path/quote_product_list.tpl"}
        <div class="clearfix">
            <form action="{$link->getModuleLink('quotes', 'QuotesCart', array(), true)|escape:'html':'UTF-8'}" method="post" class="std col-sm-4">
                {if $MESSAGING_ENABLED == 1}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="contact_via_mail" id="contact_via_mail"> {l s='Contact me via email' mod='quotes'}
                        </label>
                    </div>
                {else}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="contact_via_phone" id="contact_via_phone"> {l s='Contact me via telephone' mod='quotes'}
                        </label>
                    </div>
                    <div class="required form-group show_phone_field">
                        <label for="contact_phone">{l s='Enter your phone number'}</label>
                        <input type="text" class="text form-control validate" name="contact_phone" id="contact_phone" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile) && $guestInformations.phone_mobile}{$guestInformations.phone_mobile}{else}{$post.phone_mobile}{/if}" />
                    </div>
                {/if}
            </form>
        {if isset($isLogged) && $isLogged == 1 && count($products) > 0}
            <a class="btn btn-success submit_quote" href="javascript:void(0);" title="{l s='Submit now' mod='quotes'}">
            <span>
                {l s='Submit now' mod='quotes'}
                <i class="icon-chevron-right right"></i>
            </span>
            </a>
        </div>
        {else}
        </div>
            {include file="$tpl_path/quotes_new_account.tpl"}
        {/if}
        <div {if isset($userRegistry) && $userRegistry==1}style="display: block;"{/if} id="quote_account_saved" class="alert alert-success">
            {l s='Account information saved successfully' mod='quotes'}
        </div>
    {else}
        <p class="alert alert-warning">{l s='No quotes' mod='quotes'}</p>
    {/if}
</div>

{strip}
    {addJsDef authenticationUrl=$link->getPageLink("authentication", true)|escape:'quotes':'UTF-8'}
    {addJsDefL name=txtThereis}{l s='There is' js=1}{/addJsDefL}
    {addJsDefL name=txtErrors}{l s='Error(s)' js=1}{/addJsDefL}
    {addJsDef quoteCartUrl=$link->getModuleLink('quotes', 'QuotesCart', array(), true)|escape:'html':'UTF-8'}
    {addJsDef guestCheckoutEnabled=$PS_GUEST_QUOTES_ENABLED|intval}
    {addJsDef isGuest=$isGuest|intval}
    {addJsDef addressEnabled=$ADDRESS_ENABLED}
    {addJsDef messagingEnabled=$MESSAGING_ENABLED}
{/strip}