{**
 * ClearDeal - Settings Tab Template
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 *}

<form action="{$admin_link}&tab=settings" method="post" enctype="multipart/form-data" class="cleardeal-form">

    {* Display Settings *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">visibility</i>
            <h3>{l s='Display Settings' mod='cleardeal'}</h3>
        </div>
        <div class="cleardeal-card-body">
            <div class="cleardeal-row">
                <div class="cleardeal-field">
                    <label>{l s='Number of days to analyze' mod='cleardeal'}</label>
                    <input type="number" name="CLEARDEAL_DAYS" value="{$settings.CLEARDEAL_DAYS|escape:'html':'UTF-8'}" min="1" max="365" class="cleardeal-input cleardeal-input-sm">
                    <span class="cleardeal-hint">{l s='Default: 30 days (EU Omnibus requirement)' mod='cleardeal'}</span>
                </div>

                <div class="cleardeal-field">
                    <label>{l s='Display mode' mod='cleardeal'}</label>
                    <select name="CLEARDEAL_DISPLAY_MODE" class="cleardeal-select">
                        <option value="always" {if $settings.CLEARDEAL_DISPLAY_MODE == 'always'}selected{/if}>{l s='Always show' mod='cleardeal'}</option>
                        <option value="on-discount" {if $settings.CLEARDEAL_DISPLAY_MODE == 'on-discount'}selected{/if}>{l s='Only when product is discounted' mod='cleardeal'}</option>
                    </select>
                </div>

                <div class="cleardeal-field">
                    <label>{l s='Price type' mod='cleardeal'}</label>
                    <select name="CLEARDEAL_PRICE_TYPE" class="cleardeal-select">
                        <option value="gross" {if $settings.CLEARDEAL_PRICE_TYPE == 'gross'}selected{/if}>{l s='Gross (with tax)' mod='cleardeal'}</option>
                        <option value="net" {if $settings.CLEARDEAL_PRICE_TYPE == 'net'}selected{/if}>{l s='Net (without tax)' mod='cleardeal'}</option>
                    </select>
                </div>
            </div>

            <div class="cleardeal-row">
                <div class="cleardeal-field">
                    <label>{l s='Show percentage change' mod='cleardeal'}</label>
                    <label class="cleardeal-switch">
                        <input type="checkbox" name="CLEARDEAL_SHOW_PERCENT" value="1" {if $settings.CLEARDEAL_SHOW_PERCENT}checked{/if}>
                        <span class="cleardeal-switch-slider"></span>
                    </label>
                </div>

                <div class="cleardeal-field">
                    <label>{l s='Percentage decimal places' mod='cleardeal'}</label>
                    <input type="number" name="CLEARDEAL_PRECISION" value="{$settings.CLEARDEAL_PRECISION|escape:'html':'UTF-8'}" min="0" max="4" class="cleardeal-input cleardeal-input-xs">
                </div>
            </div>
        </div>
    </div>

    {* Price History Chart *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">show_chart</i>
            <h3>{l s='Price History Chart' mod='cleardeal'}</h3>
        </div>
        <div class="cleardeal-card-body">
            <p class="cleardeal-info">
                <i class="material-icons">info</i>
                {l s='Enable an interactive price history chart that customers can view on product pages. The chart shows price changes over the configured period.' mod='cleardeal'}
            </p>

            <div class="cleardeal-row">
                <div class="cleardeal-field">
                    <label>{l s='Show price history chart' mod='cleardeal'}</label>
                    <label class="cleardeal-switch">
                        <input type="checkbox" name="CLEARDEAL_SHOW_CHART" value="1" {if $settings.CLEARDEAL_SHOW_CHART}checked{/if} id="show_chart_toggle">
                        <span class="cleardeal-switch-slider"></span>
                    </label>
                </div>

                <div class="cleardeal-field chart-options" {if !$settings.CLEARDEAL_SHOW_CHART}style="display:none;"{/if}>
                    <label>{l s='Chart trigger' mod='cleardeal'}</label>
                    <select name="CLEARDEAL_CHART_TRIGGER" class="cleardeal-select">
                        <option value="icon" {if $settings.CLEARDEAL_CHART_TRIGGER == 'icon'}selected{/if}>{l s='Click on icon' mod='cleardeal'}</option>
                        <option value="price" {if $settings.CLEARDEAL_CHART_TRIGGER == 'price'}selected{/if}>{l s='Click on price' mod='cleardeal'}</option>
                        <option value="all" {if $settings.CLEARDEAL_CHART_TRIGGER == 'all'}selected{/if}>{l s='Click anywhere on element' mod='cleardeal'}</option>
                    </select>
                    <span class="cleardeal-hint">{l s='Choose which element opens the price history chart.' mod='cleardeal'}</span>
                </div>
            </div>
        </div>
    </div>

    {* Category Exclusions *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">block</i>
            <h3>{l s='Category Exclusions' mod='cleardeal'}</h3>
        </div>
        <div class="cleardeal-card-body">
            <p class="cleardeal-info">
                <i class="material-icons">info</i>
                {l s='Products from excluded categories will not have price history logged and will not display the lowest price information.' mod='cleardeal'}
            </p>

            <div class="cleardeal-field">
                <label>{l s='Excluded categories' mod='cleardeal'}</label>
                <div class="cleardeal-excluded-categories">
                    {if isset($excluded_categories) && $excluded_categories|count > 0}
                        <div class="cleardeal-category-tags" id="excluded_tags">
                            {foreach from=$excluded_categories item=cat}
                                <span class="cleardeal-tag">
                                    {$cat.name|escape:'html':'UTF-8'}
                                    <button type="button" class="cleardeal-tag-remove" data-id="{$cat.id}">
                                        <i class="material-icons">close</i>
                                    </button>
                                </span>
                            {/foreach}
                        </div>
                    {else}
                        <div class="cleardeal-category-tags" id="excluded_tags">
                            <span class="cleardeal-no-exclusions">{l s='No categories excluded' mod='cleardeal'}</span>
                        </div>
                    {/if}

                    <div class="cleardeal-category-add">
                        <select id="category_select" class="cleardeal-select">
                            <option value="">{l s='Select category to exclude...' mod='cleardeal'}</option>
                            {foreach from=$all_categories item=cat}
                                <option value="{$cat.id_category}" {if in_array($cat.id_category, $excluded_category_ids)}disabled{/if}>
                                    {$cat.level_depth|str_repeat:'— '}{$cat.name|escape:'html':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                        <button type="button" class="cleardeal-btn cleardeal-btn-secondary" id="add_category_btn">
                            <i class="material-icons">add</i>
                            {l s='Add' mod='cleardeal'}
                        </button>
                    </div>

                    <input type="hidden" name="CLEARDEAL_EXCLUDED_CATEGORIES" id="excluded_categories_input" value="{$settings.CLEARDEAL_EXCLUDED_CATEGORIES|escape:'html':'UTF-8'}">
                </div>
            </div>
        </div>
    </div>

    {* Labels *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">label</i>
            <h3>{l s='Labels' mod='cleardeal'}</h3>
            <div class="cleardeal-lang-switcher">
                {foreach from=$languages item=lang}
                    <button type="button" class="cleardeal-lang-btn {if $lang.id_lang == $default_lang}active{/if}" data-lang="{$lang.id_lang}">
                        {$lang.iso_code|upper}
                    </button>
                {/foreach}
            </div>
        </div>
        <div class="cleardeal-card-body">
            <p class="cleardeal-info">
                <i class="material-icons">info</i>
                {l s='Use %s to insert the number of days. Example: "Lowest price in last %s days:"' mod='cleardeal'}
            </p>

            <div class="cleardeal-field">
                <label>{l s='Product page label' mod='cleardeal'}</label>
                {foreach from=$languages item=lang}
                    <input type="text"
                           name="CLEARDEAL_LABEL_PRODUCT_{$lang.id_lang}"
                           value="{$settings.CLEARDEAL_LABEL_PRODUCT[$lang.id_lang]|escape:'html':'UTF-8'}"
                           class="cleardeal-input cleardeal-lang-input"
                           data-lang="{$lang.id_lang}"
                           {if $lang.id_lang != $default_lang}style="display:none;"{/if}>
                {/foreach}
            </div>

            <div class="cleardeal-field">
                <label>{l s='Listing label' mod='cleardeal'}</label>
                {foreach from=$languages item=lang}
                    <input type="text"
                           name="CLEARDEAL_LABEL_LISTING_{$lang.id_lang}"
                           value="{$settings.CLEARDEAL_LABEL_LISTING[$lang.id_lang]|escape:'html':'UTF-8'}"
                           class="cleardeal-input cleardeal-lang-input"
                           data-lang="{$lang.id_lang}"
                           {if $lang.id_lang != $default_lang}style="display:none;"{/if}>
                {/foreach}
            </div>

            <div class="cleardeal-field">
                <label>{l s='Quick view label' mod='cleardeal'}</label>
                {foreach from=$languages item=lang}
                    <input type="text"
                           name="CLEARDEAL_LABEL_QUICKVIEW_{$lang.id_lang}"
                           value="{$settings.CLEARDEAL_LABEL_QUICKVIEW[$lang.id_lang]|escape:'html':'UTF-8'}"
                           class="cleardeal-input cleardeal-lang-input"
                           data-lang="{$lang.id_lang}"
                           {if $lang.id_lang != $default_lang}style="display:none;"{/if}>
                {/foreach}
            </div>
        </div>
    </div>

    {* Icon Settings *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">image</i>
            <h3>{l s='Icon Settings' mod='cleardeal'}</h3>
            <div class="cleardeal-lang-switcher icon-options" {if !$settings.CLEARDEAL_SHOW_ICON}style="display:none;"{/if}>
                {foreach from=$languages item=lang}
                    <button type="button" class="cleardeal-lang-btn {if $lang.id_lang == $default_lang}active{/if}" data-lang="{$lang.id_lang}">
                        {$lang.iso_code|upper}
                    </button>
                {/foreach}
            </div>
        </div>
        <div class="cleardeal-card-body">
            <div class="cleardeal-row">
                <div class="cleardeal-field">
                    <label>{l s='Show icon' mod='cleardeal'}</label>
                    <label class="cleardeal-switch">
                        <input type="checkbox" name="CLEARDEAL_SHOW_ICON" value="1" {if $settings.CLEARDEAL_SHOW_ICON}checked{/if} id="show_icon_toggle">
                        <span class="cleardeal-switch-slider"></span>
                    </label>
                </div>

                <div class="cleardeal-field icon-options" {if !$settings.CLEARDEAL_SHOW_ICON}style="display:none;"{/if}>
                    <label>{l s='Icon position' mod='cleardeal'}</label>
                    <select name="CLEARDEAL_ICON_POSITION" class="cleardeal-select">
                        <option value="start" {if $settings.CLEARDEAL_ICON_POSITION == 'start'}selected{/if}>{l s='Before text' mod='cleardeal'}</option>
                        <option value="end" {if $settings.CLEARDEAL_ICON_POSITION == 'end'}selected{/if}>{l s='After text' mod='cleardeal'}</option>
                    </select>
                </div>
            </div>

            <div class="icon-options" {if !$settings.CLEARDEAL_SHOW_ICON}style="display:none;"{/if}>
                <div class="cleardeal-field">
                    <label>{l s='Icon type' mod='cleardeal'}</label>
                    <div class="cleardeal-radio-group">
                        <label class="cleardeal-radio">
                            <input type="radio" name="CLEARDEAL_ICON_TYPE" value="bootstrap" {if $settings.CLEARDEAL_ICON_TYPE == 'bootstrap'}checked{/if}>
                            {l s='Bootstrap Icons' mod='cleardeal'}
                        </label>
                        <label class="cleardeal-radio">
                            <input type="radio" name="CLEARDEAL_ICON_TYPE" value="file" {if $settings.CLEARDEAL_ICON_TYPE == 'file'}checked{/if}>
                            {l s='Custom image' mod='cleardeal'}
                        </label>
                    </div>
                </div>

                <div class="cleardeal-field icon-bootstrap-options" {if $settings.CLEARDEAL_ICON_TYPE != 'bootstrap'}style="display:none;"{/if}>
                    <label>{l s='Icon name' mod='cleardeal'}</label>
                    <div class="cleardeal-input-with-preview">
                        <input type="text" name="CLEARDEAL_ICON_CLASS" value="{$settings.CLEARDEAL_ICON_CLASS|escape:'html':'UTF-8'}" class="cleardeal-input" placeholder="info-circle">
                        <span class="cleardeal-icon-preview">
                            <i class="bi bi-{$settings.CLEARDEAL_ICON_CLASS|escape:'html':'UTF-8'}" id="icon_preview"></i>
                        </span>
                    </div>
                    <span class="cleardeal-hint">
                        {l s='See available icons at' mod='cleardeal'} <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a>
                    </span>
                </div>

                <div class="cleardeal-field icon-file-options" {if $settings.CLEARDEAL_ICON_TYPE != 'file'}style="display:none;"{/if}>
                    <label>{l s='Upload icon image' mod='cleardeal'}</label>
                    <input type="file" name="CLEARDEAL_ICON_IMAGE" accept="image/*" class="cleardeal-file-input">
                    {if $settings.CLEARDEAL_ICON_IMAGE}
                        <div class="cleardeal-current-icon">
                            <img src="{$module_dir}views/img/{$settings.CLEARDEAL_ICON_IMAGE}" alt="Current icon">
                            <label class="cleardeal-checkbox">
                                <input type="checkbox" name="delete_icon" value="1">
                                <span>{l s='Delete current icon' mod='cleardeal'}</span>
                            </label>
                        </div>
                    {/if}
                </div>

                <div class="cleardeal-field">
                    <label>{l s='Tooltip text' mod='cleardeal'}</label>
                    {foreach from=$languages item=lang}
                        <textarea name="CLEARDEAL_ICON_TOOLTIP_{$lang.id_lang}"
                                  class="cleardeal-textarea cleardeal-lang-input"
                                  data-lang="{$lang.id_lang}"
                                  rows="2"
                                  {if $lang.id_lang != $default_lang}style="display:none;"{/if}>{$settings.CLEARDEAL_ICON_TOOLTIP[$lang.id_lang]|escape:'html':'UTF-8'}</textarea>
                    {/foreach}
                    <span class="cleardeal-hint">{l s='Appears when hovering over the icon. Use %s for days.' mod='cleardeal'}</span>
                </div>
            </div>
        </div>
    </div>

    {* Advanced Settings *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">tune</i>
            <h3>{l s='Advanced Settings' mod='cleardeal'}</h3>
        </div>
        <div class="cleardeal-card-body">
            <div class="cleardeal-row">
                <div class="cleardeal-field">
                    <label>{l s='Price logging mode' mod='cleardeal'}</label>
                    <select name="CLEARDEAL_LOGGING_MODE" class="cleardeal-select">
                        <option value="immediate" {if $settings.CLEARDEAL_LOGGING_MODE == 'immediate'}selected{/if}>{l s='Immediate (on product update)' mod='cleardeal'}</option>
                        <option value="cron" {if $settings.CLEARDEAL_LOGGING_MODE == 'cron'}selected{/if}>{l s='CRON job only' mod='cleardeal'}</option>
                    </select>
                </div>

                <div class="cleardeal-field">
                    <label>{l s='Log retention (days)' mod='cleardeal'}</label>
                    <input type="number" name="CLEARDEAL_LOG_RETENTION" value="{$settings.CLEARDEAL_LOG_RETENTION|escape:'html':'UTF-8'}" min="30" max="3650" class="cleardeal-input cleardeal-input-sm">
                    <span class="cleardeal-hint">{l s='Logs older than this will be automatically deleted' mod='cleardeal'}</span>
                </div>
            </div>

            <div class="cleardeal-field cleardeal-cron-field">
                <label>{l s='CRON task URL' mod='cleardeal'}</label>
                <div class="cleardeal-cron-wrapper">
                    <input type="text" value="{$cron_url|escape:'html':'UTF-8'}" class="cleardeal-input cleardeal-cron-url" readonly id="cron_url">
                    <button type="button" class="cleardeal-btn cleardeal-btn-secondary cleardeal-btn-copy" data-copy="cron_url" title="{l s='Copy to clipboard' mod='cleardeal'}">
                        <i class="material-icons">content_copy</i>
                    </button>
                </div>
                <span class="cleardeal-hint">
                    {l s='Set up a CRON job to run this URL daily. This will log prices for all products and clean up old logs.' mod='cleardeal'}
                    <br>
                    <code>0 3 * * * curl -s "{$cron_url|escape:'html':'UTF-8'}" > /dev/null</code>
                </span>
            </div>
        </div>
    </div>

    {* Footer Actions *}
    <div class="cleardeal-footer">
        <button type="submit" name="submitClearDealSettings" class="cleardeal-btn cleardeal-btn-primary">
            <i class="material-icons">save</i>
            {l s='Save Settings' mod='cleardeal'}
        </button>
    </div>
</form>
