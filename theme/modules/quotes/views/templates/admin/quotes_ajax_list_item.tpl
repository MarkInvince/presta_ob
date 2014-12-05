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

<h3><i class="icon-legal"></i> {l s='Current quotes list' mod="quotes"} <span class="badge">{$totalQuotes|escape:'intval'}</span></h3>
{if $totalQuotes < 1}
    <div class="alert alert-warning">{l s='No quotes found' mod="quotes"}</div>
{else}
    <table class="table">
        <thead>
        <tr>
            <td class="text-center">{l s="ID" mod="quotes"}</td>
            <td class="text-center">{l s="Quote name" mod="quotes"}</td>
            <td class="text-center">{l s="Customer" mod="quotes"}</td>
            <td class="text-center">{l s="Total Products" mod="quotes"}</td>
            <td class="text-center">{l s="Date add" mod="quotes"}</td>
            <td class="text-center">{l s="Status" mod="quotes"}</td>
            <td class="text-center"><i class="icon-cogs"></i></td>
        </tr>
        </thead>
        <tbody id="quotes_list">
        {foreach $quotes as $quote}
            {include file="$tpl_dir./quotes_list_item.tpl"}
        {/foreach}
        </tbody>
    </table>
{/if}