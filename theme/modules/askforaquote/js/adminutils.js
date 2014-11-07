/**
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*/

function showBargain(request) {
	$(request).toggle("slow");
}
function closeBargain(request) {
	$(request).hide("slow");
}
 
function showDetails(request) {
	$(request).toggle("slow");
} 
function closeDetails(request) {
	$(request).hide("slow");
}

$(function() {
	$(".hiddenforms").draggable({ stack: ".hiddenforms" });
});