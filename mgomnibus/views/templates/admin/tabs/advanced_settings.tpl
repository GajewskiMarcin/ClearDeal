<!-- SECTION: Logging mode -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Logging mode' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>{l s='Choose logging method' mod='mgomnibus'}</label>
            <select name="OMNIBUS_LOGGING_MODE" class="form-control input-md">
                <option value="immediate" {if $settings.OMNIBUS_LOGGING_MODE == 'immediate'}selected{/if}>{l s='Immediate' mod='mgomnibus'}</option>
                <option value="cron" {if $settings.OMNIBUS_LOGGING_MODE == 'cron'}selected{/if}>{l s='CRON' mod='mgomnibus'}</option>
            </select>
            <small class="form-text">{l s='Immediate: Price changes are logged in real-time. CRON: Price changes are logged periodically via scheduled task.' mod='mgomnibus'}</small>
        </div>
    </div>
</div>

<!-- SECTION: Logging scope -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Logging scope' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">

        <div class="form-group">
            <label>{l s='Define which prices to track' mod='mgomnibus'}</label>
            <div class="multiselect-group vertical">
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_LOG_ALL_COUNTRIES" value="1" {if $settings.OMNIBUS_LOG_ALL_COUNTRIES}checked{/if}> {l s='Log for all countries' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_LOG_ALL_GROUPS" value="1" {if $settings.OMNIBUS_LOG_ALL_GROUPS}checked{/if}> {l s='Log for all customer groups' mod='mgomnibus'}
                </label>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: Logging retention -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Logging retention' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>{l s='Delete logs older than (days)' mod='mgomnibus'}</label>
            <input type="number" name="OMNIBUS_LOG_RETENTION" class="input-xs" value="{$settings.OMNIBUS_LOG_RETENTION|escape:'html':'UTF-8'}" min="0" style="width: 100px;">
            <small class="form-text">{l s='Set to 0 to keep logs indefinitely' mod='mgomnibus'}</small>
        </div>
    </div>
</div>

<!-- SECTION: Cron TOKEN -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Cron TOKEN' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>{l s='Current CRON token' mod='mgomnibus'}</label>
            <div class="input-group">
                <input type="text" name="OMNIBUS_CRON_TOKEN" class="form-control" value="{$settings.OMNIBUS_CRON_TOKEN|escape:'html':'UTF-8'}" readonly>
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default" onclick="regenerateToken()">{l s='Generate new' mod='mgomnibus'}</button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>{l s='CRON Task Link' mod='mgomnibus'}</label>
            <div class="input-group">
                <input type="text" class="form-control" value="{$module_dir}cron.php?token={$settings.OMNIBUS_CRON_TOKEN|escape:'html':'UTF-8'}" readonly>
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default" onclick="copyToClipboard(this)">{l s='Copy' mod='mgomnibus'}</button>
                </div>
            </div>
        </div>
    </div>
</div>
