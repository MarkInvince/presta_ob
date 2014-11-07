/**
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*/

// top small block related functions

function modifyQtyTop(n) {
	if (document.getElementById('topquotes')) {
		var oldQty = jQuery('#quote_quantity_top').html();
		jQuery('#quote_quantity_top').html((oldQty*1) + n);
	}
}

$(document).ready(function(){
	jQuery('#topquotecontainer').hover(function() {
		jQuery(this).find('#quote_block').stop(true, true).slideDown(450);
	}, function() {
		jQuery(this).find('#quote_block').stop(true, true).slideUp(450);
	});
});


// animate image move to quote cart
function moveQuoteImage(oldProdId,speed) {
//	var $quoteElement = $('#askblock_' + oldProdId).parent().parent().parent().find('a.product_image img,a.product_img_link img');
	var $quoteElement = $('.image' + oldProdId); // if added from list
	if (!$quoteElement.length)
		$quoteElement = $('#bigpic');
	
	var $quotePicture = $quoteElement.clone();
	var quotePictureOffsetOriginal = $quoteElement.offset();

	if ($quotePicture.size())
		$quotePicture.css({'position': 'absolute', 'top': quotePictureOffsetOriginal.top, 'left': quotePictureOffsetOriginal.left});

	var quotePictureOffset = $quotePicture.offset();
	
	if ($('#quote_block').hasClass('top')) {
		var quoteBlockOffset = $('#topquotes').offset();
	} else {
		var quoteBlockOffset = $('#quote_block').offset();
	}

	// Check if the quote cart block is activated for the animation
	if (quoteBlockOffset != undefined && $quotePicture.size()) {
		$quotePicture.appendTo('body');
		$quotePicture.css({ 'position': 'absolute', 'top': $quotePicture.css('top'), 'left': $quotePicture.css('left'), 'z-index': 5000 })
		.animate({ 'width': $quoteElement.attr('width')*0.20, 'height': $quoteElement.attr('height')*0.20, 'opacity': 0.2, 'top': quoteBlockOffset.top + 30, 'left': quoteBlockOffset.left + 15 }, speed)
		.fadeOut(100);
	}
}

var ajax = new sack();

function alreadyRequested(id_prd){
	return document.getElementById('trr_'+id_prd) ? true : false;
}

function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);

    if (elt.selectedIndex == -1)
        return null;

    return elt.options[elt.selectedIndex].text;
}

