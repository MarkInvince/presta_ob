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

{capture name=path}{l s='Your quotes' mod='quotes'}{/capture}
<h1 class="page-heading bottom-indent">{l s='Submited quotes' mod='quotes'}</h1>
<p class="info-title">{l s='Here are the quotes you\'ve submited since your account was created.' mod='quotes'}</p>

<div class="block-center" id="block-quotes">
    {if $quotes && count($quotes)}
        <div class="panel">
            <ul class="list-group">
                <li class="list-group-item"><i class="icon-remove color-red btn"></i> {l s="Not submited quotes" mod="quotes"}</li>
                <li class="list-group-item"><i class="icon-ok-circle btn color-green"></i> {l s="Submited quotes" mod="quotes"}</li>
                <li class="list-group-item"><i class="icon-mail-forward btn color-green2"></i> {l s="Submited and transorm into prestashop order quotes" mod="quotes"}</li>
                <li class="list-group-item"><i class="icon-pencil btn"></i> {l s="Click to edit quote name" mod="quotes"}</li>
            </ul>
        </div>

        <table id="quotes-list" class="table table-bordered footab">
            <thead>
            <tr>
                <th class="first_item">{l s='Reference' mod='quotes'}</th>
                <th class="item">{l s='Quote name' mod='quotes'}</th>
                <th class="item">{l s='Date' mod='quotes'}</th>
                <th class="item">{l s='Total price' mod='quotes'}</th>
                <th class="item">{l s='Quote bargains' mod='quotes'}</th>
                <th class="item text-center">{l s='Status' mod='quotes'}</th>
                <th class="last_item">{l s='Bargains' mod='quotes'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$quotes item=quote}
                <tr class="item quote_{$quote.id_quote|escape:'intval'}">
                    <td>{$quote.reference|escape:'html':'UTF-8'}</td>
                    <td data-value="{$quote.id_quote|escape:'html':'UTF-8'}" class="quote_name"><i class="icon-pencil"></i>{$quote.quote_name|escape:'html':'UTF-8'}</td>
                    <td data-value="{$quote.date_add|regex_replace:"/[\-\:\ ]/":""}" class="">
                        {dateFormat date=$quote.date_add full=0}
                    </td>
                    <td>
                        <span class="price">
                            {$quote.price|escape:'html':'UTF-8'}
                        </span>
                    </td>
                    <td>
                        <span class="price">
                            {if $quote.bargain_price ==0}
                                --
                            {else}
                                {l  s='Current offer:' mod='quotes'} <span class="color-green2">{$quote.bargain_price|escape:'html':'UTF-8'}</span>
                            {/if}
                        </span>
                    </td>
                    <td class="text-center">{if $quote.submited == 1}<i class="icon-ok-circle color-green"></i>{elseif $quote.submited == 0}<i class="icon-remove color-red"></i>{else}<i class="icon-mail-forward color-green2"></i>{/if}</td>
                    <td class="table_link">
                        <a class="show_quote_details" data-id="{$quote.id_quote|escape:'html':'UTF-8'}" href="{$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}"><i class="icon-eye-open"></i> {l s='view' mod='quotes'}</a>
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
        <a class="btn btn-default button button-small" href="{$base_dir|escape:'html':'UTF-8'}">
            <span><i class="icon-chevron-left"></i> {l s='Home' mod='quotes'}</span>
        </a>
    </li>
</ul>

{strip}
    {addJsDef submitedQuotes=$link->getModuleLink('quotes', 'SubmitedQuotes', array(), true)|escape:'html':'UTF-8'}
    {addJsDefL name=your_msg}{l s='Your bargain message:' mod='quotes' js=1|escape:'html':'UTF-8'}{/addJsDefL}
    {addJsDefL name=added}{l s='Added:' mod='quotes' js=1|escape:'html':'UTF-8'}{/addJsDefL}
{/strip}