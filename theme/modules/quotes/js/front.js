/**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function(){

	// Change quote name
	$( ".quote_name" ).on( "click", function() {
		var $thisQuoteName = $(this);
		var $oldName = $thisQuoteName.text();
		if($oldName == ''){
			var $myInput = $($thisQuoteName).find('.changed_name');
		}else {
			$($thisQuoteName).html('<input class="changed_name" type="text" value="' + $oldName + '">');
			var $myInput = $($thisQuoteName).find('.changed_name');
		}
		$myInput.focus();
		$myInput.on( "blur", function() {
			var $id_quote = $thisQuoteName.data('value');
			var $quoteName = $myInput.val();
			$.ajax({
				url: submitedQuotes,
				method:'post',
				data:
				{
					quoteRename: 'rename',
					id_quote : $id_quote,
					quoteName : $quoteName
				},
				dataType:'json',
				success: function(data) {
					if(data.hasError){
							alert(data.message);
							$thisQuoteName.html('<i class="icon-pencil"></i>' + $oldName);
					}
					if(data.renamed){
						$thisQuoteName.html('<i class="icon-pencil"></i>' + data.renamed);
					}
				}
			});
		});
	});

	// Show quote products
	$(document).on('click', '#show_quote_products_info', function (e) {
		e.preventDefault();
		$('#quote_products_info').toggle('slow');
		//$(this).text();
	});

	//Submit bargain price
	$('.rejectBargainOffer, .acceptBargainOffer').on('click', function(e) {
		var $action = $(this).data('action');
		var $id_bargain = $(this).data('id');
		var $id_quote = $(this).data('quote');

		$.ajax({
			url: submitedQuotes,
			method:'post',
			data:
			{
				actionSubmitBargain : $action,
        		id_quote : $id_quote,
				id_bargain : $id_bargain
			},
			dataType:'json',
			success: function(data) {
				if(data.hasError)
					$('#danger_bargain_' + $id_bargain).css('display', 'block');
				else
					$('.burgainSubmitForm').css('display', 'none');

				if(data.submited == 'reject')
					$('#reject_bargain_' + $id_bargain).css('display', 'block');
				if(data.submited == 'accept')
					$('#success_bargain_' + $id_bargain).css('display', 'block');
			}
		});
		return false;
	});

	$('.submit_quote').on('click', function() {
		$.ajax({
			url: quotesCart,
			method:'post',
			data: 'action=submit',
			dataType:'json',
			success: function(response) {
				console.log(response);
				if(response.hasError == false) {
					window.location = response.redirectUrl;
				}
			}
		});
		return false;
	});

    $('body').on('click','.fly_to_quote_cart_button', function(){
		$('#ipa').val($('#idCombination').val());
		$.ajax({
			url: quotesCart,
			method:'post',
			data: $('#quote_ask_form').serialize(),
			dataType:'json',
			success: function(response) {
				if(!$('#box-body').hasClass('expanded'))
					$('#box-body').addClass('expanded');
				$('#product-list').empty();
				$('#product-list').html(response.products);
			}
		});
        return false;
    });
	$('body').on('click','.ajax_add_to_quote_cart_button', function(){
		$('#ipa').val($('#idCombination').val());
		$.ajax({
			url: quotesCart,
			method:'post',
			data: $('#quote_ask_form').serialize(),
			dataType:'json',
			success: function(response) {
				if(!$('#box-body').hasClass('expanded'))
					$('#box-body').addClass('expanded');
				$('#product-list').empty();
				$('#product-list').html(response.products);
			}
		});
		return false;
	});

	$('body').on('click', '.remove-quote', function() {
		var item = $(this).attr('rel');
		var item_a = $(this);
		$.ajax({
			url: quotesCart,
			method:'post',
			data: 'action=delete&item_id='+item,
			dataType:'json',
			success: function(response) {
				item_a.closest('dt').fadeOut('slow', function(){
					item_a.closest('dt').remove();
				});
				$('#product-list').empty();
				$('#product-list').html(response.products);
			}
		});
	});

	// quotes cart actions
	var cart_block = new showCart('#header .quotes_cart_block');
	var cart_parent_block = new showCart('#header .quotes_cart');

	$("#header .quotes_cart a:first").hover(
		function(){
				$("#header .quotes_cart_block").stop(true, true).slideDown(450);
		},
		function(){
			setTimeout(function(){
				if (!cart_parent_block.isHoveringOver() && !cart_block.isHoveringOver()) {
					$("#header .quotes_cart_block").stop(true, true).slideUp(450);
					if($('#box-body').hasClass('expanded'))
						$('#box-body').removeClass('expanded');
				}

			}, 200);
		}
	);

	$("#header .cart_block").hover(
		function(){
		},
		function(){
			setTimeout(function(){
				if (!cart_parent_block.isHoveringOver()) {
					$("#header .quotes_cart_block").stop(true, true).slideUp(450);
					if($('#box-body').hasClass('expanded'))
						$('#box-body').removeClass('expanded');
				}
			}, 200);
		}
	);
});
function showCart(selector)
{
	this.hovering = false;
	var self = this;

	this.isHoveringOver = function(){
		return self.hovering;
	}

	$(selector).hover(function(){
		self.hovering = true;
	}, function(){
		self.hovering = false;
	})
}