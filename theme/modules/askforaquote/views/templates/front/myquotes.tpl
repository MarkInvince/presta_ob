{*
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*}
{if $psVersion==1.6}
<script type="text/javascript">
$(document).ready(function(){
    $('.breadcrumb').append("<span class='navigation-pipe'>{$navigationPipe}</span><a href='{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}'>&nbsp;{l s='My account'}</a><span class='navigation-pipe'>{$navigationPipe}</span><span class='navigation_page'> {l s='My Quotes' mod='askforaquote'}</span>");
});
</script>
{else}
{capture name=path}{l s='My Quotes' mod='askforaquote'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<link href="{$base_dir}modules/askforaquote/css/myquotes.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="{$base_dir}modules/askforaquote/js/jquery-ui-1.10.4.custom.js"></script> 
<script type="text/javascript" src="{$base_dir}modules/askforaquote/js/jquery.ui.draggable.min.js"></script>   
{literal}
<script type="text/javascript">
var txtsuccessBack ="{l s='Your comment was submitted!' mod='askforaquote'}";
var buttonrefresh="{l s='Refresh to see the changes' mod='askforaquote'}";
function showbargain(request) {
	var id="bargainform" + request;
	if ($('#'+id).is(':visible')) {
		jQuery('#'+id).hide(500);
	}
	else {
		jQuery('.bargainform').hide(500);
		jQuery('#'+id).show(500);
//		$('#'+id).draggable( "enable" );
	}
} 
function showdetails(request) {
	var id="details" + request;
	if ($('#'+id).is(':visible')) {
		jQuery('#'+id).hide(500);
	}
	else {
		jQuery('.bargaindetails').hide(500);
		jQuery('#'+id).show(500);
	}
}
function closeBlock(request) {
	$(request).hide("slow");
}
function showChangeForm(id) {
	$("#newname"+id).slideToggle("slow");;
}
$(function() {
	$(".dragme").draggable({ stack: ".dragme" });
});
</script>
{/literal}
<div id="req_main">
	<h1 class="page-heading">{l s='My submitted quotes' mod='askforaquote'}<span class="heading-counter">{l s='You have submitted' mod='askforaquote'} {$groups|@count} {l s='quotes so far' mod='askforaquote'}</span></h1>
    {if $quotes|@count >0}
		<div style="width:100%; border:none"> 
		{if $error > 0}
        	<span><b><font color="#FF0000">
                {if $error==1}{l s='All fields must be completed!' mod='askforaquote'}{/if}
                {if $error==2}{l s='Database error!' mod='askforaquote'}{/if}
			</font></b></span>
		{else}
			{if $succes == 1}<span><b><font color="#000000">{l s='Your comment has been submitted!' mod='askforaquote'}</font></b></span>{/if}
		{/if}
		</div>
        {foreach from=$groups item=g name=group}
        {assign var=newprice value=''}
        <div class="quotegroup">
        <h4><span id="gname{$g.id_group}">{$g.gname}</span> <a class="editname" id="editName{$g.id_group}" onclick="showChangeForm({$g.id_group})">[EDIT]</a><span class="date">{l s='Submitted on' mod='askforaquote'} {$g.date}</span></h4>
        <div class="newname" id="newname{$g.id_group}" style="display:none">
        	<label for="newgroupname{$g.id_group}">{l s='Change group name' mod='askforaquote'}</label>
            <input name="newgroupname{$g.id_group}" id="newgroupname{$g.id_group}" value="{$g.gname}" />
            <input type="button" name="Submit" value="{l s='Save' mod='askforaquote'}" class="exclusive" onclick="chngGroupName({$g.id_group})" />
        </div>
        <div class="comment"><strong>{l s='Comment' mod='askforaquote'}</strong>: {if !empty($g.comment)}{$g.comment}{else}-{/if}</div>
        <table width="100%" cellpadding="0" cellspacing="0" class="quote_summary std"> 
            <thead>
                <tr>
                    <th class="first_item">#</th>
                    <th class="item">{l s='Product name' mod='askforaquote'}</th>
                    {if !($PS_CATALOG_MODE) || ($enable_bargain == 1)}<th class="item lastprice">{if $enable_bargain == 1}{l s='Last price' mod='askforaquote'}{else}{l s='Price' mod='askforaquote'}{/if}</th>{/if}
                    {if $enable_bargain == 1}<th class="item bargaincol">&nbsp;</th>
                    <th class="last_item">{l s='Status' mod='askforaquote'}</th>{/if}
                </tr>
            </thead>
            <tbody>
                {foreach from=$quotes item=q name=quote}
                {if {$g.id_group}=={$q.id_group}}
                <!-- we count the comments for this product -->
                {assign var='totalReq' value=0}
                {foreach from=$comments item=com}
                {if ($com.id_product == $q.real_id) AND ($com.id_request == $q.id_request)}
                    {assign var=totalReq value=$totalReq+1}
                {/if}
                {/foreach}
				{assign var='currency' value=$q.curr_sign}
                <tr>
                    <td>
                    	{$smarty.foreach.quote.index +1}
                        <!--- bargain form -->
                        <div id="bargainform{$q.id_request}" class="dragme bargainform" style="display:none;position:absolute">
                            <form method="post" class="std">
                                <input type="hidden" name="request" id="request" value="{$q.id_request}" />
                                <input type="hidden" name="product" id="product" value="{$q.real_id}" />
                                <input type="hidden" name="currency" id="currency" value="{$currency}" />
                                <input type="hidden" name="customerid" id="customerid" value="{$q.id_customer}" />
                                <fieldset>
                                    <a class="close" onclick="closeBlock(bargainform{$q.id_request})">{l s='CLOSE' mod='askforaquote'}</a>
                                    <h4>{l s='Bargain for' mod='askforaquote'}&nbsp;{$q.qty}&nbsp;x&nbsp;{$q.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</h4>{if ($version == "1.5")}<br />{/if}
                                    <p class="text">
                                        <label for="price">{l s='My Price' mod='askforaquote'}&nbsp;({$currency})</label>{if ($version == "1.5")}<br />{/if}
                                        <input type="text" name="price" id="price" maxlenght="5" value="" class="form-control" />
                                    </p>
                                    <p class="text">
                                        <label for="comment">{l s='Comment' mod='askforaquote'}</label>{if ($version == "1.5")}<br />{/if}
                                        <textarea name="comment" id="comment" cols="40" rows="5" maxlenght="20" class="form-control"></textarea>
                                    </p>
                                    <p class="submit">
                                        <input type="submit" name="submitbargain" id="submitbargain" value="{l s='Submit' mod='askforaquote'}" class="button_small" />
                                    </p>
                                </fieldset>
                            </form>
                        </div>
                        <!--- details -->
                        <div id="details{$q.id_request}" class="dragme bargaindetails" style="display:none;">  
                            <a class="close" onclick="closeBlock(details{$q.id_request})">{l s='CLOSE' mod='askforaquote'}</a>
                            <h4 id="dethead">{l s='Bargain history for' mod='askforaquote'}<i>&nbsp;{$q.qty}&nbsp;x&nbsp;{$q.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</i></h4>{if ($version == "1.5")}<br />{/if}
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <thead>
                                <tr>
                                    <td>{l s='By' mod='askforaquote'}</td>
                                    <td>{l s='Comment' mod='askforaquote'}</td>
                                    <td align="right">{l s='Price' mod='askforaquote'}</td>
                                </tr>
                                </thead>
                                {foreach from=$comments item=com}
                                {if ($com.id_request == $q.id_request)}
                                <tr>
                                    <td>
                                    {if $com.user == $current_user}
                                        {l s='me' mod='askforaquote'}
                                    {else}
                                        {l s='admin' mod='askforaquote'}
                                    {/if}
                                    </td>
                                    <td>{$com.comment}</td>
                                    <td class="price">{$com.price}&nbsp;{$currency}</td>
                                </tr>
                                {/if}
                                {/foreach}
                                {if ($totalReq == 0)}
                                <tr>
                                    <td colspan="3">{l s='No bargain history for this product' mod='askforaquote'}</td>
                                </tr>
                                {/if}
                            </table> 
                        </div>
                    </td>		
                    <td>			
                        <a href="{$link->getProductLink($q.real_id, $q.link_rewrite)}" title="{$q.name|escape:'htmlall':'UTF-8'}" class="prodname">{$q.qty}&nbsp;x&nbsp;{$q.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a>
                        <br>
                        {foreach from=$q.attributes item=attribute name=attributeLoop}
                            {$attribute.attribute_group}:&nbsp;{$attribute.name}<br>
                        {/foreach}
                        {if !empty($q.reference)}
                            {l s='Reff:' mod='askforaquote'}&nbsp;{$q.reference}
                        {/if}
                    </td>
                    {if !($PS_CATALOG_MODE) || ($enable_bargain == 1)}
                    <td>
                       {if $enable_bargain == 1}
                           {if $q.comments > 0}
                              {$last[{$q.id_request}]['lastprice']}&nbsp;{$currency}
                              <br />({l s='by' mod='askforaquote'}&nbsp;
                              {if $last[{$q.id_request}]['lastuser'] == 'admin'}{l s='admin' mod='askforaquote'}{else}{l s='me' mod='askforaquote'}{/if}  )
                              {$newprice[] = $last[{$q.id_request}]['lastprice']}
                           {else}
                                {if $PS_CATALOG_MODE}
                                    &nbsp;-&nbsp;
                                {else}
                                    {$q.lastprice} {if $currency}{$currency}{/if}
                                    ({l s='by' mod='askforaquote'}&nbsp;{if $lastuser == 'admin'}{l s='admin' mod='askforaquote'}{else}{l s='me' mod='askforaquote'}{/if})
                                    {$newprice[] = $q.lastprice}
                                {/if}
                           {/if}
                           <br /><a href="#" onclick="showdetails('{$q.id_request}'); return false" class="details">{l s='Details' mod='askforaquote'} ({$totalReq})</a>
                       {else}
                            {if $PS_CATALOG_MODE}
                                &nbsp;-&nbsp;
                            {else}
                                {$q.lastprice} {if $currency}{$currency}{/if}
                                {$newprice[] = $q.lastprice}
                            {/if}
                       {/if}
                   </td>
                   {/if}
                   {if $enable_bargain == 1}
                   <td>
                    {if $q.status}
                        <input type="button" id="bargain" name="bargain" value="{l s='Bargain' mod='askforaquote'}" class="button_disabled" />
                    {else}
                        <input type="button" id="bargain" name="bargain" value="{l s='Bargain' mod='askforaquote'}" onclick="showbargain('{$q.id_request}');" class="button_small" />
                    {/if}
                   </td>
                   {if $q.status}
                   <td class="agree">
                        {l s='Agree' mod='askforaquote'}
                   </td>
                   {else}
                   <td class="notagree">
                        {l s='Not agree' mod='askforaquote'}
                   </td>
                   {/if}
                   {/if} {* enable bargain closing if *}
                </tr>
                {/if}
                {/foreach}
            </tbody>
            <tfoot>
            	<tr>
                	<td class="footer_price" {if $enable_bargain == 1}colspan="3"{else}colspan="2"{/if}>{l s='Total products' mod='askforaquote'}</td>
                	<td class="price" colspan="2">{if $PS_CATALOG_MODE}Not Applicable{else}{$g.original_price|string_format:"%.2f"} {$currency}{/if}</td>
                </tr>
                {if ($enable_bargain == 1)}
            	<tr>
                	<td class="footer_price" colspan="3">{l s='Bargained price' mod='askforaquote'}</td>
                	<td class="price" colspan="2">{if empty($newprice)}-{else}{$newprice|@array_sum} {$currency}{/if}</td>
                </tr>
                {/if}
            </tfoot>
        </table>
        </div>
        {/foreach}
    {else}
        <p class="numberinfo">{l s='You have not submitted any quotes yet.' mod='askforaquote'}</p>
    {/if}
</div>