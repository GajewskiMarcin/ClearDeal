{**
 * ClearDeal - Appearance Tab Template
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License 3.0 (AFL-3.0)
 *}

<form action="{$admin_link}&tab=appearance" method="post" class="cleardeal-form">

    {* =========================================================================
       SECTION 1: Price Element - Style & Colors
       ========================================================================= *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">palette</i>
            <h3>{l s='Price Element Style' mod='cleardeal'}</h3>
        </div>
        <div class="cleardeal-card-body">
            <p class="cleardeal-info cleardeal-info-sm">
                <i class="material-icons">info</i>
                {l s='Choose a base style for the lowest price display. Each preset defines structure and default colors.' mod='cleardeal'}
            </p>

            {* Style Presets *}
            <div class="cleardeal-presets">
                <label class="cleardeal-preset {if $settings.CLEARDEAL_STYLE_PRESET == 'default' || !$settings.CLEARDEAL_STYLE_PRESET}active{/if}" data-preset="default">
                    <input type="radio" name="CLEARDEAL_STYLE_PRESET" value="default" {if $settings.CLEARDEAL_STYLE_PRESET == 'default' || !$settings.CLEARDEAL_STYLE_PRESET}checked{/if}>
                    <div class="cleardeal-preset-preview cleardeal-preset-default">
                        <span class="cleardeal-preset-label">{l s='Lowest price:' mod='cleardeal'}</span>
                        <span class="cleardeal-preset-price">49,99 zł</span>
                        <span class="cleardeal-preset-percent">-15%</span>
                    </div>
                    <span class="cleardeal-preset-name">{l s='Default' mod='cleardeal'}</span>
                    <span class="cleardeal-preset-desc">{l s='Background + border' mod='cleardeal'}</span>
                </label>

                <label class="cleardeal-preset {if $settings.CLEARDEAL_STYLE_PRESET == 'minimal'}active{/if}" data-preset="minimal">
                    <input type="radio" name="CLEARDEAL_STYLE_PRESET" value="minimal" {if $settings.CLEARDEAL_STYLE_PRESET == 'minimal'}checked{/if}>
                    <div class="cleardeal-preset-preview cleardeal-preset-minimal">
                        <span class="cleardeal-preset-label">{l s='Lowest price:' mod='cleardeal'}</span>
                        <span class="cleardeal-preset-price">49,99 zł</span>
                        <span class="cleardeal-preset-percent">-15%</span>
                    </div>
                    <span class="cleardeal-preset-name">{l s='Minimal' mod='cleardeal'}</span>
                    <span class="cleardeal-preset-desc">{l s='No background' mod='cleardeal'}</span>
                </label>

                <label class="cleardeal-preset {if $settings.CLEARDEAL_STYLE_PRESET == 'bold'}active{/if}" data-preset="bold">
                    <input type="radio" name="CLEARDEAL_STYLE_PRESET" value="bold" {if $settings.CLEARDEAL_STYLE_PRESET == 'bold'}checked{/if}>
                    <div class="cleardeal-preset-preview cleardeal-preset-bold">
                        <span class="cleardeal-preset-label">{l s='Lowest price:' mod='cleardeal'}</span>
                        <span class="cleardeal-preset-price">49,99 zł</span>
                        <span class="cleardeal-preset-percent">-15%</span>
                    </div>
                    <span class="cleardeal-preset-name">{l s='Bold' mod='cleardeal'}</span>
                    <span class="cleardeal-preset-desc">{l s='Gradient background' mod='cleardeal'}</span>
                </label>
            </div>

            {* Customize Colors Toggle *}
            <div class="cleardeal-field cleardeal-mt-20">
                <label class="cleardeal-checkbox">
                    <input type="checkbox" name="CLEARDEAL_USE_CUSTOM_COLORS" value="1" {if $settings.CLEARDEAL_USE_CUSTOM_COLORS}checked{/if} id="use_custom_colors">
                    <span>{l s='Customize colors for selected preset' mod='cleardeal'}</span>
                </label>
            </div>

            <div id="custom_colors_section" {if !$settings.CLEARDEAL_USE_CUSTOM_COLORS}style="display:none;"{/if}>
                {* Colors for Default preset *}
                <div class="cleardeal-color-group" data-for-preset="default" {if $settings.CLEARDEAL_STYLE_PRESET != 'default' && $settings.CLEARDEAL_STYLE_PRESET}style="display:none;"{/if}>
                    <h4 class="cleardeal-color-group-title">{l s='Default preset colors' mod='cleardeal'}</h4>
                    <div class="cleardeal-row">
                        <div class="cleardeal-field">
                            <label>{l s='Background color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_BACKGROUND" value="{$settings.CLEARDEAL_COLOR_BACKGROUND|default:'#f6f9fc'}" class="cleardeal-color-picker" id="color_background">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_BACKGROUND|default:'#f6f9fc'}" class="cleardeal-input cleardeal-color-text" id="color_background_text" data-color="color_background">
                            </div>
                        </div>
                        <div class="cleardeal-field">
                            <label>{l s='Border color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_BORDER" value="{$settings.CLEARDEAL_COLOR_BORDER|default:'#e3e8ee'}" class="cleardeal-color-picker" id="color_border">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_BORDER|default:'#e3e8ee'}" class="cleardeal-input cleardeal-color-text" id="color_border_text" data-color="color_border">
                            </div>
                        </div>
                    </div>
                    <div class="cleardeal-row">
                        <div class="cleardeal-field">
                            <label>{l s='Icon color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_PRIMARY" value="{$settings.CLEARDEAL_COLOR_PRIMARY|default:'#635bff'}" class="cleardeal-color-picker" id="color_primary">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_PRIMARY|default:'#635bff'}" class="cleardeal-input cleardeal-color-text" id="color_primary_text" data-color="color_primary">
                            </div>
                        </div>
                        <div class="cleardeal-field">
                            <label>{l s='Badge color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_BADGE" value="{$settings.CLEARDEAL_COLOR_BADGE|default:'#fce4ec'}" class="cleardeal-color-picker" id="color_badge">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_BADGE|default:'#fce4ec'}" class="cleardeal-input cleardeal-color-text" id="color_badge_text" data-color="color_badge">
                            </div>
                        </div>
                    </div>
                    <div class="cleardeal-field">
                        <label class="cleardeal-checkbox">
                            <input type="checkbox" name="CLEARDEAL_USE_GRADIENT" value="1" {if $settings.CLEARDEAL_USE_GRADIENT}checked{/if} id="use_gradient_default">
                            <span>{l s='Use gradient background' mod='cleardeal'}</span>
                        </label>
                    </div>
                    <div class="cleardeal-row gradient-options-default" {if !$settings.CLEARDEAL_USE_GRADIENT}style="display:none;"{/if}>
                        <div class="cleardeal-field">
                            <label>{l s='Gradient end color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_GRADIENT_END" value="{$settings.CLEARDEAL_COLOR_GRADIENT_END|default:'#e0e5eb'}" class="cleardeal-color-picker" id="color_gradient_end">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_GRADIENT_END|default:'#e0e5eb'}" class="cleardeal-input cleardeal-color-text" id="color_gradient_end_text" data-color="color_gradient_end">
                            </div>
                        </div>
                    </div>
                </div>

                {* Colors for Minimal preset *}
                <div class="cleardeal-color-group" data-for-preset="minimal" {if $settings.CLEARDEAL_STYLE_PRESET != 'minimal'}style="display:none;"{/if}>
                    <h4 class="cleardeal-color-group-title">{l s='Minimal preset colors' mod='cleardeal'}</h4>
                    <div class="cleardeal-row">
                        <div class="cleardeal-field">
                            <label>{l s='Icon color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_MINIMAL_ICON" value="{$settings.CLEARDEAL_COLOR_MINIMAL_ICON|default:'#9ca3af'}" class="cleardeal-color-picker" id="color_minimal_icon">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_MINIMAL_ICON|default:'#9ca3af'}" class="cleardeal-input cleardeal-color-text" id="color_minimal_icon_text" data-color="color_minimal_icon">
                            </div>
                        </div>
                        <div class="cleardeal-field">
                            <label>{l s='Label color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_MINIMAL_LABEL" value="{$settings.CLEARDEAL_COLOR_MINIMAL_LABEL|default:'#9ca3af'}" class="cleardeal-color-picker" id="color_minimal_label">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_MINIMAL_LABEL|default:'#9ca3af'}" class="cleardeal-input cleardeal-color-text" id="color_minimal_label_text" data-color="color_minimal_label">
                            </div>
                        </div>
                    </div>
                    <div class="cleardeal-row">
                        <div class="cleardeal-field">
                            <label>{l s='Price color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_MINIMAL_PRICE" value="{$settings.CLEARDEAL_COLOR_MINIMAL_PRICE|default:'#374151'}" class="cleardeal-color-picker" id="color_minimal_price">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_MINIMAL_PRICE|default:'#374151'}" class="cleardeal-input cleardeal-color-text" id="color_minimal_price_text" data-color="color_minimal_price">
                            </div>
                        </div>
                        <div class="cleardeal-field">
                            <label>{l s='Badge text color' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_MINIMAL_BADGE" value="{$settings.CLEARDEAL_COLOR_MINIMAL_BADGE|default:'#c41535'}" class="cleardeal-color-picker" id="color_minimal_badge">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_MINIMAL_BADGE|default:'#c41535'}" class="cleardeal-input cleardeal-color-text" id="color_minimal_badge_text" data-color="color_minimal_badge">
                            </div>
                        </div>
                    </div>
                </div>

                {* Colors for Bold preset *}
                <div class="cleardeal-color-group" data-for-preset="bold" {if $settings.CLEARDEAL_STYLE_PRESET != 'bold'}style="display:none;"{/if}>
                    <h4 class="cleardeal-color-group-title">{l s='Bold preset colors' mod='cleardeal'}</h4>
                    <div class="cleardeal-row">
                        <div class="cleardeal-field">
                            <label>{l s='Gradient start' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_BOLD_START" value="{$settings.CLEARDEAL_COLOR_BOLD_START|default:'#6366f1'}" class="cleardeal-color-picker" id="color_bold_start">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_BOLD_START|default:'#6366f1'}" class="cleardeal-input cleardeal-color-text" id="color_bold_start_text" data-color="color_bold_start">
                            </div>
                        </div>
                        <div class="cleardeal-field">
                            <label>{l s='Gradient end' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_BOLD_END" value="{$settings.CLEARDEAL_COLOR_BOLD_END|default:'#a855f7'}" class="cleardeal-color-picker" id="color_bold_end">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_BOLD_END|default:'#a855f7'}" class="cleardeal-input cleardeal-color-text" id="color_bold_end_text" data-color="color_bold_end">
                            </div>
                        </div>
                    </div>
                    <div class="cleardeal-row">
                        <div class="cleardeal-field">
                            <label>{l s='Badge background' mod='cleardeal'}</label>
                            <div class="cleardeal-color-input">
                                <input type="color" name="CLEARDEAL_COLOR_BOLD_BADGE" value="{$settings.CLEARDEAL_COLOR_BOLD_BADGE|default:'#ffffff'}" class="cleardeal-color-picker" id="color_bold_badge">
                                <input type="text" value="{$settings.CLEARDEAL_COLOR_BOLD_BADGE|default:'#ffffff'}" class="cleardeal-input cleardeal-color-text" id="color_bold_badge_text" data-color="color_bold_badge">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {* Price Element Preview *}
            <div class="cleardeal-preview-section">
                <span class="cleardeal-preview-label">{l s='Preview' mod='cleardeal'}</span>
                <div class="cleardeal-preview-box-content">
                    <div class="cleardeal-preview-content cleardeal-preview-icon-{$settings.CLEARDEAL_ICON_POSITION|default:'start'}" id="preview_content" data-icon-position="{$settings.CLEARDEAL_ICON_POSITION|default:'start'}">
                        <span class="cleardeal-preview-icon">
                            <i class="bi bi-{$settings.CLEARDEAL_ICON_CLASS|default:'info-circle'}"></i>
                        </span>
                        <span class="cleardeal-preview-label-text">{l s='Lowest price in last 30 days:' mod='cleardeal'}</span>
                        <span class="cleardeal-preview-price">49,99 zł</span>
                        <span class="cleardeal-preview-percent">-15%</span>
                    </div>
                </div>
                {if isset($preview_product_url) && $preview_product_url}
                    <a href="{$preview_product_url}" target="_blank" class="cleardeal-btn cleardeal-btn-secondary cleardeal-btn-sm cleardeal-mt-10">
                        <i class="material-icons">open_in_new</i>
                        {l s='Preview on Product' mod='cleardeal'}
                    </a>
                {/if}
            </div>
        </div>
    </div>

    {* =========================================================================
       SECTION 2: Chart & Modal Settings
       ========================================================================= *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">show_chart</i>
            <h3>{l s='Chart & Modal Style' mod='cleardeal'}</h3>
        </div>
        <div class="cleardeal-card-body">
            <p class="cleardeal-info cleardeal-info-sm">
                <i class="material-icons">info</i>
                {l s='Customize the price history chart popup appearance.' mod='cleardeal'}
            </p>

            {* Modal Header Color *}
            <div class="cleardeal-row">
                <div class="cleardeal-field">
                    <label>{l s='Modal header color' mod='cleardeal'}</label>
                    <div class="cleardeal-color-input">
                        <input type="color" name="CLEARDEAL_MODAL_HEADER_COLOR" value="{$settings.CLEARDEAL_MODAL_HEADER_COLOR|default:'#635bff'}" class="cleardeal-color-picker" id="color_modal_header">
                        <input type="text" value="{$settings.CLEARDEAL_MODAL_HEADER_COLOR|default:'#635bff'}" class="cleardeal-input cleardeal-color-text" id="color_modal_header_text" data-color="color_modal_header">
                    </div>
                    <span class="cleardeal-hint">{l s='Background color of the popup header (gradient is auto-generated)' mod='cleardeal'}</span>
                </div>
            </div>

            {* Chart Colors *}
            <div class="cleardeal-row cleardeal-mt-20">
                <div class="cleardeal-field">
                    <label>{l s='Chart line color' mod='cleardeal'}</label>
                    <div class="cleardeal-color-input">
                        <input type="color" name="CLEARDEAL_CHART_LINE_COLOR" value="{$settings.CLEARDEAL_CHART_LINE_COLOR|default:'#635bff'}" class="cleardeal-color-picker" id="color_chart_line">
                        <input type="text" value="{$settings.CLEARDEAL_CHART_LINE_COLOR|default:'#635bff'}" class="cleardeal-input cleardeal-color-text" id="color_chart_line_text" data-color="color_chart_line">
                    </div>
                    <span class="cleardeal-hint">{l s='Color of the price history line on the chart' mod='cleardeal'}</span>
                </div>
                <div class="cleardeal-field">
                    <label>{l s='Chart fill color' mod='cleardeal'}</label>
                    <div class="cleardeal-color-input">
                        <input type="color" name="CLEARDEAL_CHART_FILL_COLOR" value="{$settings.CLEARDEAL_CHART_FILL_COLOR|default:'#635bff'}" class="cleardeal-color-picker" id="color_chart_fill">
                        <input type="text" value="{$settings.CLEARDEAL_CHART_FILL_COLOR|default:'#635bff'}" class="cleardeal-input cleardeal-color-text" id="color_chart_fill_text" data-color="color_chart_fill">
                    </div>
                    <span class="cleardeal-hint">{l s='Gradient fill under the chart line (semi-transparent)' mod='cleardeal'}</span>
                </div>
            </div>

            {* Chart/Modal Preview *}
            <div class="cleardeal-preview-section">
                <span class="cleardeal-preview-label">{l s='Preview' mod='cleardeal'}</span>
                <div class="cleardeal-preview-box-content cleardeal-preview-box-modal">
                    <div class="cleardeal-preview-modal" id="preview_modal_header">
                        <div class="cleardeal-preview-modal-header-inner">
                            <div class="cleardeal-preview-modal-title-group">
                                <span class="cleardeal-preview-modal-icon">
                                    <i class="material-icons">show_chart</i>
                                </span>
                                <span class="cleardeal-preview-modal-title">{l s='Price History' mod='cleardeal'}</span>
                            </div>
                            <span class="cleardeal-preview-modal-close">
                                <i class="material-icons">close</i>
                            </span>
                        </div>
                        <div class="cleardeal-preview-modal-body">
                            <div class="cleardeal-preview-chart" id="preview_chart">
                                <svg viewBox="0 0 200 60" class="cleardeal-preview-chart-svg">
                                    <defs>
                                        <linearGradient id="chartGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" id="chartGradientStart" style="stop-color:#635bff;stop-opacity:0.3"/>
                                            <stop offset="100%" id="chartGradientEnd" style="stop-color:#635bff;stop-opacity:0"/>
                                        </linearGradient>
                                    </defs>
                                    <path id="chartFill" d="M0,50 Q30,45 50,40 T100,35 T150,25 T200,30 L200,60 L0,60 Z" fill="url(#chartGradient)"/>
                                    <path id="chartLine" d="M0,50 Q30,45 50,40 T100,35 T150,25 T200,30" fill="none" stroke="#635bff" stroke-width="2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {* =========================================================================
       SECTION 3: Tooltip Style
       ========================================================================= *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">chat_bubble</i>
            <h3>{l s='Tooltip Style' mod='cleardeal'}</h3>
        </div>
        <div class="cleardeal-card-body">
            <p class="cleardeal-info cleardeal-info-sm">
                <i class="material-icons">info</i>
                {l s='Customize the tooltip that appears when hovering over the info icon. Text color is calculated automatically for best contrast.' mod='cleardeal'}
            </p>
            <div class="cleardeal-row">
                <div class="cleardeal-field">
                    <label>{l s='Tooltip background' mod='cleardeal'}</label>
                    <div class="cleardeal-color-input">
                        <input type="color" name="CLEARDEAL_TOOLTIP_BG" value="{$settings.CLEARDEAL_TOOLTIP_BG|default:'#1a1f36'}" class="cleardeal-color-picker" id="color_tooltip_bg">
                        <input type="text" value="{$settings.CLEARDEAL_TOOLTIP_BG|default:'#1a1f36'}" class="cleardeal-input cleardeal-color-text" id="color_tooltip_bg_text" data-color="color_tooltip_bg">
                    </div>
                </div>
            </div>

            {* Tooltip Preview *}
            <div class="cleardeal-preview-section">
                <span class="cleardeal-preview-label">{l s='Preview' mod='cleardeal'}</span>
                <div class="cleardeal-preview-box-content">
                    <div class="cleardeal-tooltip-preview" id="tooltip_preview">
                        <span class="cleardeal-tooltip-preview-text">{l s='This is the lowest price from the last 30 days before the current promotion.' mod='cleardeal'}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {* =========================================================================
       SECTION 4: Custom CSS
       ========================================================================= *}
    <div class="cleardeal-card">
        <div class="cleardeal-card-header">
            <i class="material-icons">code</i>
            <h3>{l s='Custom CSS' mod='cleardeal'}</h3>
        </div>
        <div class="cleardeal-card-body">
            <div class="cleardeal-field">
                <label>{l s='Additional Custom CSS' mod='cleardeal'}</label>
                <textarea name="CLEARDEAL_CUSTOM_CSS" class="cleardeal-textarea cleardeal-css-custom" id="custom_css" rows="6" placeholder="{l s='Add your custom CSS rules here...' mod='cleardeal'}">{$settings.CLEARDEAL_CUSTOM_CSS|default:''}</textarea>
                <span class="cleardeal-hint">{l s='Add your own CSS rules. Use selectors like .cleardeal-content, .cleardeal-modal-header, etc.' mod='cleardeal'}</span>
            </div>
        </div>
    </div>

    {* Footer Actions *}
    <div class="cleardeal-footer">
        <button type="submit" name="submitClearDealAppearance" class="cleardeal-btn cleardeal-btn-primary">
            <i class="material-icons">save</i>
            {l s='Save Appearance' mod='cleardeal'}
        </button>
    </div>
</form>
