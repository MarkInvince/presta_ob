<tr>
    <td class="text-center">{$quote.id_quote}</td>
    <td class="text-center">{$quote.quote_name}</td>
    <td class="text-center"><a target="_blank" href="{$quote.customer.link}">{$quote.customer.name}</a></td>
    <td class="text-center">{count($quote.products)}</td>
    <td class="text-center">{$quote.date_add}</td>
    <td class="text-center">{if $quote.submited == 1}<i class="icon-ok"></i>{else}<i class="icon-remove"></i> {/if}</td>
    <td class="text-center">
        <form action="{$index}" method="post" class="action_form">
            <input type="hidden" name="id_customer" value="{$quote.customer.id}" />
            <input type="hidden" name="id_quote" value="{$quote.id_quote}" />
            <div class="btn-group">
                <button type="button" class="btn btn-default view_quote" >
                    <i class="icon-pencil"></i>
                    {l s="View" mod="quotes"}
                </button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="javascript:void(0);" class="delete_quote">
                            <i class="icon-trash"></i>
                            {l s="Delete" mod="quotes"}
                        </a>
                    </li>
                </ul>
            </div>
        </form>
    </td>
</tr>