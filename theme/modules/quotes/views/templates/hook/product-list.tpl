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

{if $present_on_product_list == 1}
    {if $categories}
        {if in_array($category->id, $categories)}
            <div class="ask_offer clearfix {if $filtered_on_status == 1 && (isset($product.available_for_order) && $product.available_for_order)}unvisible{/if}">
                <form class="quote_ask_form" action="{$actionAddQuotes|escape:'html':'UTF-8'}" method="post">
                    <input type="hidden" name="action" value="add" />
                    <input type="hidden" name="ajax" value="true" />
                    <input type="hidden" name="pid" value="{$product.id_product|intval}" />
                    <input type="hidden" name="ipa" class="ipa" value="" />
                    <input type="hidden" class="pqty" name="pqty" value="1" />
                    <input type="hidden" class="product_list_opt" name="product_list_opt" value="1" />
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
    {else}
        <div class="ask_offer clearfix {if $filtered_on_status == 1 && (isset($product.available_for_order) && $product.available_for_order)}unvisible{/if}">
            <form class="quote_ask_form" action="{$actionAddQuotes|escape:'html':'UTF-8'}" method="post">
                <input type="hidden" name="action" value="add" />
                <input type="hidden" name="ajax" value="true" />
                <input type="hidden" name="pid" value="{$product.id_product|intval}" />
                <input type="hidden" name="ipa" class="ipa" value="" />
                <input type="hidden" class="pqty" name="pqty" value="1" />
                <input type="hidden" class="product_list_opt" name="product_list_opt" value="1" />
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

{/if}