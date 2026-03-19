<!-- SECTION: Basic Settings -->
<div class="card">
    <div class="card-header d-flex-between">
        <h3>{l s='Basic settings' mod='mgomnibus'}</h3>
        <div class="header-control">
            <select id="matrix-view-selector-basic" class="form-control input-md matrix-view-selector">
                <option value="PRODUCT">{l s='Product page' mod='mgomnibus'}</option>
                <option value="LISTING">{l s='Listing' mod='mgomnibus'}</option>
                <option value="QUICK">{l s='Quick view' mod='mgomnibus'}</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <!-- View Switcher -->
        <div class="view-switcher">
            <a href="#" class="view-tab active" data-device="DESKTOP">{l s='Desktop' mod='mgomnibus'}</a>
            <a href="#" class="view-tab" data-device="MOBILE">{l s='Mobile' mod='mgomnibus'}</a>
        </div>

        {foreach from=['DESKTOP', 'MOBILE'] item=device}
        {foreach from=['PRODUCT', 'LISTING', 'QUICK'] item=view}
        <div class="matrix-group" data-device="{$device}" data-view="{$view}" style="display: none;">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Module width' mod='mgomnibus'}</label>
                        <select name="OMNIBUS_WIDTH_MODE_{$device}_{$view}" class="form-control input-md">
                            <option value="auto" {if $settings["OMNIBUS_WIDTH_MODE_{$device}_{$view}"] == 'auto'}selected{/if}>{l s='Auto' mod='mgomnibus'}</option>
                            <option value="full" {if $settings["OMNIBUS_WIDTH_MODE_{$device}_{$view}"] == 'full'}selected{/if}>{l s='Full (100%)' mod='mgomnibus'}</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Element spacing' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_ELEMENT_SPACING_{$device}_{$view}" class="form-control input-xs" value="{$settings["OMNIBUS_ELEMENT_SPACING_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                        <small class="form-text">{l s='Spacing between label, price, percent change, and icon' mod='mgomnibus'}</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Background color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_BG_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_BG_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_BG_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Border color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_BORDER_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_BORDER_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_BORDER_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Border thickness' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_BORDER_WIDTH_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_BORDER_WIDTH_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{l s='Padding' mod='mgomnibus'}</label>
                <div class="padding-inputs">
                    <div class="padding-item">
                        <small>{l s='Top' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_PADDING_TOP_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PADDING_TOP_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Right' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_PADDING_RIGHT_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PADDING_RIGHT_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Bottom' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_PADDING_BOTTOM_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PADDING_BOTTOM_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Left' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_PADDING_LEFT_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PADDING_LEFT_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                </div>
            </div>
        </div>
        {/foreach}
        {/foreach}
    </div>
</div>

<!-- SECTION: Typography & colors -->
<div class="card">
    <div class="card-header d-flex-between">
        <h3>{l s='Typography & colors' mod='mgomnibus'}</h3>
        <div class="header-control">
            <select id="matrix-view-selector-typography" class="form-control input-md matrix-view-selector">
                <option value="PRODUCT">{l s='Product page' mod='mgomnibus'}</option>
                <option value="LISTING">{l s='Listing' mod='mgomnibus'}</option>
                <option value="QUICK">{l s='Quick view' mod='mgomnibus'}</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <!-- View Switcher -->
        <div class="view-switcher">
            <a href="#" class="view-tab active" data-device="DESKTOP">{l s='Desktop' mod='mgomnibus'}</a>
            <a href="#" class="view-tab" data-device="MOBILE">{l s='Mobile' mod='mgomnibus'}</a>
        </div>

        {foreach from=['DESKTOP', 'MOBILE'] item=device}
        {foreach from=['PRODUCT', 'LISTING', 'QUICK'] item=view}
        <div class="matrix-group" data-device="{$device}" data-view="{$view}" style="display: none;">
            
            <!-- Sub-section: Label -->
            <h4 class="subsection-title">{l s='Label' mod='mgomnibus'}</h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Font size' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_LABEL_FONT_SIZE_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_LABEL_FONT_SIZE_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Font color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_LABEL_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_LABEL_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_LABEL_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="subsection-divider">

            <!-- Sub-section: Price -->
            <h4 class="subsection-title">{l s='Price' mod='mgomnibus'}</h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Font size' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_PRICE_FONT_SIZE_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PRICE_FONT_SIZE_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Font color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_PRICE_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_PRICE_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_PRICE_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="subsection-divider">

            <!-- Sub-section: Percent change -->
            <h4 class="subsection-title">{l s='Percent change' mod='mgomnibus'}</h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Font size' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_PERCENT_FONT_SIZE_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PERCENT_FONT_SIZE_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Font color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_PERCENT_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_PERCENT_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_PERCENT_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Background color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_PERCENT_BG_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_PERCENT_BG_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_PERCENT_BG_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Border radius' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_PERCENT_BORDER_RADIUS_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PERCENT_BORDER_RADIUS_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Border size' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_PERCENT_BORDER_WIDTH_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PERCENT_BORDER_WIDTH_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Border color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_PERCENT_BORDER_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_PERCENT_BORDER_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_PERCENT_BORDER_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>{l s='Padding' mod='mgomnibus'}</label>
                <div class="padding-inputs">
                    <div class="padding-item">
                        <small>{l s='Top' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_PERCENT_PADDING_TOP_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PERCENT_PADDING_TOP_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Right' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_PERCENT_PADDING_RIGHT_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PERCENT_PADDING_RIGHT_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Bottom' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_PERCENT_PADDING_BOTTOM_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PERCENT_PADDING_BOTTOM_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Left' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_PERCENT_PADDING_LEFT_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_PERCENT_PADDING_LEFT_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                </div>
            </div>

            <hr class="subsection-divider">

            <!-- Sub-section: Icon -->
            <h4 class="subsection-title">{l s='Icon' mod='mgomnibus'}</h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Icon size' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_ICON_SIZE_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_ICON_SIZE_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Icon color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_ICON_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_ICON_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_ICON_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="subsection-divider">

            <!-- Sub-section: Icon Tooltip -->
            <h4 class="subsection-title">{l s='Icon Tooltip' mod='mgomnibus'}</h4>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Font size' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_TOOLTIP_FONT_SIZE_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_TOOLTIP_FONT_SIZE_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Font color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_TOOLTIP_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_TOOLTIP_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_TOOLTIP_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Background color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_TOOLTIP_BG_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_TOOLTIP_BG_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_TOOLTIP_BG_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Border size' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_TOOLTIP_BORDER_WIDTH_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_TOOLTIP_BORDER_WIDTH_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Border color' mod='mgomnibus'}</label>
                        <div class="input-group color-picker-group">
                            <input type="text" name="OMNIBUS_TOOLTIP_BORDER_COLOR_{$device}_{$view}" class="form-control color-text" value="{$settings["OMNIBUS_TOOLTIP_BORDER_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon p-0">
                                <input type="color" class="color-picker" value="{$settings["OMNIBUS_TOOLTIP_BORDER_COLOR_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>{l s='Width' mod='mgomnibus'}</label>
                        <div class="input-group">
                            <input type="number" name="OMNIBUS_TOOLTIP_WIDTH_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_TOOLTIP_WIDTH_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">px</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>{l s='Padding' mod='mgomnibus'}</label>
                <div class="padding-inputs">
                    <div class="padding-item">
                        <small>{l s='Top' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_TOOLTIP_PADDING_TOP_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_TOOLTIP_PADDING_TOP_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Right' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_TOOLTIP_PADDING_RIGHT_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_TOOLTIP_PADDING_RIGHT_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Bottom' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_TOOLTIP_PADDING_BOTTOM_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_TOOLTIP_PADDING_BOTTOM_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                    <div class="padding-item">
                        <small>{l s='Left' mod='mgomnibus'}</small>
                        <input type="number" name="OMNIBUS_TOOLTIP_PADDING_LEFT_{$device}_{$view}" class="form-control" value="{$settings["OMNIBUS_TOOLTIP_PADDING_LEFT_{$device}_{$view}"]|escape:'html':'UTF-8'}">
                    </div>
                </div>
            </div>

        </div>
        {/foreach}
        {/foreach}
    </div>
</div>

<!-- SECTION: Custom CSS -->
<div class="card">
    <div class="card-header">
        <h3>{l s='Custom CSS' mod='mgomnibus'}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>{l s='Custom CSS Code' mod='mgomnibus'}</label>
            <textarea name="OMNIBUS_CUSTOM_CSS" class="form-control" rows="10" placeholder="{l s='/* Enter your custom CSS here */' mod='mgomnibus'}">{$settings.OMNIBUS_CUSTOM_CSS|escape:'html':'UTF-8'}</textarea>
        </div>
    </div>
</div>
