{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h1 class="page-heading bottom-indent">{l s='Quote information' mod='quotes'}</h1>

<p><a href="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}" id="show_quote_products_info">&raquo; {l s='Click to hide/show quote products info'}</a></p>

<table id="quote_products_info" class="table table-bordered">
    <tr>
        <th class="quotes_cart_product first_item">{l s='Product' mod="quotes"}</th>
        <th class="quotes_cart_description item">{l s='Name' mod="quotes"}</th>
        <th class="quotes_cart_unit item">{l s='Unit price' mod="quotes"}</th>
        <th class="quotes_cart_quantity item">{l s='Qty' mod="quotes"}</th>
        <th class="quotes_cart_total item">{l s='Total' mod="quotes"}</th>
    </tr>
    {foreach from=$quote.products item=product}

        <tr id="product_{$product.id|escape:'intval'}_{$product.id_attribute|escape:'intval'}">
            <td class="quotes_cart_product">
                <a href="{$product.link|escape:'html':'UTF-8'}">
                    <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                </a>
            </td>
            <td class="quotes_cart_description">
                <p class="product-name">
                    <a href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
                </p>
            </td>
            <td class="quotes_cart_unit">
                {$product.price|escape:'intval'}
            </td>
            <td class="quotes_cart_quantity">
                {$product.quantity|escape:'intval'}
            </td>
            <td class="quotes_cart_total">
                {$product.price_total|escape:'intval'}
            </td>

        </tr>
    {/foreach}
</table>

{if $MESSAGING_ENABLED}
    <form action="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}" method="post" id="client_bargain_txt" class="std">
        <input type="hidden" id="id_quote" name="id_quote" value="{$id_quote|escape:'html':'UTF-8'}" />
        <input type="hidden" name="action" value="addClientBargain" />
        <fieldset>
            <div class="box">
                <div id="success_bargain_message" class="alert alert-success">
                    {l s='Bargain message added' mod='quotes'}
                </div>
                <div id="errors_bargain_message" class="alert alert-danger"></div>
                <h3 class="page-subheading">{l s='New bargain message' mod='quotes'}</h3>
                <div class="form-group is_customer_param">
                    <textarea class="form-control" name="bargain_text" id="bargain_text" cols="26" rows="3"></textarea>
                </div>
                <button type="submit" name="addClientBargain" id="addClientBargain" class="btn btn-default button button-medium"><span>{l s='Send' mod='quotes'}<i class="icon-chevron-right right"></i></span></button>
            </div>
        </fieldset>
    </form>
{/if}

<h1 class="page-heading bottom-indent">{l s='Quote bargains' mod='quotes'}</h1>


<ul class="bargains_list">
    {if $bargains && count($bargains) > 0}
        {foreach from=$bargains item=bargain}
            {if $bargain.bargain_whos == 'customer'}
                <li class="customer_bargain clearfix">
                    <div class="row">
                        <div class="bargain_heading clearfix">
                            <div class="date col-xs-9">
                                <p class="bargain_whos">{l s='Your bargain message:' mod='quotes'}</p>
                            </div>
                            <div class="date col-xs-3">
                                <strong>{l s='Added:' mod='quotes'}</strong> {$bargain.date_add|escape:'html':'UTF-8'}
                            </div>
                        </div>
                        <div class="bargain_message col-xs-12 box">{$bargain.bargain_text|escape:'html':'UTF-8'}</div>
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
                                <strong>{l s='Added:' mod='quotes'}</strong> {$bargain.date_add|escape:'html':'UTF-8'}
                            </div>
                        </div>
                        {if $bargain.bargain_text}
                            <div class="bargain_message col-xs-12 box">{$bargain.bargain_text|escape:'html':'UTF-8'}</div>
                        {/if}
                        {if $bargain.bargain_price != 0}
                            <div class="col-xs-6 bargain_price_container clearfix">
                                <table class="table">
                                    <tr>
                                        <td>{l s='Admins price' mod="quotes"}</td>
                                        <td class="price">{$bargain.bargain_price_display|escape:'html':'UTF-8'}</td>
                                    </tr>
                                    {if $bargain.bargain_price_text}
                                        <tr>
                                            <td>{l s='The offer' mod="quotes"}</td>
                                            <td>{$bargain.bargain_price_text|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/if}
                                </table>
                                {if !$bargain.bargain_customer_confirm}
                                    <form  action="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}" method="post" class="burgainSubmitForm std">
                                        <fieldset>
                                            <a  data-action="reject" data-id="{$bargain.id_bargain}" data-quote="{$id_quote|escape:'intval'}" class="btn btn-default button button-medium rejectBargainOffer">
                                                <span>{l s='Reject offer' mod='quotes'}</span>
                                            </a>
                                            <a  data-action="accept" data-id="{$bargain.id_bargain}" data-quote="{$id_quote|escape:'intval'}" class="btn btn-default button button-medium acceptBargainOffer">
                                                <span>{l s='Accept offer' mod='quotes'}</span>
                                            </a>
                                        </fieldset>
                                    </form>
                                {/if}
                                <div class="bargain_alerts">
                                    <div id="success_bargain_{$bargain.id_bargain|escape:'intval'}" {if $bargain.bargain_customer_confirm == 1}style="display: block"{/if} class="alert alert-success">
                                        {l s='Bargain offer accepted' mod='quotes'}
                                    </div>
                                    <div id="reject_bargain_{$bargain.id_bargain|escape:'intval'}" {if $bargain.bargain_customer_confirm == 2}style="display: block"{/if} class="alert alert-warning">
                                        {l s='Bargain offer rejected' mod='quotes'}
                                    </div>
                                    <div id="danger_bargain_{$bargain.id_bargain|escape:'intval'}" class="alert alert-danger">
                                        {l s='Submit error, try again' mod='quotes'}
                                    </div>
                                    <div {if $quote.submited == 2}style="display: block"{/if} class="alert alert-success">
                                        {l s='Transformed to order' mod='quotes'}
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                </li>
            {/if}
        {/foreach}
    {else}
        <p class="alert alert-warning bargains_list_warning">{l s='There are no any bargains yet' mod='quotes'}</p>
    {/if}
</ul>