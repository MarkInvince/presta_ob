<div id="opc_new_account" class="opc-main-block">
    <div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>

    <form action="{$link->getPageLink('authentication', true, NULL, "back=order-opc")|escape:'html':'UTF-8'}" method="post" id="login_form" class="box">
        <fieldset>
            <h3 class="page-subheading">{l s='Already registered?'}</h3>
            <p><a href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" id="openLoginFormBlock">&raquo; {l s='Click here'}</a></p>
            <div id="login_form_content" style="display:none;">
                <!-- Error return block -->
                <div id="opc_login_errors" class="alert alert-danger" style="display:none;"></div>
                <!-- END Error return block -->
                <p class="form-group">
                    <label for="login_email">{l s='Email address'}</label>
                    <input type="text" class="form-control validate" id="login_email" name="email" data-validate="isEmail" />
                </p>
                <p class="form-group">
                    <label for="login_passwd">{l s='Password'}</label>
                    <input class="form-control validate" type="password" id="login_passwd" name="login_passwd" data-validate="isPasswd" />
                </p>
                <a href="{$link->getPageLink('password', true)|escape:'html':'UTF-8'}" class="lost_password">{l s='Forgot your password?'}</a>
                <p class="submit">
                    {if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'html':'UTF-8'}" />{/if}
                    <button type="submit" id="SubmitLogin" name="SubmitLogin" class="button btn btn-default button-medium"><span><i class="icon-lock left"></i>{l s='Sign in'}</span></button>
                </p>
            </div>
        </fieldset>
    </form>

    <form action="{$link->getModuleLink('quotes', 'QuotesCart', array(), true)|escape:'html':'UTF-8'}" method="post" id="new_account_form" class="std" autocomplete="on" autofill="on">
        <fieldset>
            <div class="box">
                <h3 id="new_account_title" class="page-subheading">{l s='New Customer'}</h3>
                <div id="opc_account_choice" class="row">
                    <div class="col-xs-12 col-md-6">
                        <p class="title_block">{l s='Instant Checkout'}</p>
                        <p class="opc-button">
                            <button type="submit" class="btn btn-default button button-medium exclusive" id="opc_guestCheckout"><span>{l s='Guest checkout'}</span></button>
                        </p>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <p class="title_block">{l s='Create your account today and enjoy:'}</p>
                        <ul class="bullet">
                            <li>- {l s='Personalized and secure access'}</li>
                            <li>- {l s='A fast and easy check out process'}</li>
                            <li>- {l s='Separate billing and shipping addresses'}</li>
                        </ul>
                        <p class="opc-button">
                            <button type="submit" class="btn btn-default button button-medium exclusive" id="opc_createAccount"><span><i class="icon-user left"></i>{l s='Create an account'}</span></button>
                        </p>
                    </div>
                </div>
                <div id="opc_account_form" class="">
                    <!-- Error return block -->
                    <div id="opc_account_errors" class="alert alert-danger" style="display:none;"></div>
                    {if isset($authentification_error)}
                    <div class="alert alert-danger">
                        {*{if {$authentification_error|@count} == 1}*}
                            {*<p>{l s='There\'s at least one error'} :</p>*}
                            {*{else}*}
                            {*<p>{l s='There are %s errors' sprintf=[$account_error|@count]} :</p>*}
                        {*{/if}*}
                        <ol>
                            {foreach from=$authentification_error item=v}
                                <li>{$v}</li>
                            {/foreach}
                        </ol>
                    </div>
                    {/if}
                    <!-- END Error return block -->
                    <!-- Account -->
                    <input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
                    <input type="hidden" class="hidden" name="back" value="addresses">
                    {*<input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && isset($guestInformations.id_customer) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />*}
                    {*<input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && isset($guestInformations.id_address_delivery) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />*}
                    {*<input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && isset($guestInformations.id_address_delivery) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />*}
                    <div class="required text form-group">
                        <label for="email">{l s='Email'} <sup>*</sup></label>
                        <input type="text" class="text form-control validate" id="email" name="email" data-validate="isEmail" value="{if isset($guestInformations) && isset($guestInformations.email) && $guestInformations.email}{$guestInformations.email}{/if}" />
                    </div>
                    <div class="required password is_customer_param form-group">
                        <label for="passwd">{l s='Password'} <sup>*</sup></label>
                        <input type="password" class="text form-control validate" name="passwd" id="passwd" data-validate="isPasswd" />
                        <span class="form_info">{l s='(five characters min.)'}</span>
                    </div>

                    <div class="required form-group">
                        <label for="firstname">{l s='First name'} <sup>*</sup></label>
                        <input type="text" class="text form-control validate" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.customer_firstname) && $guestInformations.customer_firstname}{$guestInformations.customer_firstname}{/if}" />
                    </div>
                    <div class="required form-group">
                        <label for="lastname">{l s='Last name'} <sup>*</sup></label>
                        <input type="text" class="form-control validate" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.customer_lastname) && $guestInformations.customer_lastname}{$guestInformations.customer_lastname}{/if}" />
                    </div>

                    {if !$ADDRESS_ENABLED}
                        {if !$PS_GUEST_QUOTES_ENABLED}
                            <div class="required form-group">
                                <p class="title_block">{l s='Add the address'}</p>
                                <p class="opc-button">
                                    <a id="show_address_form" class="btn btn-default button button-medium exclusive" id="opc_guestCheckout"><span>{l s='Add the address'}</span></a>
                                </p>
                            </div>
                        {/if}
                    {/if}

                    <div id="address_block" {if !$ADDRESS_ENABLED}{if !$PS_GUEST_QUOTES_ENABLED}style="display: none"{/if}{/if}>
                        <h3 class="page-subheading top-indent">{l s='Delivery address'}</h3>
                        <input type="hidden" class="hidden" name="address_enabled" value="1">
                        {$stateExist = false}
                        {$postCodeExist = false}
                        {$dniExist = false}
                        {foreach from=$dlv_all_fields item=field_name}
                            {if $field_name eq "company" && $b2b_enable}
                                <div class="text form-group">
                                    <label for="company">{l s='Company'}</label>
                                    <input type="text" class="text form-control validate" id="company" name="company" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.company) && $guestInformations.company}{$guestInformations.company}{/if}" />
                                </div>
                            {elseif $field_name eq "vat_number"}
                                <div id="vat_number_block" style="display:none;">
                                    <div class="form-group">
                                        <label for="vat_number">{l s='VAT number'}</label>
                                        <input type="text" class="text form-control" name="vat_number" id="vat_number" value="{if isset($guestInformations) && isset($guestInformations.vat_number) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />
                                    </div>
                                </div>
                            {elseif $field_name eq "dni"}
                                {assign var='dniExist' value=true}
                                <div class="required dni form-group">
                                    <label for="dni">{l s='Identification number'} <sup>*</sup></label>
                                    <input type="text" class="text form-control validate" name="dni" id="dni" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
                                    <span class="form_info">{l s='DNI / NIF / NIE'}</span>
                                </div>
                            {elseif $field_name eq "firstname"}
                                <div class="required text form-group">
                                    <label for="firstname">{l s='First name'} <sup>*</sup></label>
                                    <input type="text" class="text form-control validate" id="firstname" name="firstname" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.firstname) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
                                </div>
                            {elseif $field_name eq "lastname"}
                                <div class="required text form-group">
                                    <label for="lastname">{l s='Last name'} <sup>*</sup></label>
                                    <input type="text" class="text form-control validate" id="lastname" name="lastname" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.lastname) && $guestInformations.lastname}{$guestInformations.lastname}{/if}" />
                                </div>
                            {elseif $field_name eq "address1"}
                                <div class="required text form-group">
                                    <label for="address1">{l s='Address'} <sup>*</sup></label>
                                    <input type="text" class="text form-control validate" name="address1" id="address1" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address1) && isset($guestInformations) && isset($guestInformations.address1) && $guestInformations.address1}{$guestInformations.address1}{/if}" />
                                </div>
                            {elseif $field_name eq "address2"}
                                <div class="text is_customer_param form-group">
                                    <label for="address2">{l s='Address (Line 2)'}</label>
                                    <input type="text" class="text form-control validate" name="address2" id="address2" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address2) && isset($guestInformations) && isset($guestInformations.address2) && $guestInformations.address2}{$guestInformations.address2}{/if}" />
                                </div>
                            {elseif $field_name eq "postcode"}
                                {$postCodeExist = true}
                                <div class="required postcode text form-group">
                                    <label for="postcode">{l s='Zip/Postal code'} <sup>*</sup></label>
                                    <input type="text" class="text form-control validate" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
                                </div>
                            {elseif $field_name eq "city"}
                                <div class="required text form-group">
                                    <label for="city">{l s='City'} <sup>*</sup></label>
                                    <input type="text" class="text form-control validate" name="city" id="city" data-validate="isCityName" value="{if isset($guestInformations) && isset($guestInformations.city) && $guestInformations.city}{$guestInformations.city}{/if}" />
                                </div>
                            {elseif $field_name eq "country" || $field_name eq "Country:name"}
                                <div class="required select form-group">
                                    <label for="id_country">{l s='Country'} <sup>*</sup></label>
                                    <select name="id_country" id="id_country" class="form-control">
                                        {foreach from=$countries item=v}
                                            <option value="{$v.id_country}"{if (isset($guestInformations) && isset($guestInformations.id_country) && $guestInformations.id_country == $v.id_country) || (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            {elseif $field_name eq "state" || $field_name eq 'State:name'}
                                {$stateExist = true}
                                <div class="required id_state form-group" style="display:none;">
                                    <label for="id_state">{l s='State'} <sup>*</sup></label>
                                    <select name="id_state" id="id_state" class="form-control">
                                        <option value="">-</option>
                                    </select>
                                </div>
                            {/if}
                        {/foreach}
                        {if !$postCodeExist}
                            <div class="required postcode form-group unvisible">
                                <label for="postcode">{l s='Zip/Postal code'} <sup>*</sup></label>
                                <input type="text" class="text form-control validate" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
                            </div>
                        {/if}
                        {if !$stateExist}
                            <div class="required id_state form-group unvisible">
                                <label for="id_state">{l s='State'} <sup>*</sup></label>
                                <select name="id_state" id="id_state" class="form-control">
                                    <option value="">-</option>
                                </select>
                            </div>
                        {/if}
                        {if !$dniExist}
                            <div class="required dni form-group">
                                <label for="dni">{l s='Identification number'} <sup>*</sup></label>
                                <input type="text" class="text form-control validate" name="dni" id="dni" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
                                <span class="form_info">{l s='DNI / NIF / NIE'}</span>
                            </div>
                        {/if}
                        <div class="form-group is_customer_param">
                            <label for="other">{l s='Additional information'}</label>
                            <textarea class="form-control" name="other" id="other" cols="26" rows="7"></textarea>
                        </div>
                        {if isset($one_phone_at_least) && $one_phone_at_least}
                            <p class="inline-infos required is_customer_param">{l s='You must register at least one phone number.'}</p>
                        {/if}
                        <div class="form-group is_customer_param">
                            <label for="phone">{l s='Home phone'}</label>
                            <input type="text" class="text form-control validate" name="phone" id="phone"  data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone) && $guestInformations.phone}{$guestInformations.phone}{/if}" />
                        </div>
                        <div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
                            <label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
                            <input type="text" class="text form-control validate" name="phone_mobile" id="phone_mobile" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile) && $guestInformations.phone_mobile}{$guestInformations.phone_mobile}{/if}" />
                        </div>
                        <input type="hidden" name="alias" id="alias" value="{l s='My address'}"/>
                    </div>

                    {$HOOK_CREATE_ACCOUNT_FORM}


                    <div class="submit opc-add-save clearfix">
                        <p class="required opc-required pull-right">
                            <sup>*</sup>{l s='Required field'}
                        </p>
                        {if !$PS_GUEST_QUOTES_ENABLED}
                            <button type="submit" name="submitAccount" id="submitAccount" class="btn btn-default button button-medium"><span>{l s='Save'}<i class="icon-chevron-right right"></i></span></button>
                        {else}
                            <button type="submit" name="submitGuestAccount" id="submitGuestAccount" class="btn btn-default button button-medium"><span>{l s='Save'}<i class="icon-chevron-right right"></i></span></button>
                        {/if}

                    </div>
                    <div style="display: none;" id="opc_account_saved" class="alert alert-success">
                        {l s='Account information saved successfully'}
                    </div>
                <!-- END Account -->
                </div>
            </div>
        </fieldset>
    </form>
</div>
{strip}
    {if isset($guestInformations) && isset($guestInformations.id_state) && $guestInformations.id_state}
        {addJsDef idSelectedState=$guestInformations.id_state|intval}
    {else}
        {addJsDef idSelectedState=false}
    {/if}
    {if isset($guestInformations) && isset($guestInformations.id_state_invoice) && $guestInformations.id_state_invoice}
        {addJsDef idSelectedStateInvoice=$guestInformations.id_state_invoice|intval}
    {else}
        {addJsDef idSelectedStateInvoice=false}
    {/if}
    {if isset($guestInformations) && isset($guestInformations.id_country) && $guestInformations.id_country}
        {addJsDef idSelectedCountry=$guestInformations.id_country|intval}
    {else}
        {addJsDef idSelectedCountry=false}
    {/if}
    {if isset($guestInformations) && isset($guestInformations.id_country_invoice) && $guestInformations.id_country_invoice}
        {addJsDef idSelectedCountryInvoice=$guestInformations.id_country_invoice|intval}
    {else}
        {addJsDef idSelectedCountryInvoice=false}
    {/if}
    {if isset($countries)}
        {addJsDef countries=$countries}
    {/if}
    {if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
        {addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
    {/if}
{/strip}
