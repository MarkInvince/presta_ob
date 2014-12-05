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
	$('#cms_page_select').parent().parent().css('display', 'none');
	$('#MAIN_TERMS_AND_COND_on').on('change', function(){
		$('#cms_page_select').parent().parent().fadeIn("slow");
	});
	$('#MAIN_TERMS_AND_COND_off').on('change', function(){
		$('#cms_page_select').parent().parent().fadeOut("slow");
	});

	$('body').on('click','.view_quote',  function(){
		$(this).closest('form').submit();
	});

	$('body').on('click', '.delete_quote', function(){
		if(confirm(confirmDelete)) {
			$.ajax({
				method   : 'post',
				data     : 'action=delete&item='+ $(this).attr('rel'),
				url      : adminQuotesUrl,
				dataType :'json',
				success: function(response) {
					if(response.data.hasError == false) {
						$('#quotes_panel').empty();
						$('#quotes_panel').html(response.data.quotes);
					}
					else {
						alert(response.data.message);
					}
				}
			});
		}
	});

	//Delete bargain offer
	$('.deleteBargainOffer').on('click', function() {

		if(confirm(confirmDelete)){
			var $action = $(this).data('action');
			var $id_bargain = $(this).data('id');
			var $thisBargain = $(this).closest('.admin_bargain');

			$.ajax({
				url: adminQuotesUrl,
				method:'post',
				data:
				{
					actionBargainDelete : $action,
					id_bargain : $id_bargain
				},
				dataType:'json',
				success: function(data) {
					console.log(data);
					console.log(data.deleted);
					if(data.hasError)
						$('#danger_bargain_' + $id_bargain).css('display', 'block');
					if(data.deleted){
						$thisBargain.html(data.message);
					}
				}
			});
		}
		return false;
	});

});