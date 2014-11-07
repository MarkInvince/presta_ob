{*
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*}
<script type="text/javascript">
function beforeask(prodid) {
	var ipavalue = document.getElementById('idCombination') ? document.getElementById('idCombination').value : 0;
	var selects = document.getElementsByTagName('select');
	var uniquecode = '';
	if ( document.getElementById('catalog_mode').value == '1' ) var quantity = document.getElementById('quantity_wanted_ask').value;
	else var quantity = document.getElementById('quantity_wanted').value; 
	for(i=0;i<selects.length;i++){
		sel = selects[i];
		if(sel.id.substr(0,5) == 'group') {
			uniquecode+= '&'+sel.id.substr(6,sel.id.length-6)+'='+sel.value;
		}
	}
	// added to read radio button values
    var radioData = $.map($('input:radio:checked'), function(elem, idx) {
//			return "&"+$(elem).attr("name")+"="+$(elem).val()+"("+$(elem).attr("rel")+")";
			return "&"+$(elem).attr("name").substr(6)+"="+$(elem).val();
    }).join('');
	uniquecode+= radioData;
	colorid=document.getElementById('colorgroupid').value;
	devicecol='group_'+colorid;
	if (uniquecode.indexOf(colorid+'=') > 0) {
		rtsa=2;
	}
	else {
		if (document.getElementById(devicecol)) {
			idcolor=document.getElementById('group_'+colorid).value;
			uniquecode+='&'+colorid+'='+idcolor;
		}
		else if (document.getElementById('color_to_pick_list')) {
			var list = document.getElementById('color_to_pick_list');
			var elems = list.getElementsByTagName("li");
			for (var i=0; i<elems.length; i++) {
				if (elems[i].className == 'selected') {
					var colors=elems[i].getElementsByTagName("a");
						for (var j=0; j<colors.length; j++) {
							idcolor=colors[j].id.replace('color_','');
						}
					}
				}
			uniquecode+='&'+colorid+'='+idcolor;
			}
		}
		uniquecode = uniquecode.replace(/&/g,'');
		uniquecode = uniquecode.replace(/=/g,'');
		if (uniquecode != '') {
		sum = 0;
		for (var i = 0; i < uniquecode.length; i++) {
			sum += parseInt(uniquecode.charAt(i), 10);
		}
		multip=1;
		for (var i = 0; i < uniquecode.length; i++) {
			if (parseInt(uniquecode.charAt(i), 10) > 0) {
				multip= multip * parseInt(uniquecode.charAt(i), 10); 
			}
		}
		uniquecode=parseInt((multip / sum) * 100);
	}
	uniquecode=prodid+'06'+uniquecode;
	makeRequest(uniquecode);
}
</script>
		<div class="ask_offer clearfix">
            {if ($version == 1.4)}
            <div class="qsuccess" style="display:none;"></div>
            {/if}
		    <form id="askblock" method="post">
			<input type="hidden" name="colorgroupid" id="colorgroupid" value="{$colorgroup}" />
			<input type="hidden" name="askcustomerid" id="askcustomerid" value="{$customerid}" />
			<input type="hidden" name="prodlink" id="prodlink" value="{$prodlink}" />
			<input type="hidden" name="prodname" id="prodname" value="{$product->name|escape:'htmlall':'UTF-8'}" />
			{if $PS_CATALOG_MODE}
            <p class="clearfix">
			<label for="quantity_wanted_ask">{l s='Quantity' mod='askforaquote'}:</label>
			<input type="hidden" name="catalog_mode" id="catalog_mode" value="1" />
			<input type="text" name="qty_ask" id="quantity_wanted_ask" class="text" value="1" size="2" maxlength="3" />
            </p>
			{else}
			<input type="hidden" name="catalog_mode" id="catalog_mode" value="0" />
			{/if}
            {if $quickView == 1}
				<p id="ask_offer" class="buttons_bottom_block"><input type="button" name="Submit" value="{l s='Ask for a quote' mod='askforaquote'}" class="exclusive" onclick="parent.beforeask({$prodid})"/></p>
            {else}
				<p id="ask_offer" class="buttons_bottom_block"><input type="button" name="Submit" value="{l s='Ask for a quote' mod='askforaquote'}" class="exclusive" onclick="beforeask({$prodid})"/></p>
            {/if}
			</form>
		</div>