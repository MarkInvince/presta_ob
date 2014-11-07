{*
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*}
<!-- MODULE Ask for a quote -->
{if $psVersion==1.6}
<li class="account_gallery">
	<a href="{$base_dir}modules/askforaquote/frontoffice/myquotes.php" title="{l s='My quotes' mod='askforaquote'}">
    	<i class="icon-question-sign"></i>
        <span>{l s='My quotes' mod='askforaquote'}</span>
    </a>
</li>
{else}
<link href="{$base_dir}modules/askforaquote/css/my_account.css" rel="stylesheet" type="text/css" media="all" /> 
<li class="account_askforquote">
	<a href="{$base_dir}modules/askforaquote/frontoffice/myquotes.php" title="{l s='My quotes' mod='askforaquote'}"><img src="{$base_dir}modules/askforaquote/img/quoteicon.png" class="icon" /> {l s='My quotes' mod='askforaquote'}</a>
</li>
{/if}
<!-- /MODULE Ask for a quote -->