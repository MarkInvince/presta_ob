{*
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*}
{if ($version == "1.6")}
<link href="{$base_dir}modules/askforaquote/css/style16.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript">
jQuery(document).ready(function(){
    $('.breadcrumb').append("<span class='navigation-pipe'>{$navigationPipe}</span><span class='navigation_page'> {l s='Submit Quote' mod='askforaquote'}</span>");
});
</script>
{else}
<link href="{$base_dir}modules/askforaquote/css/summary.css" rel="stylesheet" type="text/css" media="all" />
{/if}
<script type="text/javascript" src="{$base_dir}modules/askforaquote/js/order-opc-quote.js"></script> 
<script type="text/javascript" src="{$base_dir}modules/askforaquote/js/jquery.scrollTo-1.4.2-min.js"></script>
<script type="text/javascript" src="{$base_dir}modules/askforaquote/js/statesManagement.js"></script> 
{if ($version == "1.4")}
<link href="{$base_dir_ssl}css/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="{$base_dir_ssl}js/jquery/jquery.fancybox-1.3.4.js"></script>
{else}
<link href="{$base_dir_ssl}js/jquery/plugins/fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="{$base_dir_ssl}js/jquery/plugins/fancybox/jquery.fancybox.js"></script>
{/if}
<script type="text/javascript">
	var baseDir = '{$base_dir_ssl}';
    var addresses = new Array();
	var isLogged = {$isLogged|intval};
	var guestCheckoutEnabled = {$PS_GUEST_CHECKOUT_ENABLED|intval};
	var countries = new Array();
	var countriesNeedIDNumber = new Array();
	var countriesNeedZipCode = new Array();
    var isGuest = {$isGuest|intval};
    var authenticationUrl = '{$link->getPageLink("authentication.php", true)}';
    var txtWithTax = "{l s='(tax incl.)' mod='askforaquote'}";
	var txtWithoutTax = "{l s='(tax excl.)' mod='askforaquote'}";
	var txtTOSIsAccepted = "{l s='Terms of service is accepted' mod='askforaquote'}";
	var txtTOSIsNotAccepted = "{l s='Terms of service have not been accepted' mod='askforaquote'}";
	var txtThereis = "{l s='There is' mod='askforaquote'}";
	var txtErrors = "{l s='error(s)' mod='askforaquote'}";
	var txtDeliveryAddress = "{l s='Delivery address' mod='askforaquote'}";
	var txtInvoiceAddress = "{l s='Invoice address' mod='askforaquote'}";
	var txtModifyMyAddress = "{l s='Modify my address' mod='askforaquote'}";
	var txtInstantCheckout = "{l s='Instant checkout' mod='askforaquote'}";
	var txtNoMoreReq = "{l s='No quotes' mod='askforaquote'}";
	var txtReqH2 = "{l s='Request offers' mod='askforaquote'}";
    var txtsuccessReq ="{l s='Quotes successfully submitted!' mod='askforaquote'}";
    var txtsuccessBack ="{l s='Back to homepage' mod='askforaquote'}";
    var txtNoQuote = "{l s='No products to quote.' mod='askforaquote'}";
    $('document').ready( function() {
        if(document.getElementById('invoice_address')) document.getElementById('invoice_address').style.display='none';
        if(document.getElementById('opc_invoice_address')) document.getElementById('opc_invoice_address').style.display='none';
        var p_list = document.getElementsByTagName("b");
        for(var i=p_list.length-1; i>=0; i--){
            var p = p_list[i];           
            p.parentNode.removeChild(p);        
        }
    	$('a.iframe').fancybox();
    });
