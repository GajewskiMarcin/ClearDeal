{if isset($confirmation)}
    <div class="alert alert-success">{$confirmation}</div>
{/if}

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="mg-omnibus-panel">
    <!-- Tabs Navigation -->
    <div class="panel-tabs">
        <button type="button" class="tab-link active" data-tab="basic">{l s='Basic Settings' mod='mgomnibus'}</button>
        <button type="button" class="tab-link" data-tab="advanced">{l s='Advanced Settings' mod='mgomnibus'}</button>
        <button type="button" class="tab-link" data-tab="appearance">{l s='Appearance Settings' mod='mgomnibus'}</button>
        <button type="button" class="tab-link" data-tab="support">{l s='Tip Jar' mod='mgomnibus'}</button>
    </div>

    <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
        
        <!-- Tab Content: Basic Settings -->
        <div id="tab-basic" class="tab-content active">
            {include file="./tabs/basic_settings.tpl"}
        </div>

        <!-- Tab Content: Advanced Settings -->
        <div id="tab-advanced" class="tab-content">
            {include file="./tabs/advanced_settings.tpl"}
        </div>

        <!-- Tab Content: Appearance Settings -->
        <div id="tab-appearance" class="tab-content">
            {include file="./tabs/appearance_settings.tpl"}
        </div>

        <!-- Tab Content: Support -->
        <div id="tab-support" class="tab-content">
            {include file="./tabs/support.tpl"}
        </div>

        <!-- Footer Actions -->
        <div class="mg-footer-actions">
            <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload();">
                {l s='Cancel' mod='mgomnibus'}
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="if(confirm('{l s='Are you sure you want to reset all settings to default values?' mod='mgomnibus'}')) { document.getElementById('reset-settings').value='1'; this.form.submit(); }">
                {l s='Reset Settings' mod='mgomnibus'}
            </button>
            <button type="submit" name="submitMgOmnibus" class="btn btn-primary">
                {l s='Save Changes' mod='mgomnibus'}
            </button>
            <input type="hidden" id="reset-settings" name="resetSettings" value="0">
        </div>
    </form>
</div>

<script>
    var mg_omnibus_languages = {json_encode($languages)};
</script>
