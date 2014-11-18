<div class="row panel">
    <h3><i class="icon-hand-right"></i> {l s='Quote request:' mod="quotes"} #{$quote[0]['id_quote']}</h3>
    <br/>
    <div class="col-lg-12 panel admin-panel">
        <h3><i class="icon-user"></i> {l s='Requisites' mod="quotes"}</h3>

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
                    <td><h4>{l s="Quote total:"}</h4></td>
                    <td><h4>{$quote['quote_total']['quote_normal']}<h4></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="panel-footer">
        <button onclick="javascript:history.go(-1);" class="btn btn-default pull-right" ><i class="icon-chevron-left"></i> {l s="Back"}</button>
    </div>
</div>