function addRequest(id_prd){
	
	var ipavalue = document.getElementById('idCombination') ? document.getElementById('idCombination').value : 0;
	var selects = document.getElementsByTagName('select');
	var attrib = '';
    var avalues = '';
	
  	if ( document.getElementById('catalog_mode').value == '1' ) var quantity = document.getElementById('quantity_wanted_ask').value;
		else  var quantity = document.getElementById('quantity_wanted').value; 
	
		
	for(i=0;i<selects.length;i++){
		sel = selects[i];
		if(sel.id.substr(0,5) == 'group') {
			attrib+= '&'+sel.id.substr(6,sel.id.length-6)+'='+sel.value;
			optiuni=sel.getElementsByTagName("option");
			for (var j=0; j<optiuni.length; j++) {
				if (optiuni[j].selected) avalues+=optiuni[j].title+', ';
			}
		}
	}
	
	// added to read radio button values
	var cinputs = $("#attributes input[type=radio]:checked").length;
	if (cinputs>0) {
		var radioNames = $.map($('input:radio:checked'), function(elem, idx) {
	//			return "&"+$(elem).attr("name")+"="+$(elem).val()+"("+$(elem).attr("rel")+")";
				if($(elem).attr("name").substr(0,5) == 'group') {
					attrib+= '&'+$(elem).attr("name").substr(6,$(elem).attr("name").length-6)+'='+$(elem).val();
        		}
				return $(elem).attr("rel")+', ';
		}).join('');
		avalues+= radioNames;
	}
	
	colorid=document.getElementById('colorgroupid').value;
	devicecol='group_'+colorid;

	if (attrib.indexOf(colorid+'=') > 0) {
		rtsa=2;
	}
	else {
		if (document.getElementById(devicecol)) {
			idcolor=document.getElementById('group_'+colorid).value;
			attrib+='&'+colorid+'='+idcolor;
			optiuni=document.getElementById(devicecol).getElementsByTagName("option");
			for (var j=0; j<optiuni.length; j++) {
				if (optiuni[j].selected) avalues+=optiuni[j].title+', ';
			}
		} else if (document.getElementById('color_to_pick_list')) {
			
			var list = document.getElementById('color_to_pick_list');
			var elems = list.getElementsByTagName("li");
			
			for (var i=0; i<elems.length; i++) {
				if (elems[i].className == 'selected') {
					var colors=elems[i].getElementsByTagName("a");
					for (var j=0; j<colors.length; j++) {
						idcolor=colors[j].id.replace('color_','');
						title=colors[j].title;
					}	
				}
			}
			
			attrib+='&'+colorid+'='+idcolor;
			avalues+=title+', ';
		}
	}

	saveattrib=attrib;
	textattrib = attrib;
	textattrib = textattrib.replace(/&/g,'-');
	textattrib = textattrib.replace(/=/g,'e');
	avalues=avalues.substring(0,avalues.length-2);

	customerid=document.getElementById('askcustomerid').value;
	same=0;
	
	if (alreadyRequested(id_prd)) {
		newquantity=parseInt(quantity)+parseInt(document.getElementById('qr_'+id_prd).innerHTML);
		modifyqty(customerid,id_prd,newquantity);
	} else {
		
		nrrows=1;
		
		ajax.requestFile = baseDir+'modules/askforaquote/ajax.php?pid='+id_prd+'&qty='+quantity+'&ipa='+ipavalue+'&textattrib='+avalues+'&nrrows='+nrrows+'&op=1'+attrib;
	
		ajax.onError = err;
		
		if(document.getElementById('requestTable')){
			if(document.getElementById('noquote')){
				$('#noquote').slideUp(500);
				setTimeout(function() {
					$('#noquote').html('');
				}, 500);
				$('#submitbox').prop('disabled', false).removeClass('exclusive_disabled').addClass('exclusive');
				$('#qsubmitnow').fadeIn(500);
//				document.getElementById('noquote').innerHTML = '';
			}
			nrrows = document.getElementById('requestTable').getElementsByTagName("TR").length;
			var table = document.getElementById('requestTable');     
			var row = table.insertRow(table.rows.length);    
			row.id = 'trr_'+id_prd;
			
			customerid=document.getElementById('askcustomerid').value;  
			prodlink=document.getElementById('prodlink').value;  
			prodname=document.getElementById('prodname').value;
			
			// product name detect for older Prestashop
			if(!prodname){
				var prodname = $('h1').html();
			}
			
			moveQuoteImage('',1000);
			
			// add the row to the quote cart
			var cell1 = row.insertCell(0); 
			cell1.className = "quote_qty";
			cell1.id = 'qr_'+id_prd;
			cell1.valign='top';
			cell1.style.verticalAlign='top';
			cell1.innerHTML = quantity; 
			
			var cell2 = row.insertCell(1);
			/*
			cell2.innerHTML = (typeof(product_name) != 'undefined' && product_name) ? product_name : ((typeof(product_names) != 'undefined' && product_names[id_prd]) ? product_names[id_prd] : $('#primary_block h1').html() );//product_names[id_prd];
			cell2.innerHTML =  "x&nbsp;" + '<a href="'+prodlink+'">' + cell2.innerHTML + '</a>' + '<input type="hidden" id="at_' + id_prd +'" value="' + textattrib + '" />' + '<br><span style="font-size:10px"><i>'+ avalues +'</i></span>';*/
			cell2.innerHTML =  "x&nbsp;" + '<a href="'+prodlink+'">' + prodname + '</a>' + '<input type="hidden" id="at_' + id_prd +'" value="' + textattrib + '" />' + '<br><span style="font-size:10px"><i>'+ avalues +'</i></span>';
			
			var cell3 = row.insertCell(2);
			cell3.style.width = "16px";
			cell3.innerHTML = "<span class='remove_link'><a style=\"margin-left:10px\" rel=\"nofollow\" class=\"ajax_cart_block_remove_link\" href=\"#\" onclick='return removeReq(this.parentNode.parentNode.parentNode.rowIndex, "+id_prd+")' title='"+rLtitle+"'><img src=\""+baseDir+"modules/askforaquote/img/delete.gif\" /></a></span>";
			$('#trr_'+id_prd).fadeOut(400);
			$('#trr_'+id_prd).fadeIn(400);
			
			modifyQtyTop(1);
			
		} else ajax.onCompletion = suc;	// if no block is present go directly to the quote cart
		
		ajax.runAJAX(); 
	
	}

    return false;
    
}


