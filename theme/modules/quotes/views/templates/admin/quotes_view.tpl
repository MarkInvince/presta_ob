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

<script type="text/javascript">
    var confirmDelete   = '{l s="Are you sure you want delete?" mod="quotes"}';
    var adminQuotesUrl = "{$index|escape:'html':'UTF-8'}";
</script>
<div class="row panel">
    <h3><i class="icon-hand-right"></i> {l s='Quote request:' mod="quotes"} #{$quote[0]['id_quote']|intval}</h3>
    <br/>
    <div {if $quote[0]['submited'] != 2}style="display: none"{/if} class="alert alert-success">
        {l s='Transformed to order' mod='quotes'}
    </div>
    <div class="col-lg-12 panel admin-panel">
        <h3><i class="icon-user"></i> {l s='Requisites' mod="quotes"}</h3>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <table class="table">
                            <tr>
                                <td>{l s="Customer Name:" mod="quotes"}</td>
                                <td><strong>{$quote.customer.name|escape:'html':'UTF-8'}</strong></td>
                            </tr>
                            <tr>
                                <td>{l s="Gender:" mod="quotes"}</td>
                                <td><strong>{if $quote.customer.gender == 1}<i class="icon-male"></i>{elseif $quote.customer.gender == 2}<i class="icon-female"></i>{else}{l s="Not selected"}{/if}</strong></td>
                            </tr>
                            <tr>
                                <td>{l s="Email:" mod="quotes"}</td>
                                <td><strong><a href="mailto:{$quote.customer.email|escape:'html':'UTF-8'}">{$quote.customer.email|escape:'html':'UTF-8'}</a></strong></td>
                            </tr>
                            <tr>
                                <td>{l s="Birthday:" mod="quotes"}</td>
                                <td><strong>{if $quote.customer.birthday == '0000-00-00'}{l s="Not specified" mod="quotes"}{else}{$quote.customer.birthday|escape:'html':'UTF-8'}{/if}</strong></td>
                            </tr>
                            <tr>
                                <td>{l s="Registration date:" mod="quotes"}</td>
                                <td><strong>{$quote.customer.date_add|escape:'html':'UTF-8'}</strong></td>
                            </tr>
                        </table>
                        <br/>
                        <div class="text-right">
                            <a target="_blank" href="{$link->getAdminLink("AdminCustomers", true)|escape:'html':'UTF-8'}&id_customer={$quote.customer.id|intval}&updatecustomer" class="btn btn-default"><i class="icon-edit"></i> {l s="Edit" mod="quotes"}</a>
                        </div>
                    </div>
                    <div class="col-lg-1"></div>
                    <div class="col-lg-7">
                        {if count($quote.customer.addresses) > 0}
                            {foreach $quote.customer.addresses as $address}
                                <div class="panel panel-default">
                                    <div class="panel-heading">{$address.alias|escape:'html':'UTF-8'}</div>
                                    <div class="panel-body">
                                        <table class="table">
                                            <tr>
                                                <td>{l s="Company" mod="quotes"}</td>
                                                <td><strong>{if !empty($address.company)}{$address.company|escape:'html':'UTF-8'}{else}{l s="Not specified" mod="quotes"}{/if}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="First name, Last name" mod="quotes"}</td>
                                                <td><strong>{$address.firstname} {$address.lastname|escape:'html':'UTF-8'}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Region" mod="quotes"}</td>
                                                <td><strong>{$address.country}, {$address.state|escape:'html':'UTF-8'}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Address" mod="quotes"}</td>
                                                <td><strong>{$address.address1} {$address.address2|escape:'html':'UTF-8'} {$address.city|escape:'html':'UTF-8'}, {$address.postcode|escape:'html':'UTF-8'} </strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Phone" mod="quotes"}</td>
                                                <td><strong>{if !empty($address.phone)}{$address.phone|escape:'html':'UTF-8'}{else}{l s="Not specified" mod="quotes"}{/if}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Phone mobile" mod="quotes"}</td>
                                                <td><strong>{if !empty($address.phonemobile)}{$address.phonemobile|escape:'html':'UTF-8'}{else}{l s="Not specified" mod="quotes"}{/if}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Creation date" mod="quotes"}</td>
                                                <td><strong>{$address.date_add|escape:'html':'UTF-8'}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            {/foreach}
                        {else}
                            <div class="alert alert-warning">
                                {$quote.customer.name|escape:'html':'UTF-8'} {l s="has not registered any addresses yet"}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 panel">
        <h3><i class="icon-list-ul"></i> {l s='Products list' mod="quotes"}</h3>
        <table class="table">
            <thead>
            <tr>
                <td>{l s="ID" mod="quotes"}</td>
                <td>{l s="Name" mod="quotes"}</td>
                <td>{l s="Unit price" mod="quotes"}</td>
                <td>{l s="Quantity" mod="quotes"}</td>
                <td>{l s="Total" mod="quotes"}</td>
            </tr>
            </thead>
            {foreach $quote.products as $product}
                <tr>
                    <td>{$product.id|intval}</td>
                    <td>
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="{$product.link|escape:'html':'UTF-8'}" target="_blank">
                                    <div class="col-lg-2">
                                        <img src="{$product.image|escape:'html':'UTF-8'}" class="img-responsive" width="50" height="50" alt="{$product.name|escape:'html':'UTF-8'}" />
                                    </div>
                                    <div>{$product.name|escape:'html':'UTF-8'}</div>
                                    <small>{$product.attr|escape:'html':'UTF-8'}</small>
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>{$product.unit_price|escape:'html':'UTF-8'}</td>
                    <td>{$product.quantity|intval}</td>
                    <td>{$product.total|escape:'html':'UTF-8'}</td>
                </tr>
            {/foreach}
            <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><h4>{l s="Quote total:" mod="quotes"}</h4></td>
                <td><h4>{$quote.quote_total.quote_normal|escape:'html':'UTF-8'}<h4></td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="col-lg-12 panel">
        <form id="bargain_form" class="defaultForm form-horizontal AdminCustomers" action="{$index|escape:'html':'UTF-8'}" method="post">
            <input type="hidden" name="id_quote" value="{$id_quote|intval}"/>
            <input type="hidden" name="id_customer" value="{$id_customer|intval}">
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-user"></i> {l s='Bargain form' mod='quotes'}
                </div>
                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-lg-3 required">{l s='Bargain message' mod='quotes'}</label>
                        <div class="col-lg-4 ">
                            <textarea name="bargain_text" class="textarea-autosize" style=""></textarea>
                        </div>
                    </div>

                    {if {$quote[0]['submited']} == 0}
                        <div class="form-group">
                            <label class="control-label col-lg-3">{l s='Bargain price' mod='quotes'}</label>
                            <div class="input-group col-lg-2">
                                <span class="input-group-addon">{$currency|escape:'html':'UTF-8'}</span>
                                <input maxlength="14" name="bargain_price" id="bargain_price" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-3">{l s='The offer' mod='quotes'}</label>
                            <div class="col-lg-4 ">
                                <input type="text" name="bargain_price_text" id="bargain_price_text" value="" class="">
                            </div>
                        </div>
                    {/if}

                    <div class="form-group col-lg-7">
                        <button type="submit" id="addClientBargain" name="addClientBargain" class="btn btn-default pull-right">
                            <i class="process-icon-save"></i> {l s='Add' mod='quotes'}
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <!-- /.form-wrapper -->
            </div>
        </form>

    </div>

    <div class="col-lg-12 panel">
        <h3><i class="icon-list-ul"></i> {l s='Quote bargains' mod='quotes'}</h3>
        {if $bargains && count($bargains) > 0}
            <ul class="bargains_list">
                {foreach from=$bargains item=bargain}
                    {if $bargain.bargain_whos == 'customer'}
                        <li class="customer_bargain clearfix">
                            <div class="row">
                                <div class="bargain_heading clearfix">
                                    <div class="date col-xs-9">
                                        <p class="bargain_whos">{$quote.customer.name|escape:'html':'UTF-8'} {l s='bargain:' mod='quotes'}</p>
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
                                                <td>{l s='Your price offer' mod="quotes"}</td>
                                                <td class="price">{$bargain.bargain_price_display|escape:'html':'UTF-8'}</td>
                                            </tr>
                                            {if $bargain.bargain_price_text}
                                                <tr>
                                                    <td>{l s='The offer' mod="quotes"}</td>
                                                    <td>{$bargain.bargain_price_text|escape:'html':'UTF-8'}</td>
                                                </tr>
                                            {/if}
                                        </table>

                                        <div class="bargain_alerts">
                                            <div id="success_bargain_{$bargain.id_bargain|intval}"
                                                 {if $bargain.bargain_customer_confirm == 1}style="display: block"{/if}
                                                 class="alert alert-success">
                                                {l s='Bargain offer accepted' mod='quotes'}
                                            </div>
                                            <div id="reject_bargain_{$bargain.id_bargain|intval}"
                                                 {if $bargain.bargain_customer_confirm == 2}style="display: block"{/if}
                                                 class="alert alert-warning">
                                                {l s='Bargain offer rejected' mod='quotes'}
                                            </div>
                                            <div id="danger_bargain_{$bargain.id_bargain|intval}" class="alert alert-danger">
                                                {l s='Something wrong, try again' mod='quotes'}
                                            </div>
                                        </div>

                                    </div>
                                    {if !$bargain.bargain_customer_confirm}
                                        <div class="col-lg-1 bargain_action">
                                            <form  action="{$index|escape:'html':'UTF-8'}" method="post" class="burgainSubmitForm std">
                                                <a data-action="deleteBargain" data-id="{$bargain.id_bargain|intval}" class="btn btn-default deleteBargainOffer">
                                                    <i class="icon-trash"></i><span> {l s='Delete' mod='quotes'}</span>
                                                </a>
                                            </form>
                                        </div>
                                    {elseif $bargain.bargain_customer_confirm == 1}
                                        {if $quote[0]['submited'] != 2}
                                            <div class="col-lg-3 bargain_action">
                                                <form  action="{$index|escape:'html':'UTF-8'}" method="post" class="burgainSubmitForm std">
                                                    <input type="hidden" name="id_quote" value="{$id_quote|intval}"/>
                                                    <input type="hidden" name="id_customer" value="{$id_customer|intval}">
                                                    <input type="hidden" name="total_products" value="{$quote.quote_total.quote_static|escape:'html':'UTF-8'}">
                                                    <input type="hidden" name="bargain_price" value="{$bargain.bargain_price|escape:'html':'UTF-8'}">
                                                    <input type="hidden" name="id_cart" value="{$quote[0]['id_cart']|intval}">
                                                    <button type="submit" name="transformQuote" class="btn btn-primary">
                                                        {l s='Transform quote to order' mod='quotes'}
                                                    </button>
                                                </form>
                                            </div>
                                        {/if}
                                    {/if}
                                {/if}
                            </div>
                        </li>
                    {/if}
                {/foreach}
            </ul>
        {else}
            <p class="alert alert-warning">{l s='There are no any bargains yet' mod='quotes'}</p>
        {/if}
    </div>


    <div class="panel-footer">
        <button onclick="javascript:history.go(-1);" class="btn btn-default pull-right"><i
                    class="icon-chevron-left"></i> {l s="Back"}</button>
    </div>
