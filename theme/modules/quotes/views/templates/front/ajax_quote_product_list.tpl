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
{if isset($products) && count($products) > 0}
    {include file="$tpl_path/quote_product_list.tpl"}
    {if isset($isLogged) && $isLogged == 1 && count($products) > 0}
        <a class="btn btn-success submit_quote" href="javascript:void(0);" title="{l s='Submit now' mod='quotes'}">
            <span>
                {l s='Submit now' mod='quotes'}
                <i class="icon-chevron-right right"></i>
            </span>
        </a>
    {else}
        {include file="$tpl_path/quotes_new_account.tpl"}
    {/if}
    <div {if isset($userRegistry) && $userRegistry==1}style="display: block;"{/if} id="quote_account_saved" class="alert alert-success">
        {l s='Account information saved successfully' mod='quotes'}
    </div>
{else}
    <p class="alert alert-warning">{l s='No quotes' mod='quotes'}</p>
{/if}