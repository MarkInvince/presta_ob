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

<tr class="quote_item">
    <td class="text-center">{$quote.id_quote|intval}</td>
    <td class="text-center">{$quote.quote_name|escape:'html':'UTF-8'}</td>
    <td class="text-center"><a target="_blank" href="{$quote.customer.link|escape:'html':'UTF-8'}">{$quote.customer.name|escape:'html':'UTF-8'}</a></td>
    <td class="text-center">{count($quote.products)|intval}</td>
    <td class="text-center">{$quote.date_add|escape:'html':'UTF-8'}</td>
    <td class="text-center">{if $quote.submited == 1}<i class="icon-ok-circle color-green"></i>{elseif $quote.submited == 0}<i class="icon-remove color-red"></i>{else}<i class="icon-mail-forward color-green"></i>{/if}</td>
    <td class="text-center">
        <form action="{$index}" method="post" class="action_form">
            <input type="hidden" name="id_customer" value="{$quote.customer.id|intval}" />
            <input type="hidden" name="id_quote" value="{$quote.id_quote|intval}" />
            <input type="hidden" name="action" value="view" />
            <div class="btn-group">
                <button type="button" class="btn btn-default view_quote" >
                    <i class="icon-pencil"></i>
                    {l s="View" mod="quotes"}
                </button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="javascript:void(0);" rel="{$quote.id_quote|intval}_{$quote.customer.id|intval}" class="delete_quote">
                            <i class="icon-trash"></i>
                            {l s="Delete" mod="quotes"}
                        </a>
                    </li>
                </ul>
            </div>
        </form>
    </td>
</tr>