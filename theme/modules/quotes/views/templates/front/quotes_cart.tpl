{capture name=path}{l s='Ask for a Quote' mod='quotes'}{/capture}

{if isset($empty)}
    <p class="alert alert-warning">{l s='No quotes' mod='quotes'}</p>
{else}
    {if isset($isLogged) AND $isLogged == 1}
        <a id="qsubmitnow" class="button" href="{$base_dir}modules/askforaquote/frontoffice/askforaquote.php?gofinal=1" title="{l s='Submit without preview' mod='askforaquote'}">{l s='Submit now' mod='quotes'}</a>
        <form method="post" action="{$base_dir}modules/askforaquote/frontoffice/askforaquote.php">
            <input type="submit" name="submitbox" id="submitbox" title="{l s='View detailed list before submit' mod='askforaquote'}" value="{l s='View list' mod='askforaquote'}" class="button exclusive" />
        </form>
    {else}
        <form method="post" action="{$base_dir}modules/askforaquote/frontoffice/askforaquote.php">
            <input type="submit" name="submitbox" id="submitbox" value="{l s='Submit Quote' mod='quotes'}" class="button exclusive" />
        </form>
    {/if}
{else}