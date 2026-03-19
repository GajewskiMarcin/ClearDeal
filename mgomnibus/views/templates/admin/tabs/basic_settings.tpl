<!-- SECTION: Display Settings -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Display settings' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>{l s='Number of days to analyze' mod='mgomnibus'}</label>
                    <input type="number" name="OMNIBUS_DAYS" class="form-control input-xs" value="{$settings.OMNIBUS_DAYS|escape:'html':'UTF-8'}">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>{l s='Display mode' mod='mgomnibus'}</label>
                    <select name="OMNIBUS_DISPLAY_MODE" class="form-control">
                        <option value="always" {if $settings.OMNIBUS_DISPLAY_MODE == 'always'}selected{/if}>{l s='Always' mod='mgomnibus'}</option>
                        <option value="on-discount" {if $settings.OMNIBUS_DISPLAY_MODE == 'on-discount'}selected{/if}>{l s='Only when discount/promotion is active' mod='mgomnibus'}</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>{l s='Price type' mod='mgomnibus'}</label>
                    <select name="OMNIBUS_PRICE_TYPE" class="form-control">
                        <option value="gross" {if $settings.OMNIBUS_PRICE_TYPE == 'gross'}selected{/if}>{l s='Gross price' mod='mgomnibus'}</option>
                        <option value="net" {if $settings.OMNIBUS_PRICE_TYPE == 'net'}selected{/if}>{l s='Net price' mod='mgomnibus'}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: Percent change -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Percent change' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>{l s='Show on the views' mod='mgomnibus'}</label>
            <div class="multiselect-group vertical">
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_PERCENT_PRODUCT_DESKTOP" value="1" {if $settings.OMNIBUS_SHOW_PERCENT_PRODUCT_DESKTOP}checked{/if}> {l s='Product page – desktop' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_PERCENT_PRODUCT_MOBILE" value="1" {if $settings.OMNIBUS_SHOW_PERCENT_PRODUCT_MOBILE}checked{/if}> {l s='Product page – mobile' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_PERCENT_LISTING_DESKTOP" value="1" {if $settings.OMNIBUS_SHOW_PERCENT_LISTING_DESKTOP}checked{/if}> {l s='Listing – desktop' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_PERCENT_LISTING_MOBILE" value="1" {if $settings.OMNIBUS_SHOW_PERCENT_LISTING_MOBILE}checked{/if}> {l s='Listing – mobile' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_PERCENT_QUICKVIEW_DESKTOP" value="1" {if $settings.OMNIBUS_SHOW_PERCENT_QUICKVIEW_DESKTOP}checked{/if}> {l s='Quick view – desktop' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_PERCENT_QUICKVIEW_MOBILE" value="1" {if $settings.OMNIBUS_SHOW_PERCENT_QUICKVIEW_MOBILE}checked{/if}> {l s='Quick view – mobile' mod='mgomnibus'}
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>{l s='Show when positive value' mod='mgomnibus'}</label>
            <label class="switch">
                <input type="checkbox" name="OMNIBUS_SHOW_POSITIVE_PERCENT" value="1" {if $settings.OMNIBUS_SHOW_POSITIVE_PERCENT}checked{/if}>
                <span class="slider round"></span>
            </label>
        </div>

        <div class="form-group">
            <label>{l s='Decimal places for percentage value' mod='mgomnibus'}</label>
            <input type="number" name="OMNIBUS_PRECISION" class="input-xs" value="{$settings.OMNIBUS_PRECISION|escape:'html':'UTF-8'}" min="0" max="4" style="width: 80px;">
        </div>
    </div>
</div>

<!-- SECTION: Label text -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Label text' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>{l s='Product page' mod='mgomnibus'}</label>
            <div class="input-group">
                {foreach from=$languages item=lang}
                <input type="text" name="OMNIBUS_LABEL_PRODUCT_{$lang.id_lang}" class="form-control lang-input-wrapper" data-lang="{$lang.id_lang}" value="{$settings.OMNIBUS_LABEL_PRODUCT[$lang.id_lang]|escape:'html':'UTF-8'}" style="display: {if $lang.id_lang == $default_lang}block{else}none{/if};">
                {/foreach}
                <div class="input-group-addon">
                    <span class="current-lang-name">{$languages[0].iso_code|upper}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>{l s='Listing' mod='mgomnibus'}</label>
            <div class="input-group">
                {foreach from=$languages item=lang}
                <input type="text" name="OMNIBUS_LABEL_LISTING_{$lang.id_lang}" class="form-control lang-input-wrapper" data-lang="{$lang.id_lang}" value="{$settings.OMNIBUS_LABEL_LISTING[$lang.id_lang]|escape:'html':'UTF-8'}" style="display: {if $lang.id_lang == $default_lang}block{else}none{/if};">
                {/foreach}
                <div class="input-group-addon">
                    <span class="current-lang-name">{$languages[0].iso_code|upper}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>{l s='Quick view' mod='mgomnibus'}</label>
            <div class="input-group">
                {foreach from=$languages item=lang}
                <input type="text" name="OMNIBUS_LABEL_QUICKVIEW_{$lang.id_lang}" class="form-control lang-input-wrapper" data-lang="{$lang.id_lang}" value="{$settings.OMNIBUS_LABEL_QUICKVIEW[$lang.id_lang]|escape:'html':'UTF-8'}" style="display: {if $lang.id_lang == $default_lang}block{else}none{/if};">
                {/foreach}
                <div class="input-group-addon">
                    <span class="current-lang-name">{$languages[0].iso_code|upper}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION: Icon display -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Icon display' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>{l s='Show on the views' mod='mgomnibus'}</label>
            <div class="multiselect-group vertical">
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_ICON_PRODUCT_DESKTOP" value="1" {if $settings.OMNIBUS_SHOW_ICON_PRODUCT_DESKTOP}checked{/if}> {l s='Product page – desktop' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_ICON_PRODUCT_MOBILE" value="1" {if $settings.OMNIBUS_SHOW_ICON_PRODUCT_MOBILE}checked{/if}> {l s='Product page – mobile' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_ICON_LISTING_DESKTOP" value="1" {if $settings.OMNIBUS_SHOW_ICON_LISTING_DESKTOP}checked{/if}> {l s='Listing – desktop' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_ICON_LISTING_MOBILE" value="1" {if $settings.OMNIBUS_SHOW_ICON_LISTING_MOBILE}checked{/if}> {l s='Listing – mobile' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_ICON_QUICKVIEW_DESKTOP" value="1" {if $settings.OMNIBUS_SHOW_ICON_QUICKVIEW_DESKTOP}checked{/if}> {l s='Quick view – desktop' mod='mgomnibus'}
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="OMNIBUS_SHOW_ICON_QUICKVIEW_MOBILE" value="1" {if $settings.OMNIBUS_SHOW_ICON_QUICKVIEW_MOBILE}checked{/if}> {l s='Quick view – mobile' mod='mgomnibus'}
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>{l s='Icon position' mod='mgomnibus'}</label>
            <select name="OMNIBUS_ICON_POSITION" class="form-control input-md">
                <option value="start" {if $settings.OMNIBUS_ICON_POSITION == 'start'}selected{/if}>{l s='Start (before text)' mod='mgomnibus'}</option>
                <option value="end" {if $settings.OMNIBUS_ICON_POSITION == 'end'}selected{/if}>{l s='End (after text)' mod='mgomnibus'}</option>
            </select>
        </div>

        <div class="form-group">
            <label>{l s='Icon type' mod='mgomnibus'}</label>
            <div class="radio-group">
                <label class="radio-inline">
                    <input type="radio" name="OMNIBUS_ICON_TYPE" value="file" {if $settings.OMNIBUS_ICON_TYPE == 'file'}checked{/if}> {l s='File' mod='mgomnibus'}
                </label>
                <label class="radio-inline">
                    <input type="radio" name="OMNIBUS_ICON_TYPE" value="awesome" {if $settings.OMNIBUS_ICON_TYPE == 'awesome'}checked{/if}> {l s='Google Material Icons' mod='mgomnibus'}
                </label>
            </div>
        </div>

        <div class="form-group icon-input-file" {if $settings.OMNIBUS_ICON_TYPE != 'file'}style="display:none;"{/if}>
            <label>{l s='Upload file' mod='mgomnibus'}</label>
            <input type="file" name="OMNIBUS_ICON_IMAGE" class="form-control">
            {if $settings.OMNIBUS_ICON_IMAGE}
                <div class="current-icon">
                    <img src="{$module_dir}views/img/{$settings.OMNIBUS_ICON_IMAGE}" alt="Icon" style="max-height: 30px; margin-top: 10px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="DELETE_OMNIBUS_ICON_IMAGE" value="1"> {l s='Delete current icon' mod='mgomnibus'}
                    </label>
                </div>
            {/if}
        </div>

        <div class="form-group icon-input-awesome" {if $settings.OMNIBUS_ICON_TYPE != 'awesome'}style="display:none;"{/if}>
            <label>{l s='Icon name' mod='mgomnibus'}</label>
            <input type="text" name="OMNIBUS_ICON_CLASS" class="form-control input-md" placeholder="e.g. info, warning, schedule" value="{$settings.OMNIBUS_ICON_CLASS|escape:'html':'UTF-8'}">
        </div>

        <div class="form-group">
            <label>{l s='Icon Tooltip' mod='mgomnibus'}</label>
            <div class="input-group">
                {foreach from=$languages item=lang}
                <textarea name="OMNIBUS_ICON_TOOLTIP_{$lang.id_lang}" class="form-control lang-input-wrapper" data-lang="{$lang.id_lang}" rows="2" style="display: {if $lang.id_lang == $default_lang}block{else}none{/if};">{$settings.OMNIBUS_ICON_TOOLTIP[$lang.id_lang]|escape:'html':'UTF-8'}</textarea>
                {/foreach}
                <div class="input-group-addon">
                    <span class="current-lang-name">{$languages[0].iso_code|upper}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('input[name="OMNIBUS_ICON_TYPE"]').change(function() {
            if ($(this).val() == 'file') {
                $('.icon-input-file').show();
                $('.icon-input-awesome').hide();
            } else {
                $('.icon-input-file').hide();
                $('.icon-input-awesome').show();
            }
        });
    });
</script>
