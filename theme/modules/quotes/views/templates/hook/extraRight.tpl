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
{if $present_on_product == 1}
    <div class="ask_offer clearfix {if $filtered_on_status == 1 && $product->available_for_order}unvisible{/if}">
        <form class="quote_ask_form" action="{$actionAddQuotes}" method="post">
            <input type="hidden" name="action" value="add" />
            <input type="hidden" name="ajax" value="true" />
            <input type="hidden" name="pid" value="{$product->id|intval}" />
            <input type="hidden" name="ipa" class="ipa" value="" />
            {if $PS_CATALOG_MODE}
                <label for="quantity_wanted_ask">{l s='Quantity' mod='quotes'}:</label>
                <input type="hidden" name="catalog_mode" value="1" />
                <input type="text" name="pqty" class="pqty" value="1" size="2" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="3" />
            {else}
                <input type="hidden" class="pqty" name="pqty" value="1" />
            {/if}

            {if isset($enableAnimation) AND $enableAnimation}
                <button class="fly_to_quote_cart_button btn btn-primary">
                    <span>{l s='Ask for a quote' mod='quotes'}</span>
                </button>
            {else}
                <a class="ajax_add_to_quote_cart_button"  title="{l s='Ask for a quote' mod='quotes'}" >
                    <span>{l s='Ask for a quote' mod='quotes'}</span>
                </a>
            {/if}
        </form>
    </div>
{/if}