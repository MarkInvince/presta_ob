{*
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*}
<!-- MODULE Request Offer -->
<script type="text/javascript">    
    {if isset($product) && $product}
        var product_name = "{$product->name|replace:'"':''|replace:'\'':''}";
    {else}    
        var product_name = "";
    {/if}
    var rLtitle = "{l s='remove this product from list' mod='askforaquote'}";
    var rOcheckOut = "{$base_dir}{$asklink}";
    var txtNoQuote = "{l s='No quotes' mod='askforaquote'}";
	$(document).ready(function() {
		var rowCount = document.getElementById('requestTable').getElementsByTagName('tr').length;
		if (rowCount <1){ 
			$('#submitbox').prop('disabled', true).removeClass('exclusive').addClass('exclusive_disabled');
			$('#qsubmitnow').css('display','none');
		}
	});
</script>    
<div id="quote_block" class="block exclusive">
	<h4>{l s='Ask for a Quote' mod='askforaquote'}</h4>
	<div class="block_content clearfix" id="boxlist">
            <p id="noquote">{if isset($prdcts) AND $prdcts}{else}{l s='No quotes' mod='askforaquote'}{/if}</p>
            <table cellpadding="0" cellspacing="0" border="0" width="100%" id="requestTable">
            {if isset($prdcts) AND $prdcts}
			{assign var=i value=0}
                {foreach from=$prdcts item=prod}
				{assign var=i value=$i+1}
                    <tr id="trr_{$prod->prodcode}">
                        <td valign="top" class="quote_qty" id="qr_{$prod->prodcode}">{$prod->cant}
						</td>
                          <td class="prod">x&nbsp;<a href="{$link->getProductLink($prod->id, $prod->link_rewrite, $prod->id_category_default)|escape:'htmlall':'UTF-8'}">{$prod->name[$cookie->id_lang]}</a>
							  <input type="hidden" id="at_{$prod->prodcode}" value="{$prod->attrib}" />
							 <br /><span style="font-size:10px"><i>{$prod->textattribs}</i></span>
						  </td>
                        <td width="16px"><span class='remove_link'><a style="margin-left:10px" rel="nofollow" class="ajax_cart_block_remove_link" href="#" onclick='return removeReq(this.parentNode.parentNode.parentNode.rowIndex, {$prod->prodcode})' title="{l s='remove this product from list' mod='askforaquote'}"><img src="{$base_dir}modules/askforaquote/img/delete.gif" /></a></span></td>
                    </tr>
                {/foreach}
            {/if}            
            </table>
            <div id="chckout" class="buttons_bottom_block clearfix">
            {if isset($isLogged) AND $isLogged == 1}
                <a id="qsubmitnow" class="button" href="{$base_dir}modules/askforaquote/frontoffice/askforaquote.php?gofinal=1" title="{l s='Submit without preview' mod='askforaquote'}"{if ($version == 1.4)} style="float:none"{/if}>{l s='Submit now' mod='askforaquote'}</a>
                <form method="post" action="{$base_dir}modules/askforaquote/frontoffice/askforaquote.php">
                	<input type="submit" name="submitbox" id="submitbox" title="{l s='View detailed list before submit' mod='askforaquote'}" value="{l s='View list' mod='askforaquote'}" class="button exclusive" />
                </form>
            {else}
                <form method="post" action="{$base_dir}modules/askforaquote/frontoffice/askforaquote.php">
                	<input type="submit" name="submitbox" id="submitbox" value="{l s='Submit Quote' mod='askforaquote'}" class="button exclusive" />
                </form>
            {/if}
            </div>
	</div>
</div>