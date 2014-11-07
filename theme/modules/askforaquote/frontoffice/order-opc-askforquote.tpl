{*
* Ask for a Quote module for PrestaShop
*
*  @author    Presta FABRIQUE - www.presta-shop-modules.com
*  @copyright 2014 Presta FABRIQUE
*  @license   Presta FABRIQUE
*}
<script type="text/javascript" src="{$base_dir}modules/askforaquote/js/order-opc-quote.js"></script> 
<script type="text/javascript">
	var fieldsnocomplete="{l s='All required fields must be completed' mod='askforaquote'}";
	var emailnotvalid="{l s='The email address is not valid' mod='askforaquote' mod='askforaquote'}";
	var alertext="{l s='You have to agree with the Terms and conditions' mod='askforaquote'}";
	var simple_checkout = {$simple_checkout};
	$(document).ready(function() {
		if(simple_checkout) {
			$('.hideSimple').hide();
			$('.col-sm-6').removeClass('col-sm-6').addClass('simple-column');
		}
	});
</script>
<div id="opc_new_account" class="opc-main-block">
    <div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>
    <h2{if $psVersion==1.6} class="page-heading"{/if}>{l s='Submit options' mod='askforaquote'}</h2>
	<form action="{$link->getPageLink('authentication.php', true)}?back=askforquote.php" method="post" id="login_form" class="std box">
		<fieldset>
			<h3 class="quotelogin page-subheading">{l s='Already registered?' mod='askforaquote'}</h3>
            <p><a href="#" id="openLoginFormBlock">&raquo; {l s='Click here' mod='askforaquote'}</a></p>
			<div id="login_form_content" class="clearfix" style="display:none;">
				<!-- Error return block -->
				<div id="opc_login_errors" class="error" style="display:none;"></div>
				<!-- END Error return block -->
				<div class="{if $psVersion==1.6}form-group{else}fieldhold{/if}">
					<label for="login_email">{l s='E-mail address' mod='askforaquote'}</label>
					<input type="text" id="login_email" name="email" class="form-control" />
				</div>
				{if $psVersion!=1.6}<div class="fieldhold">&nbsp;</div>{/if}
				<div class="{if $psVersion==1.6}form-group{else}fieldhold{/if}">
					<label for="passwd">{l s='Password' mod='askforaquote'}</label>
					<input type="password" id="passwd" name="passwd" class="form-control" />
				</div>
				{if $psVersion!=1.6}<div class="fieldhold">&nbsp;</div>{/if}
				<div class="{if $psVersion==1.6}form-group{else}fieldhold{/if}">
					{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
					<input type="submit" id="SubmitLoginQuote" name="SubmitLoginQuote" class="button" value="{l s='Log in' mod='askforaquote'}" />
				</div>
				<p class="lost_password"><a href="{$link->getPageLink('password.php', true)}">{l s='Forgot your password?' mod='askforaquote'}</a></p>
			</div>
		</fieldset>
	</form>
	<form action="#" method="post" id="new_account_form" class="std box">
		<fieldset>
			<div id="opc_account_form" class="quoteaccount">
				<script type="text/javascript">
				// <![CDATA[
				idSelectedCountry = {if isset($guestInformations) && $guestInformations.id_state}{$guestInformations.id_state|intval}{else}false{/if};
				{if isset($countries)}
					{foreach from=$countries item='country'}
						{if isset($country.states) && $country.contains_states}
							countries[{$country.id_country|intval}] = new Array();
							{foreach from=$country.states item='state' name='states'}
								countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state}', 'name' : '{$state.name|escape:'htmlall':'UTF-8'}'{rdelim});
							{/foreach}
						{/if}
						{if $country.need_identification_number}
							countriesNeedIDNumber.push({$country.id_country|intval});
						{/if}
						{if isset($country.need_zip_code)}
							countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
						{/if}
					{/foreach}
				{/if}
				//]]>
				{if $vat_management}
					{literal}
					function vat_number()
					{
						if ($('#company').val() != '')
							$('#vat_number_block').show();
						else
							$('#vat_number_block').hide();
					}
					function vat_number_invoice()
					{
						if ($('#company_invoice').val() != '')
							$('#vat_number_block_invoice').show();
						else
							$('#vat_number_block_invoice').hide();
					}
					$(document).ready(function() {
						$('#company').blur(function(){
							vat_number();
						});
						$('#company_invoice').blur(function(){
							vat_number_invoice();
						});
						vat_number();
						vat_number_invoice();
					});
					{/literal}
				{/if}
				</script>
				<div class="col-sm-6">
                    <h3 id="new_account_title" class="page-subheading">{l s='New Customer' mod='askforaquote'}</h3>
                    <!-- Error return block -->
                    <div id="opc_account_errors" class="error" style="display:none;"></div>
                    <!-- END Error return block -->
                    <!-- Account -->
                    <input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
                    <input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />
                    <input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
                    <input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
                    <p class="required text">
                        <label for="email">{l s='E-mail' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" id="email" name="email" value="{if isset($guestInformations) && $guestInformations.email}{$guestInformations.email}{/if}" />
                    </p>
                    <p class="required password is_customer_param">
                        <label for="passwd">{l s='Password' mod='askforaquote'} <sup>*</sup></label>
                        <input type="password" class="text form-control" name="passwd" id="reg_passwd" />
                        <span class="form_info">{l s='(5 characters min.)' mod='askforaquote'}</span>
                    </p>
                    <p class="radio required hideSimple">
                        {if ($version == "1.6")}
                        <label>{l s='Title' mod='askforaquote'}&nbsp;&nbsp;</label>
                        {else}
                        <span>{l s='Title' mod='askforaquote'}</span>
                        {/if}
                        <input type="radio" name="id_gender" id="id_gender1" value="1" {if isset($guestInformations) && $guestInformations.id_gender == 1}checked="checked"{/if} />
                        <label for="id_gender1" class="top">{l s='Mr.' mod='askforaquote'}</label>
                        <input type="radio" name="id_gender" id="id_gender2" value="2" {if isset($guestInformations) && $guestInformations.id_gender == 2}checked="checked"{/if} />
                        <label for="id_gender2" class="top">{l s='Ms.' mod='askforaquote'}</label>
                    </p>
                    <p class="required text">
                        <label for="firstname">{l s='First name' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" value="{if isset($guestInformations) && $guestInformations.customer_firstname}{$guestInformations.customer_firstname}{/if}" />
                    </p>
                    <p class="required text">
                        <label for="lastname">{l s='Last name' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" value="{if isset($guestInformations) && $guestInformations.customer_lastname}{$guestInformations.customer_lastname}{/if}" />
                    </p>
                    <p class="select hideSimple">
                        {if ($version == "1.6")}
                        <label>{l s='Date of Birth' mod='askforaquote'}&nbsp;&nbsp;</label>
                        {else}
                        <span>{l s='Date of Birth' mod='askforaquote'}</span>
                        {/if}
                        <div class="row hideSimple">
                            <div class="col-xs-4">
                                <select id="days" name="days" class="form-control">
                                    <option value="">-</option>
                                    {foreach from=$days item=day}
                                        <option value="{$day|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_day == $day)} selected="selected"{/if}>{$day|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <select id="months" name="months" class="form-control">
                                    <option value="">-</option>
                                    {foreach from=$months key=k item=month}
                                        <option value="{$k|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_month == $k)} selected="selected"{/if}>{l s="$month"}&nbsp;</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <select id="years" name="years" class="form-control">
                                    <option value="">-</option>
                                    {foreach from=$years item=year}
                                        <option value="{$year|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_year == $year)} selected="selected"{/if}>{$year|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </p>
                    {if $newsletter}
                    <p class="checkbox clearfix">
                        <input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($guestInformations) && $guestInformations.newsletter}checked="checked"{/if} />
                        <label for="newsletter">{l s='Sign up for our newsletter' mod='askforaquote'}</label>
                    </p>
                    <p class="checkbox clearfix">
                        <input type="checkbox"name="optin" id="optin" value="1" {if isset($guestInformations) && $guestInformations.optin}checked="checked"{/if} />
                        <label for="optin">{l s='Receive special offers from our partners' mod='askforaquote'}</label>
                    </p>
                    {/if}
                </div>
				<div class="col-sm-6">
                    <h3 class="page-subheading top-indent hideSimple">{l s='Personal address' mod='askforaquote'}</h3>
                    {foreach from=$dlv_all_fields item=field_name}
                    {if $field_name eq "company"}
                    <p class="text hideSimple">
                        <label for="company">{l s='Company' mod='askforaquote'}</label>
                        <input type="text" class="text form-control" id="company" name="company" value="{if isset($guestInformations) && $guestInformations.company}{$guestInformations.company}{/if}" />
                    </p>
                    {elseif $field_name eq "firstname"}
                    <p class="required text hideout">
                        <label for="firstname">{l s='First name' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" id="firstname" name="firstname" value="{if isset($guestInformations) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
                    </p>
                    {elseif $field_name eq "lastname"}
                    <p class="required text hideout">
                        <label for="lastname">{l s='Last name' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" id="lastname" name="lastname" value="{if isset($guestInformations) && $guestInformations.lastname}{$guestInformations.lastname}{/if}" />
                    </p>
                    {elseif $field_name eq "address1"}
                    <p class="required text hideSimple">
                        <label for="address1">{l s='Address' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" name="address1" id="address1" value="{if isset($guestInformations) && $guestInformations.address1}{$guestInformations.address1}{/if}" />
                    </p>
                    {elseif $field_name eq "address2"}
                    <p class="text is_customer_param hideSimple">
                        <label for="address2">{l s='Address (Line 2)' mod='askforaquote'}</label>
                        <input type="text" class="text form-control" name="address2" id="address2" value="" />
                    </p>
                    {elseif $field_name eq "postcode"}
                    <p class="required postcode text hideSimple">
                        <label for="postcode">{l s='Zip / Postal code' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" name="postcode" id="postcode" value="{if isset($guestInformations) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
                    </p>
                    {elseif $field_name eq "city" || $field_name eq "city,"}
                    <p class="required text hideSimple">
                        <label for="city">{l s='City' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" name="city" id="city" value="{if isset($guestInformations) && $guestInformations.city}{$guestInformations.city}{/if}" />
                    </p>
                    {elseif $field_name eq "country" || $field_name eq "Country:name"}
                    <p class="required select hideSimple">
                        <label for="id_country">{l s='Country' mod='askforaquote'} <sup>*</sup></label>
                        <select name="id_country" id="id_country" class="form-control">
                            <option value="">-</option>
                            {foreach from=$countries item=v}
                            <option value="{$v.id_country}" {if (isset($guestInformations) AND $guestInformations.id_country == $v.id_country) OR (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </p>
                    {elseif $field_name eq "vat_number"}
                        <p class="text hideSimple" id="vat_number_block">
                            <label for="vat_number">{l s='VAT number' mod='askforaquote'}</label>
                            <input type="text" class="text form-control" name="vat_number" id="vat_number" value="{if isset($guestInformations) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />
                        </p>
                    {/if}
                    {/foreach}
                    <p class="required text dni hideSimple">
                        <label for="dni">{l s='Identification number' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" name="dni" id="dni" value="{if isset($guestInformations) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
                        <span class="form_info">{l s='DNI / NIF / NIE' mod='askforaquote'}</span>
                    </p>
                    <p class="required id_state select hideSimple">
                        <label for="id_state">{l s='State' mod='askforaquote'} <sup>*</sup></label>
                        <select name="id_state" id="id_state" class="form-control">
                            <option value="">-</option>
                        </select>
                    </p>
                    <p class="textarea is_customer_param hideSimple">
                        <label for="other">{l s='Additional information' mod='askforaquote'}</label>
                        <textarea name="other" id="other" cols="26" rows="3" class=" form-control"></textarea>
                    </p>
                    <p class="text">
                        <label for="phone">{l s='Home phone' mod='askforaquote'} <sup>*</sup></label>
                        <input type="text" class="text form-control" name="phone" id="phone" value="{if isset($guestInformations) && $guestInformations.phone}{$guestInformations.phone}{/if}" />
                    </p>
                    <p class="text is_customer_param hideSimple">
                        <label for="phone_mobile">{l s='Mobile phone' mod='askforaquote'}</label>
                        <input type="text" class="text form-control" name="phone_mobile" id="phone_mobile" value="" />
                    </p>
                </div><!-- col-sm-6 closing -->
				<input type="hidden" name="alias" id="alias" value="{l s='My address' mod='askforaquote'}" />
                {if ($version == "1.5")}
                <div class="error_customerprivacy" style="color:red;"></div>
                <div class="account_creation customerprivacy clearfix">
                    <h3>{l s='Customer data privacy' mod='blockcustomerprivacy' mod='askforaquote'}</h3>
                    <p class="required" style="float: left;">
                        <input type="checkbox" value="1" id="customer_privacy" name="customer_privacy" checked="checked" style="margin:0 15px;" />				
                    </p>
                    <label for="customer_privacy">{l s='The personal data you provide is used to answer to your queries, process your orders or allow you to access specific information. You have a right to modify and delete all the personal information which we hold concerning yourself in the "my account" page.' mod='askforaquote'}</label>		
                </div>
                {/if}
				<!-- END Account -->
			</div>
		</fieldset>
		<div class="checkbox">
		{if $shopid == 0}
			<table width="300px" style="border:none">
            	<tr>
                	{if $terms == 1}<td align="right" style="border:none"><input type="checkbox" name="termsask" id="termsask">&nbsp;{l s='I accept the' mod='askforaquote'}&nbsp;</td>{/if}
                    <td align="left" style="border:none"><label for="termsask"><a href="{$base_dir}modules/askforaquote/frontoffice/terms.php" style="color:#0033CC" class="iframe" data-fancybox-type="iframe">{l s='Terms and conditions' mod='askforaquote'}</a></label></td>
                </tr>
            </table>
		{else}
			{if $terms == 1}<input type="checkbox" name="termsask" id="termsask">&nbsp;<label for="termsask">{l s='I accept the' mod='askforaquote'}&nbsp;{else}<label for="termsask">{/if}<a href="{$base_dir}modules/askforaquote/frontoffice/terms.php" style="color:#0033CC" class="iframe" data-fancybox-type="iframe">{l s='Terms and conditions' mod='askforaquote'}</a></label>
		{/if}		
		{if $terms == 1}
		<script type="text/javascript">
		$(document).ready(function() { 
		    $('#submitquoteterms').click(function(){
				var t_val = $('#termsask').is(':checked') ? 1 : 0;
				if (t_val == 0) alert(alertext);
				else  $('#submitAccountQuote').click();
			});
        });	
		$(document).ready(function() { 
		    $('#submitasguestterms').click(function(){
				var t_val = $('#termsask').is(':checked') ? 1 : 0;
				email=document.getElementById('email').value;
				firstname=document.getElementById('customer_firstname').value;
				lastname=document.getElementById('customer_lastname').value;
				AtPos = email.indexOf("@");
				AtPosLast = email.lastIndexOf("@");
				wrongone = email.indexOf("@.");
				wrongtwo = email.indexOf(".@");
				StopPos = email.lastIndexOf(".");
				dif=email.length - StopPos;
				if (AtPos < 1 || StopPos == -1  ||  wrongone > 0 ||  wrongtwo > 0 || AtPos < AtPosLast) {
					alert(emailnotvalid);
				} 
				else if (dif > 4 || dif < 3 || StopPos < AtPos)  alert(emailnotvalid);
				else {	
					if ( (email == '') || (firstname == '') || (lastname == '') ) {
						alert(fieldsnocomplete);
					}
					else if (t_val == 0) alert(alertext);
					else { 
						document.getElementById('reg_passwd').value='';
						$('#submitasguest').click();
					}
				}
			});
        });
		</script>
		{else}
		<script type="text/javascript">
		$(document).ready(function() { 
		    $('#submitasguestcheck').click(function(){
				var t_val = $('#termsask').is(':checked') ? 1 : 0;
				email=document.getElementById('email').value;
				firstname=document.getElementById('customer_firstname').value;
				lastname=document.getElementById('customer_lastname').value;
				AtPos = email.indexOf("@");
				AtPosLast = email.lastIndexOf("@");
				wrongone = email.indexOf("@.");
				wrongtwo = email.indexOf(".@");
				StopPos = email.lastIndexOf(".");
				dif=email.length - StopPos;
				if (AtPos < 1 || StopPos == -1  ||  wrongone > 0 ||  wrongtwo > 0 || AtPos < AtPosLast) {
					alert(emailnotvalid);
				} 
				else if (dif > 4 || dif < 3 || StopPos < AtPos)  alert(emailnotvalid);
				else {
					if ( (email == '') || (firstname == '') || (lastname == '') ) {
						alert(fieldsnocomplete);
					}
					else  { 
						document.getElementById('reg_passwd').value='';
						$('#submitasguest').click();
					}
				}
			});
        });
		</script>
		{/if}
		</div>
		<div style="clear:both"></div>
        <br /><br />
		<div class="clearfix">			
            <p style="float: right;">
            {if $terms == 1}
                {if $shopid == 0}
                <table style="float:right">
                    <tr>
                        <td style="border:none"><input type="button" class="exclusive button" name="submitquoteterms" id="submitquoteterms" value="{l s='Submit & Register' mod='askforaquote'}" /></td>
                        {if $guestcheckout == 1}
                        <td style="border:none"><input type="button" class="exclusive button" name="submitasguestterms" id="submitasguestterms" value="{l s='Submit as Guest' mod='askforaquote'}" /></td>
                        {/if}
                    </tr>
                </table> 
                {else}
                    <input type="button" class="exclusive button" name="submitquoteterms" id="submitquoteterms" value="{l s='Submit and Register' mod='askforaquote'}" />&nbsp;&nbsp;{if $guestcheckout == 1}<input type="button" class="exclusive button" name="submitasguestterms" id="submitasguestterms" value="{l s='Submit as Guest' mod='askforaquote'}" />{/if} 
            	{/if}	
                <input type="submit" class="exclusive button" name="submitAccountQuote" id="submitAccountQuote" value="{l s='Submit and Register' mod='askforaquote'}" style="display:none" />&nbsp;&nbsp;<input type="submit" class="exclusive button" name="submitasguest" id="submitasguest" value="{l s='Submit as Guest' mod='askforaquote'}" style="display:none"/>
            {else}
                {if $shopid == 0}
                <table style="float:right">
                    <tr>
                        <td style="border:none"><input type="submit" class="exclusive button" name="submitAccountQuote" id="submitAccountQuote" value="{l s='Submit & Register' mod='askforaquote'}" /></td>
                        {if $guestcheckout == 1}
                        <td style="border:none"><input type="button" class="exclusive button" name="submitasguestcheck" id="submitasguestcheck" value="{l s='Submit as Guest' mod='askforaquote'}" /><input type="submit" class="exclusive button" name="submitasguest" id="submitasguest" value="{l s='Submit as Guest' mod='askforaquote'}"  style="display:none"/></td>
                        {/if}
                    </tr>
                </table> 
                {else}
                <input type="submit" class="exclusive button" name="submitAccountQuote" id="submitAccountQuote" value="{l s='Submit and Register' mod='askforaquote'}" />&nbsp;&nbsp;{if $guestcheckout == 1}<input type="button" class="exclusive button" name="submitasguestcheck" id="submitasguestcheck" value="{l s='Submit as Guest' mod='askforaquote'}" /><input type="submit" class="exclusive button" name="submitasguest" id="submitasguest" value="{l s='Submit as Guest' mod='askforaquote'}"  style="display:none"/>{/if}
                {/if}
            {/if}	
            </p>
            <p style="float: right;color: green;display: none;" id="opc_account_saved">
                {l s='Account information saved successfully' mod='askforaquote' mod='askforaquote'}
            </p>
            <p style="float: left;">
                <sup>*</sup>{l s='Required field' mod='askforaquote' mod='askforaquote'}
            </p>
        </div>
	</form>
	<div class="clear"></div>
</div>