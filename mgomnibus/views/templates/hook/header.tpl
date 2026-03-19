<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
:root {
    /* Global Defaults (Fallback) */
    --mg-omnibus-font-family: inherit;

    {foreach from=['DESKTOP', 'MOBILE'] item=device}
        {foreach from=['PRODUCT', 'LISTING', 'QUICK'] item=view}
            {assign var="suffix" value="`$device`_`$view`"}
            {assign var="css_suffix" value="`$device|lower`-`$view|lower`"}
            
            /* {$device} - {$view} */
            --mg-omnibus-display-{$css_suffix}: {if $omnibus_settings["OMNIBUS_WIDTH_MODE_`$suffix`"] == 'full'}block{else}inline-block{/if};
            --mg-omnibus-width-{$css_suffix}: {if $omnibus_settings["OMNIBUS_WIDTH_MODE_`$suffix`"] == 'full'}100%{else}auto{/if};
            --mg-omnibus-bg-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_BG_COLOR_`$suffix`"]|default:'transparent'};
            --mg-omnibus-border-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_BORDER_COLOR_`$suffix`"]|default:'transparent'};
            --mg-omnibus-border-width-{$css_suffix}: {$omnibus_settings["OMNIBUS_BORDER_WIDTH_`$suffix`"]|default:0}px;
            
            --mg-omnibus-padding-top-{$css_suffix}: {$omnibus_settings["OMNIBUS_PADDING_TOP_`$suffix`"]|default:0}px;
            --mg-omnibus-padding-right-{$css_suffix}: {$omnibus_settings["OMNIBUS_PADDING_RIGHT_`$suffix`"]|default:0}px;
            --mg-omnibus-padding-bottom-{$css_suffix}: {$omnibus_settings["OMNIBUS_PADDING_BOTTOM_`$suffix`"]|default:0}px;
            --mg-omnibus-padding-left-{$css_suffix}: {$omnibus_settings["OMNIBUS_PADDING_LEFT_`$suffix`"]|default:0}px;

            --mg-omnibus-label-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_LABEL_COLOR_`$suffix`"]|default:'inherit'};
            --mg-omnibus-label-font-size-{$css_suffix}: {$omnibus_settings["OMNIBUS_LABEL_FONT_SIZE_`$suffix`"]|default:'inherit'}px;

            --mg-omnibus-price-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_PRICE_COLOR_`$suffix`"]|default:'inherit'};
            --mg-omnibus-price-font-size-{$css_suffix}: {$omnibus_settings["OMNIBUS_PRICE_FONT_SIZE_`$suffix`"]|default:'inherit'}px;
            --mg-omnibus-price-padding-top-{$css_suffix}: {$omnibus_settings["OMNIBUS_PRICE_PADDING_TOP_`$suffix`"]|default:0}px;
            --mg-omnibus-price-padding-right-{$css_suffix}: {$omnibus_settings["OMNIBUS_PRICE_PADDING_RIGHT_`$suffix`"]|default:0}px;
            --mg-omnibus-price-padding-bottom-{$css_suffix}: {$omnibus_settings["OMNIBUS_PRICE_PADDING_BOTTOM_`$suffix`"]|default:0}px;
            --mg-omnibus-price-padding-left-{$css_suffix}: {$omnibus_settings["OMNIBUS_PRICE_PADDING_LEFT_`$suffix`"]|default:0}px;

            --mg-omnibus-percent-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_COLOR_`$suffix`"]|default:'inherit'};
            --mg-omnibus-percent-font-size-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_FONT_SIZE_`$suffix`"]|default:'0.9em'}px;
            --mg-omnibus-percent-bg-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_BG_COLOR_`$suffix`"]|default:'transparent'};
            --mg-omnibus-percent-padding-top-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_PADDING_TOP_`$suffix`"]|default:0}px;
            --mg-omnibus-percent-padding-right-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_PADDING_RIGHT_`$suffix`"]|default:0}px;
            --mg-omnibus-percent-padding-bottom-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_PADDING_BOTTOM_`$suffix`"]|default:0}px;
            --mg-omnibus-percent-padding-left-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_PADDING_LEFT_`$suffix`"]|default:0}px;
            
            --mg-omnibus-percent-border-radius-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_BORDER_RADIUS_`$suffix`"]|default:0}px;
            --mg-omnibus-percent-border-width-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_BORDER_WIDTH_`$suffix`"]|default:0}px;
            --mg-omnibus-percent-border-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_PERCENT_BORDER_COLOR_`$suffix`"]|default:'transparent'};

            --mg-omnibus-icon-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_ICON_COLOR_`$suffix`"]|default:'inherit'};
            --mg-omnibus-icon-size-{$css_suffix}: {$omnibus_settings["OMNIBUS_ICON_SIZE_`$suffix`"]|default:'inherit'}px;
            
            --mg-omnibus-tooltip-font-size-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_FONT_SIZE_`$suffix`"]|default:'12'}px;
            --mg-omnibus-tooltip-width-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_WIDTH_`$suffix`"]|default:'200'}px;
            --mg-omnibus-tooltip-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_COLOR_`$suffix`"]|default:'#ffffff'};
            --mg-omnibus-tooltip-bg-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_BG_COLOR_`$suffix`"]|default:'#333333'};
            --mg-omnibus-tooltip-border-width-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_BORDER_WIDTH_`$suffix`"]|default:1}px;
            --mg-omnibus-tooltip-border-color-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_BORDER_COLOR_`$suffix`"]|default:'#666666'};
            --mg-omnibus-tooltip-padding-top-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_PADDING_TOP_`$suffix`"]|default:6}px;
            --mg-omnibus-tooltip-padding-right-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_PADDING_RIGHT_`$suffix`"]|default:10}px;
            --mg-omnibus-tooltip-padding-bottom-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_PADDING_BOTTOM_`$suffix`"]|default:6}px;
            --mg-omnibus-tooltip-padding-left-{$css_suffix}: {$omnibus_settings["OMNIBUS_TOOLTIP_PADDING_LEFT_`$suffix`"]|default:10}px;
        {/foreach}
    {/foreach}
}
</style>

<script>
    var omnibus_settings = {json_encode($omnibus_settings)};
</script>

{if isset($omnibus_settings.OMNIBUS_CUSTOM_CSS) && $omnibus_settings.OMNIBUS_CUSTOM_CSS}
<style>
    {$omnibus_settings.OMNIBUS_CUSTOM_CSS nofilter}
</style>
{/if}
