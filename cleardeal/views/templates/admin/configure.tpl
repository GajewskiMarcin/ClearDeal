{**
 * ClearDeal - Admin Configuration Template
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License 3.0 (AFL-3.0)
 *}

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="cleardeal-panel">
    {* Messages *}
    {if isset($confirmations) && $confirmations|count > 0}
        {foreach from=$confirmations item=msg}
            <div class="cleardeal-alert cleardeal-alert-success">
                <i class="material-icons">check_circle</i>
                {$msg}
            </div>
        {/foreach}
    {/if}

    {if isset($errors) && $errors|count > 0}
        {foreach from=$errors item=msg}
            <div class="cleardeal-alert cleardeal-alert-error">
                <i class="material-icons">error</i>
                {$msg}
            </div>
        {/foreach}
    {/if}

    {* Tabs Navigation (WiseBlock-style) *}
    <div class="cleardeal-tabs">
        <a href="{$admin_link}&tab=settings" class="cleardeal-tab {if $current_tab == 'settings'}active{/if}">
            {l s='Settings' mod='cleardeal'}
        </a>
        <a href="{$admin_link}&tab=appearance" class="cleardeal-tab {if $current_tab == 'appearance'}active{/if}">
            {l s='Appearance' mod='cleardeal'}
        </a>
        <a href="{$admin_link}&tab=logs" class="cleardeal-tab {if $current_tab == 'logs'}active{/if}">
            {l s='Price Logs' mod='cleardeal'}
        </a>
        <a href="{$admin_link}&tab=support" class="cleardeal-tab {if $current_tab == 'support'}active{/if}">
            {l s='About & Support' mod='cleardeal'}
        </a>
    </div>

    {* Tab Content *}
    <div class="cleardeal-content">
        {if $current_tab == 'settings'}
            {include file="./settings_tab.tpl"}
        {elseif $current_tab == 'appearance'}
            {include file="./appearance_tab.tpl"}
        {elseif $current_tab == 'logs'}
            {include file="./logs_tab.tpl"}
        {elseif $current_tab == 'support'}
            {include file="./support_tab.tpl"}
        {/if}
    </div>
</div>

<script>
    var cleardeal_languages = {json_encode($languages)};
    var cleardeal_default_lang = {$default_lang};
    var cleardeal_current_lang = {$current_lang};
</script>
