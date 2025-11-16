{* modules/ahscarrierzip/views/templates/admin/configure.tpl *}

<form action="{$form_action}" method="post">
    <div class="panel">
        <h3><i class="icon-truck"></i> {$module_display_name|escape:'html':'UTF-8'}</h3>
        <p>
            {l s='Set allowed postal code prefixes for each carrier. Leave empty for no restriction.' mod='ahscarrierzip'}<br>
            {l s='Example: 550 (for all 550xxx postcodes) or 550,551 (multiple prefixes).' mod='ahscarrierzip'}
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th>{l s='ID' mod='ahscarrierzip'}</th>
                    <th>{l s='Carrier name' mod='ahscarrierzip'}</th>
                    <th>{l s='Allowed ZIP prefixes' mod='ahscarrierzip'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$carriers item=carrier}
                <tr>
                    <td>{$carrier.id_carrier}</td>
                    <td>{$carrier.name|escape:'html':'UTF-8'}</td>
                    <td>
                        <input type="text"
                               name="prefix_{$carrier.id_carrier}"
                               value="{$prefixes[$carrier.id_carrier]|escape:'html':'UTF-8'}"
                               class="form-control"
                               placeholder="ex: 550 sau 550,551" />
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>

        <div class="panel-footer">
            <button type="submit" name="submitAhscarrierzip" class="btn btn-primary">
                <i class="icon-save"></i> {l s='Save' mod='ahscarrierzip'}
            </button>
        </div>
    </div>
</form>