// function to add the request from the product list
function addRequestfromlist(id_prd,cant,old) {
	var oldProdId = old;

	var ipavalue = document.getElementById('idCombination') ? document.getElementById('idCombination').value : 0;
	var selects = document.getElementsByTagName('select');
	var attrib = '';
    var avalues = '';
	
	var quantity=cant;
		
	for(i=0;i<selects.length;i++){
		sel = selects[i];
		if(sel.id.substr(0,5) == 'group') {
			attrib+= '&'+sel.id.substr(6,sel.id.length-6)+'='+sel.value;
			optiuni=sel.getElementsByTagName("option");
			 for (var j=0; j<optiuni.length; j++) {
				if (optiuni[j].selected) avalues+=optiuni[j].title+', ';
			 }
		}
	}

	textattrib = attrib;

	customerid=document.getElementById('askcustomerid').value;
	same=0;
	
	if (alreadyRequested(id_prd)) {
		newquantity=parseInt(quantity)+parseInt(document.getElementById('qr_'+id_prd).innerHTML);
		modifyqty(customerid,id_prd,newquantity);
	} else {
		nrrows=1;
		ajax.requestFile = baseDir+'modules/askforaquote/ajax.php?pid='+id_prd+'&qty='+quantity+'&ipa='+ipavalue+'&textattrib='+avalues+'&nrrows='+nrrows+'&op=1'+attrib;
		ajax.onError = err;
	
		if(document.getElementById('requestTable')){ // check if left side block is there
			if(document.getElementById('noquote')){
				$('#noquote').slideUp(500);
				$('#submitbox').prop('disabled', false).removeClass('exclusive_disabled').addClass('exclusive');
				$('#qsubmitnow').fadeIn(500);
//				document.getElementById('noquote').innerHTML = '';
			}
			nrrows = document.getElementById('requestTable').getElementsByTagName("TR").length;
			var table = document.getElementById('requestTable');     
			var row = table.insertRow(table.rows.length);    
			row.id = 'trr_'+id_prd;
			
			customerid=document.getElementById('askcustomerid').value;  
			prodlink=document.getElementById('prodlink_'+old).value;  
			askproductname=document.getElementById('askprodname_'+old).value;
			
			moveQuoteImage(oldProdId, 1000);
			
			var cell1 = row.insertCell(0); 
			cell1.className = "quote_qty" + 3;
			cell1.id = 'qr_'+id_prd;
			cell1.valign='top';
			cell1.style.verticalAlign='top';
			cell1.innerHTML = quantity; 
			
			var cell2 = row.insertCell(1);
			
			cell2.innerHTML =  "x&nbsp;" + '<a href="'+prodlink+'">' + askproductname + '</a>' + '<input type="hidden" id="at_' + id_prd +'" value="' + textattrib + '" />' + '<br><span style="font-size:10px"><i>'+ avalues +'</i></span>';
			
			var cell3 = row.insertCell(2);
			cell3.style.width = "16px";
			cell3.innerHTML = "<span class='remove_link'><a style=\"margin-left:10px\" rel=\"nofollow\" class=\"ajax_cart_block_remove_link\" href=\"#\" onclick='return removeReq(this.parentNode.parentNode.parentNode.rowIndex, "+id_prd+")' title='"+rLtitle+"'><img src=\""+baseDir+"modules/askforaquote/img/delete.gif\" /></a></span>";
			$('#trr_'+id_prd).fadeOut(400);
			$('#trr_'+id_prd).fadeIn(400);
			
			modifyQtyTop(1);
			
		} else { // if no block is present go directly to the quote cart
			ajax.onCompletion = suc;	
		}
		
		ajax.runAJAX(); 
	
	}

    return false;
    
}

function suc(){
	window.location = baseDir+'modules/askforaquote/frontoffice/askforaquote.php';
}

