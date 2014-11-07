{*
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*}
<!-- MODULE Request Offer top box --> 
{if $psVersion==1.6}
<link href="{$base_dir}modules/askforaquote/css/style16.css" rel="stylesheet" type="text/css" media="all" />
{else}
<link href="{$base_dir}modules/askforaquote/css/ro_top_box.css" rel="stylesheet" type="text/css" media="all" />
{/if}
<div id="topquotecontainer" class="clearfix col-sm-2">
    <a rel="nofollow" href="#" title="{l s='Submit Quotes' mod='askforaquote'}" class="topquotes" id="topquotes">
        <i class="icon-question-sign"></i>
        <strong>{l s='Quotes' mod='askforaquote'}:</strong>
        <span id="quote_quantity_top">{if isset($prdcts) AND $prdcts}{$prdcts|@count}{else}{l s='0' mod='askforaquote'}{/if}</span>
    </a>
    <script>
		jQuery('#topquotes').click(function() {
			$('#submitbox').click();
			return false;
		});
		$(document).ready(function() {
			$('#header .row .col-sm-4').removeClass('col-sm-4').addClass('col-sm-3');
        });
	</script>
    {if !isset($colmodexists)}
	<script type="text/javascript">
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
    <div id="quote_block" class="block exclusive top" style="display:none;">
        <div class="block_content clearfix" id="boxlist">
            {if isset($prdcts) AND $prdcts}{else}<p id="noquote">{l s='No quotes' mod='askforaquote'}</p>{/if}
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
            <div id="chckout" class="buttons_bottom_block">
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
    {/if}
</div>
<!-- /MODULE Request Offer top box -->