</script>
{if $psVersion!=1.6}
{capture name=path}{l s='Quotes' mod='askforaquote'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<div id="req_main">
{if $prdcts|@count != 0}
		<h1 class="page-heading">{l s='Submit your Quote' mod='askforaquote'}&nbsp;</h1>
        <p class="numberinfo">{l s='Your quote list contains' mod='askforaquote'} <span id="summary_products_quantity">{$prdcts|@count} {if $prdcts|@count == 1}{l s='product' mod='askforaquote'}{else}{l s='products' mod='askforaquote'}{/if}</span></p>
        <div id="order-detail-content" class="table_block">
	        <table id="cart_summary" class="std">
		        <thead>
			        <tr>
				        <th class="first_item" colspan="2">{l s='Product' mod='askforaquote'}</th>
						<th class="item"  align="left" width="50">{l s='Qty.' mod='askforaquote'}</th>
				        <th class="last_item" align="center" width="50">{l s='Del.' mod='askforaquote'}</th>
			        </tr>
		        </thead>
		        <tbody>
		        {foreach from=$prdcts item=product name=productLoop}
			        {assign var='productId' value=$product.id_product}
			        {assign var='quantityDisplayed' value=0}
			        {* Display the product line *}			        
                    <tr id="product_{$product.prodcode}" class="{if $smarty.foreach.productLoop.last}last_item{elseif $smarty.foreach.productLoop.first}first_item{/if} cart_item">
	                    <td class="cart_product">
		                    <a href="{$link->getProductLink($product.id_product, $product.link_rewrite)}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')}" onerror="this.src='{$link->getImageLink($product.link_rewrite, $product.id_image, 'small')}';" alt="{$product.name|escape:'htmlall':'UTF-8'}" {if isset($smallSize)}width="{$smallSize.width}" height="{$smallSize.height}" {/if} /></a>
	                    </td>
	                    <td class="cart_description">
		                    <h5><a href="{$link->getProductLink($product.id_product, $product.link_rewrite)}">{$product.name|escape:'htmlall':'UTF-8'}</a></h5>
		                    {if !empty($product.attributes)}
                                {foreach from=$product.attributes item=attribute name=attributeLoop}
                                    {$attribute.attribute_group}: {$attribute.name}<br>
                                {/foreach}
				    		{/if}
                            {if !empty($product.reference)}
                            	{l s='Reff:' mod='askforaquote'} {$product.reference}
                            {/if}
	                    </td>
                        <td class="cart_quantity" align="left">
							<input type="text" value="{$product.qty}" onkeyup="return modifyqty{if ($version != 1.4)}Sub{/if}({$customerid},{$product.prodcode},this.value)" size="5" id="qtyof_{$product.prodcode}" class="form-control" />
                            <div id="update_{$product.prodcode}" class="updatedbox" style="display:none;"></div>
						</td>
						<td class="cart_remove">
                            <span class="remove_link"><a style="background-image: url('{$base_dir}modules/askforaquote/img/delete.gif')" rel="nofollow" class="ajax_cart_block_remove_link" href="#" onclick='return removeReq(this.parentNode.parentNode.parentNode.rowIndex, {$product.prodcode})' title="{l s='remove this product from list' mod='askforaquote'}">&nbsp;&nbsp;&nbsp;</a></span>
                        </td>
                    </tr>
		        {/foreach}
		        </tbody>	        
	        </table>
			{if $gofinal}
			<input type="submit" name="Submit" id="subquotes" value="{l s='Submit these quotes' mod='askforaquote'}" class="button" onclick="return submitReq({$customerid},'{$dataclientemail}');" style="display:none" />
            <input type="hidden" id="group_name" name="group_name" value="{l s='MyQuotes' mod='askforaquote'}{$groupNr}" />
            <input type="hidden" id="group_comment" name="group_comment" value="" />
            <input type="hidden" id="submitted_quotes" name="submitted_quotes" value="{$groupNr}" />
            <input type="hidden" id="total_price" name="total_price" value="{$tprice}" />
			<script type="text/javascript">
		    	$('#subquotes').click();
			</script> 
			{/if}
			{if $isLogged AND !$isGuest AND !$gofinal}
            <div id="opc_new_account" class="opc-main-block">
            	<h2{if $psVersion==1.6} class="page-heading"{/if}>{l s='Quote options' mod='askforaquote'}</h2>
                <form action="" id="comment_form" class="std box{if $psVersion==1.5}_recursive{/if}">
            	<input type="hidden" id="submitted_quotes" name="submitted_quotes" value="{$groupNr}" />
            	<input type="hidden" id="total_price" name="total_price" value="{$tprice}" />
                <fieldset>
				<div class="{if $psVersion==1.6}form-group{else}fieldhold{/if}">
					<label for="group_name">{l s='Quote group name' mod='askforaquote'}</label>
					<input type="text" id="group_name" name="group_name" class="form-control" value="{l s='MyQuotes' mod='askforaquote'}{$groupNr}" />
				</div>
				<div class="{if $psVersion==1.6}form-group{else}fieldhold{/if}">
					<label for="group_comment">{l s='Message to seller' mod='askforaquote'}</label>
					<textarea name="group_comment" id="group_comment" cols="26" rows="3" class="form-control"></textarea>
				</div>
                </fieldset>
                </form>
            </div>
	        <div id="chckout_pg" class="buttons_bottom_block"><form><p id="chckout_pg" class="buttons_bottom_block"><input type="submit" name="Submit" value="{l s='Submit these quotes' mod='askforaquote'}" class="button" onclick="return submitReq({$customerid},'{$dataclientemail}');"/></p></form></div>    
	        {/if}
        </div>
		{if $guesterror == 1}
		<br /><br />
		<p style="color:#FF0000; font-size:14px">{l s='This email already exists. Please change the email or login' mod='askforaquote'}</p>
		<br /><br />
		{/if}
        <p class="cart_navigation{if $psVersion==1.5}_recursive{/if} clearfix">
            <a href="{if (isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order.php')) || isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order-opc') || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index')}{else}{$smarty.server.HTTP_REFERER|escape:'html':'UTF-8'|secureReferrer}{/if}"
                class="button-exclusive btn btn-default"
                title="{l s='Continue shopping' mod='askforaquote'}">
                <i class="icon-chevron-left"></i>{l s='Check other products' mod='askforaquote'}
            </a>
            <br /><br />
        </p>
        {if $isLogged AND !$isGuest}
		{else}
			<!-- Create account / Guest account / Login block -->
			{include file="order-opc-askforquote.tpl"}
			<!-- END Create account / Guest account / Login block -->
		{/if}
{else}
    <h2>{l s='Quotes' mod='askforaquote'}&nbsp;</h2>
    <p class='warning'>{l s='No quotes' mod='askforaquote'}</p>
{/if}
</div>