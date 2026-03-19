{if isset($omnibus_data) && $omnibus_data}
    <div class="mg-omnibus-wrapper mg-omnibus-hook-{$omnibus_data.hook_type} mg-omnibus-icon-{$omnibus_settings.OMNIBUS_ICON_POSITION|default:'start'}">
        {assign var="hook_upper" value=$omnibus_data.hook_type|upper}
        {assign var="show_percent_desktop" value=$omnibus_settings["OMNIBUS_SHOW_PERCENT_{$hook_upper}_DESKTOP"]}
        {assign var="show_percent_mobile" value=$omnibus_settings["OMNIBUS_SHOW_PERCENT_{$hook_upper}_MOBILE"]}
        {assign var="show_icon_desktop" value=$omnibus_settings["OMNIBUS_SHOW_ICON_{$hook_upper}_DESKTOP"]}
        {assign var="show_icon_mobile" value=$omnibus_settings["OMNIBUS_SHOW_ICON_{$hook_upper}_MOBILE"]}

        <div class="mg-omnibus-price">
            {if isset($omnibus_settings.OMNIBUS_CHART_TRIGGER) && $omnibus_settings.OMNIBUS_CHART_TRIGGER == 'button'}
                <a href="#" class="mg-omnibus-chart-trigger">{$omnibus_settings.OMNIBUS_CHART_BUTTON_LABEL|default:'View price history'}</a>
            {else}
                <span class="mg-omnibus-icon-wrapper {if !$show_icon_desktop}mg-desktop-icon-hide{/if} {if !$show_icon_mobile}mg-mobile-icon-hide{/if}">
                {if isset($omnibus_settings.OMNIBUS_ICON_TYPE) && $omnibus_settings.OMNIBUS_ICON_TYPE == 'file' && isset($omnibus_settings.OMNIBUS_ICON_IMAGE) && $omnibus_settings.OMNIBUS_ICON_IMAGE}
                    <img src="{$urls.base_url}modules/mgomnibus/views/img/{$omnibus_settings.OMNIBUS_ICON_IMAGE}" alt="" />
                {else}
                    <i class="material-icons">{$omnibus_settings.OMNIBUS_ICON_CLASS|default:'info'}</i>
                {/if}
                {if isset($omnibus_tooltip) && $omnibus_tooltip}
                    <span class="mg-omnibus-tooltip">{$omnibus_tooltip}</span>
                {/if}
                </span>
            {/if}
            
            <span class="mg-omnibus-label">{$omnibus_data.label}</span>
            
            <span class="mg-omnibus-amount">{$omnibus_data.lowest_price_formatted}</span>

            {if isset($omnibus_data.percentage) && $omnibus_data.percentage}
                <span class="mg-omnibus-percent {if !$show_percent_desktop}mg-desktop-percent-hide{/if} {if !$show_percent_mobile}mg-mobile-percent-hide{/if}">
                    {$omnibus_data.percentage}%
                </span>
            {/if}
        </div>
    </div>
    <!-- DEBUG OMNIBUS:
    {$omnibus_data.debug|@print_r}
    -->
{/if}