function err(){
	//alert('Error '+ajax.responseStatus[0]+' message: '+ajax.responseStatus[1]);
}

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function removeReq(indx, id_prodct){       
    
    var row = document.getElementById('trr_'+id_prodct);
	$(row).fadeOut(500);
	setTimeout(function() {
		row.parentNode.removeChild(row);
		var rowCount = document.getElementById('requestTable').getElementsByTagName('tr').length;
		if (rowCount <1){ 
			if( document.getElementById('req_main') ){
				document.getElementById('req_main').innerHTML = "<h2>"+txtReqH2+"</h2><p class='warning'>"+txtNoMoreReq+"</p>";
			}
			$('#noquote').html(txtNoQuote).slideDown(500);
			$('#submitbox').prop('disabled', true).removeClass('exclusive').addClass('exclusive_disabled');
			$('#qsubmitnow').hide();
		}
		
		if(document.getElementById('cart_summary')){
			row = document.getElementById('product_'+id_prodct);
			row.parentNode.removeChild(row);    
			var rowCount = document.getElementById('cart_summary').getElementsByTagName('tr').length;
			if(rowCount < 1) document.getElementById('req_main').innerHTML = "<h2>"+txtReqH2+"</h2><p class='warning'>"+txtNoMoreReq+"</p>";
		}
		
		 
		ajax.requestFile = baseDir+'modules/askforaquote/ajax.php?pid='+id_prodct+'&op=0';    
		
		ajax.runAJAX();
	
		modifyQtyTop(-1);
	}, 500);
	
	return false;
}

function makeRequest(id_prd){
	id_prd = typeof(id_prd) != 'undefined' ? id_prd : id_product;
	addRequest(id_prd);
	return false;
}

function makeRequestfromlist(id_prd,cant,old){
	id_prd = typeof(id_prd) != 'undefined' ? id_prd : id_product;
	addRequestfromlist(id_prd,cant,old);
	return false;
}

function goCho(){
	document.location = rOcheckOut;
}

function modifyqty(customer,product,qty) {
	
	ajax.requestFile = baseDir+'modules/askforaquote/ajax.php?modqqty=1&modcustomer='+customer+'&modproduct='+product+'&modqty='+qty;  
	ajax.runAJAX();
	
	var oldProdId = product.slice(0,-2); //we strip the uniqueid which is 06 in our case - see product_list.tpl top script
	moveQuoteImage(oldProdId,500); //speed is 50% shorter than the first add
	
	jQuery('#qr_'+product).html(qty);
	
}

function modifyqtySub(customer,product,qty) { //update quantity from the submit page when changing the text input value
	
	ajax.requestFile = baseDir+'modules/askforaquote/ajax.php?modqqty=1&modcustomer='+customer+'&modproduct='+product+'&modqty='+qty;  
	ajax.runAJAX();
	
	jQuery('#qr_'+product).html(qty);
	
}

function submitReq(customer,customerdata){
	$('document').ready( function() {
	 
		gName = $('#group_name').val();
		comment = $('#group_comment').val();
		subsTotal = $('#submitted_quotes').val();
		priceTotal = $('#total_price').val();
		
		ajax.requestFile = baseDir+'modules/askforaquote/ajax.php?sbmit=1&customer='+customer+'&clientemaildata='+customerdata+'&gname='+gName+'&comment='+comment+'&newTotal='+subsTotal+'&priceTotal='+priceTotal;
		ajax.runAJAX();
				
		document.getElementById('req_main').innerHTML = "<h2>"+txtReqH2+"</h2><div class='conf'>"+txtsuccessReq+"</div><br /><a href='"+baseDir+"' class='button_large'>"+txtsuccessBack+"</a>";
		
		$('#noquote').html(txtNoQuote).slideDown(500);
		$('#submitbox').prop('disabled', true).removeClass('exclusive').addClass('exclusive_disabled');
		$('#qsubmitnow').hide();
		$('#quote_quantity_top').html("0");
		 
		var p_list = document.getElementById('requestTable').getElementsByTagName("tr");
		for (var i=p_list.length-1; i>=0; i--) {
				var p = p_list[i];           
				p.parentNode.removeChild(p);
		}
		
		return false;
	
	});
}

function chngGroupName(groupID) {
	
	newGroupName = $('#newgroupname'+groupID).val();
	
	ajax.requestFile = baseDir+'modules/askforaquote/ajax.php?modgname=1&modgroup='+groupID+'&modgname='+newGroupName;  
	ajax.runAJAX();
	
	$('#gname'+groupID).fadeOut().html(newGroupName).fadeIn();
	
	$("#newname"+groupID).slideUp();
	
}