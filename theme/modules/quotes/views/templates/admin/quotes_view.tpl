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
    <div class="panel-footer">
        <button onclick="javascript:history.go(-1);" class="btn btn-default pull-right" ><i class="icon-chevron-left"></i> {l s="Back"}</button>
    </div>
</div>