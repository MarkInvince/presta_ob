<script type="text/javascript">
    var confirmDelete   = '{l s="Are you sure you want delete?" mod="quotes"}';
    var adminQuotesUrl = "{$index}";
</script>
<div class="row panel">
    <h3><i class="icon-hand-right"></i> {l s='Quote request:' mod="quotes"} #{$quote[0]['id_quote']}</h3>
<br/>

<div class="col-lg-6 panel admin-panel">
    <h3><i class="icon-hand-right"></i> {l s='Requisites' mod="quotes"}</h3>
</div>
<div class="col-lg-6 panel">
    <h3><i class="icon-legal"></i> {l s='View guote' mod="quotes"}</h3>
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
        {foreach $quote['products'] as $product}
            <tr>
                <td>{$product.id}</td>
                <td>{$product.name}</td>
                <td>{$product.unit_price}</td>
                <td>{$product.quantity}</td>
                <td>{$product.total}</td>
            </tr>
        {/foreach}
    </table>
</div>

<div class="col-lg-12 panel">
    <form id="bargain_form" class="defaultForm form-horizontal AdminCustomers" action="{$index}" method="post">
        <input type="hidden" name="id_quote" value="{$id_quote}"/>
        <input type="hidden" name="id_customer" value="{$id_customer}">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-user"></i> {l s='Bargain form' mod='quotes'}
            </div>
            <div class="form-wrapper">
                <div class="form-group">
                    <label class="control-label col-lg-3 required">{l s='Bargain message' mod='quotes'}</label>
                    <div class="col-lg-4 ">
                        <textarea name="bargain_text" class="textarea-autosize" style=""></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Bargain price' mod='quotes'}</label>
                    <div class="input-group col-lg-2">
                        <span class="input-group-addon">{$currency}</span>
                        <input maxlength="14" name="bargain_price" id="bargain_price" type="text">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='The offer' mod='quotes'}</label>
                    <div class="col-lg-4 ">
                        <input type="text" name="bargain_price_text" id="bargain_price_text" value="" class="">
                    </div>
                </div>

                <div class="form-group col-lg-7">
                    <button type="submit" id="addClientBargain" name="addClientBargain" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Add' mod='quotes'}
                    </button>
                </div>
                <div class="clearfix"></div>
            </div>
            <!-- /.form-wrapper -->
        </div>
    </form>

</div>

<div class="col-lg-12 panel">
    <h3><i class="icon-list-ul"></i> {l s='Quote bargains' mod='quotes'}</h3>
    {if $bargains && count($bargains) > 0}
        <ul class="bargains_list">
            {foreach from=$bargains item=bargain}
                {if $bargain.bargain_whos == 'customer'}
                    <li class="customer_bargain clearfix">
                        <div class="row">
                            <div class="bargain_heading clearfix">
                                <div class="date col-xs-9">
                                    <p class="bargain_whos">{$quote.customer.name} {l s='bargain:' mod='quotes'}</p>
                                </div>
                                <div class="date col-xs-3">
                                    <strong>{l s='Added:' mod='quotes'}</strong> {$bargain.date_add}
                                </div>
                            </div>
                            <div class="bargain_message col-xs-12 box">{$bargain.bargain_text}</div>
                        </div>
                    </li>
                {else}
                    <li class="admin_bargain clearfix">
                        <div class="row">
                            <div class="bargain_heading clearfix">
                                <div class="date col-xs-9">
                                    <p class="bargain_whos">{l s='Administrator bargain message:' mod='quotes'}</p>
                                </div>
                                <div class="date col-xs-3">
                                    <strong>{l s='Added:' mod='quotes'}</strong> {$bargain.date_add}
                                </div>
                            </div>
                            {if $bargain.bargain_text}
                                <div class="bargain_message col-xs-12 box">{$bargain.bargain_text}</div>
                            {/if}
                            {if $bargain.bargain_price != 0}
                                <div class="col-xs-6 bargain_price_container clearfix">
                                    <table class="table">
                                        <tr>
                                            <td>{l s='Your price offer' mod="quotes"}</td>
                                            <td class="price">{$bargain.bargain_price_display}</td>
                                        </tr>
                                        {if $bargain.bargain_price_text}
                                            <tr>
                                                <td>{l s='The offer' mod="quotes"}</td>
                                                <td>{$bargain.bargain_price_text}</td>
                                            </tr>
                                        {/if}
                                    </table>

                                    <div class="bargain_alerts">
                                        <div id="success_bargain_{$bargain.id_bargain}"
                                             {if $bargain.bargain_customer_confirm == 1}style="display: block"{/if}
                                             class="alert alert-success">
                                            {l s='Bargain offer accepted' mod='quotes'}
                                        </div>
                                        <div id="reject_bargain_{$bargain.id_bargain}"
                                             {if $bargain.bargain_customer_confirm == 2}style="display: block"{/if}
                                             class="alert alert-warning">
                                            {l s='Bargain offer rejected' mod='quotes'}
                                        </div>
                                        <div id="danger_bargain_{$bargain.id_bargain}" class="alert alert-danger">
                                            {l s='Something wrong, try again' mod='quotes'}
                                        </div>
                                    </div>
                                </div>
                                {if !$bargain.bargain_customer_confirm}
                                    <div class="col-lg-1 bargain_delete">
                                        <form  action="{$index}" method="post" class="burgainSubmitForm std">
                                            <a data-action="deleteBargain" data-id="{$bargain.id_bargain}" class="btn btn-default deleteBargainOffer">
                                                <i class="icon-trash"></i><span> {l s='Delete' mod='quotes'}</span>
                                            </a>
                                        </form>
                                    </div>
                                {/if}
                            {/if}
                        </div>
                    </li>
                {/if}
            {/foreach}
        </ul>
    {else}
        <p class="alert alert-warning">{l s='There are no any bargains yet' mod='quotes'}</p>
    {/if}
</div>


<div class="panel-footer">
    <button onclick="javascript:history.go(-1);" class="btn btn-default pull-right"><i
                class="icon-chevron-left"></i> {l s="Back"}</button>
</div>
