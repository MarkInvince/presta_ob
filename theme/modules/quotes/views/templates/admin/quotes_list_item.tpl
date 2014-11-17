<tr>
    <td class="text-center">{$quote.id_quote}</td>
    <td class="text-center">{$quote.quote_name}</td>
    <td class="text-center">{}</td>
    <td class="text-center">{count($quote.products)}</td>
    <td class="text-center">{$quote.date_add}</td>
    <td class="text-center">
        <div class="btn-group">
            <button type="button" class="btn btn-default edit_product_change_link">
                <i class="icon-pencil"></i>
                {l s="View" mod="quotes"}
            </button>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="#" class="delete_product_line">
                        <i class="icon-trash"></i>
                        {l s="Delete" mod="quotes"}
                    </a>
                </li>
            </ul>
        </div>

        <button type="button" class="btn btn-default submitProductChange" style="display: none;">
            <i class="icon-ok"></i>
            {l s="Update" mod="quotes"}
        </button>
        <button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
            <i class="icon-remove"></i>
            {l s="Cancel" mod="quotes"}
        </button>
    </td>
</tr>