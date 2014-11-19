<div class="row panel">
    <h3><i class="icon-hand-right"></i> {l s='Quote request:' mod="quotes"} #{$quote[0]['id_quote']}</h3>
    <br/>
    <div class="col-lg-12 panel admin-panel">
        <h3><i class="icon-user"></i> {l s='Requisites' mod="quotes"}</h3>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <table class="table">
                            <tr>
                                <td>{l s="Customer Name:" mod="quotes"}</td>
                                <td><strong>{$quote.customer.name}</strong></td>
                            </tr>
                            <tr>
                                <td>{l s="Gender:" mod="quotes"}</td>
                                <td><strong>{if $quote.customer.gender == 1}<i class="icon-male"></i>{elseif $quote.customer.gender == 2}<i class="icon-female"></i>{else}{l s="Not selected"}{/if}</strong></td>
                            </tr>
                            <tr>
                                <td>{l s="Email:" mod="quotes"}</td>
                                <td><strong><a href="mailto:{$quote.customer.email}">{$quote.customer.email}</a></strong></td>
                            </tr>
                            <tr>
                                <td>{l s="Birthday:" mod="quotes"}</td>
                                <td><strong>{if $quote.customer.birthday == '0000-00-00'}{l s="Not specified" mod="quotes"}{else}{$quote.customer.birthday}{/if}</strong></td>
                            </tr>
                            <tr>
                                <td>{l s="Registration date:" mod="quotes"}</td>
                                <td><strong>{$quote.customer.date_add}</strong></td>
                            </tr>
                        </table>
                        <br/>
                        <div class="text-right">
                            <a href="javascript:void(0);" class="btn btn-default"><i class="icon-edit"></i> {l s="Edit" mod="quotes"}</a>
                        </div>
                    </div>
                    <div class="col-lg-1"></div>
                    <div class="col-lg-7">
                        {if count($quote.customer.addresses) > 0}
                            {foreach $quote.customer.addresses as $address}
                                <div class="panel panel-default">
                                    <div class="panel-heading">{$address.alias}</div>
                                    <div class="panel-body">
                                        <table class="table">
                                            <tr>
                                                <td>{l s="Company" mod="quotes"}</td>
                                                <td><strong>{if !empty($address.company)}{$address.company}{else}{l s="Not specified" mod="quotes"}{/if}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="First name, Last name" mod="quotes"}</td>
                                                <td><strong>{$address.firstname} {$address.lastname}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Region" mod="quotes"}</td>
                                                <td><strong>{$address.country}, {$address.state}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Address" mod="quotes"}</td>
                                                <td><strong>{$address.address1} {$address.address2} {$address.city}, {$address.postcode} </strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Phone" mod="quotes"}</td>
                                                <td><strong>{if !empty($address.phone)}{$address.phone}{else}{l s="Not specified" mod="quotes"}{/if}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Phone mobile" mod="quotes"}</td>
                                                <td><strong>{if !empty($address.phonemobile)}{$address.phonemobile}{else}{l s="Not specified" mod="quotes"}{/if}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{l s="Creation date" mod="quotes"}</td>
                                                <td><strong>{$address.date_add}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            {/foreach}
                        {else}
                            <div class="alert alert-warning">
                                {$quote.customer.name} {l s="has not registered any addresses yet"}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 panel">
        <h3><i class="icon-list-ul"></i> {l s='Products list' mod="quotes"}</h3>
        <table class="table">
            <thead>
                <tr>
                    <td>{l s="ID" mod="quotes"}</td>
                    <td>{l s="Name" mod="quotes"}</td>
                    <td>{l s="Unit price" mod="quotes"}</td>
                    <td>{l s="Quantity" mod="quotes"}</td>
                    <td>{l s="Total" mod="quotes"}</td>
                </tr>
            </thead>
            {foreach $quote.products as $product}
                <tr>
                    <td>{$product.id}</td>
                    <td>
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="{$product.link}" target="_blank">
                                    <div class="col-lg-2">
                                        <img src="{$product.image}" class="img-responsive" width="50" height="50" alt="{$product.name}" />
                                    </div>
                                    <div>{$product.name}</div>
                                    <small>{$product.attr}</small>
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>{$product.unit_price}</td>
                    <td>{$product.quantity}</td>
                    <td>{$product.total}</td>
                </tr>
            {/foreach}
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><h4>{l s="Quote total:" mod="quotes"}</h4></td>
                    <td><h4>{$quote['quote_total']['quote_normal']}<h4></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="panel-footer">
        <button onclick="javascript:history.go(-1);" class="btn btn-default pull-right" ><i class="icon-chevron-left"></i> {l s="Back" mod="quotes"}</button>
    </div>
</div>