{**
 * ClearDeal - Frontend Price Display Template
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License 3.0 (AFL-3.0)
 *}

{if isset($cleardeal_data) && $cleardeal_data}
    <div class="cleardeal-wrapper cleardeal-hook-{$cleardeal_data.hook_type} cleardeal-icon-{$cleardeal_icon_position}{if $cleardeal_show_chart} cleardeal-has-chart cleardeal-chart-trigger-{$cleardeal_chart_trigger}{/if}"
         {if $cleardeal_show_chart}data-product-id="{$cleardeal_data.id_product}" data-attribute-id="{$cleardeal_data.id_product_attribute|default:0}"{/if}>
        <div class="cleardeal-content">
            {* Icon at start *}
            {if $cleardeal_show_icon && $cleardeal_icon_position == 'start'}
                <span class="cleardeal-icon{if $cleardeal_show_chart && $cleardeal_chart_trigger == 'icon'} cleardeal-chart-clickable{/if}">
                    {if $cleardeal_icon_type == 'file' && $cleardeal_icon_image}
                        <img src="{$cleardeal_module_dir}views/img/{$cleardeal_icon_image}" alt="" class="cleardeal-icon-img">
                    {else}
                        <i class="bi bi-{$cleardeal_icon_class}"></i>
                    {/if}
                    {if $cleardeal_tooltip}
                        <span class="cleardeal-tooltip">{$cleardeal_tooltip}</span>
                    {/if}
                </span>
            {/if}

            {* Label *}
            <span class="cleardeal-label">{$cleardeal_data.label}</span>

            {* Price *}
            <span class="cleardeal-price{if $cleardeal_show_chart && $cleardeal_chart_trigger == 'price'} cleardeal-chart-clickable{/if}">{$cleardeal_data.lowest_price_formatted}</span>

            {* Percentage *}
            {if $cleardeal_data.percentage !== null}
                <span class="cleardeal-percent {if $cleardeal_data.is_negative}cleardeal-percent-negative{else}cleardeal-percent-positive{/if}">
                    {if $cleardeal_data.percentage > 0}+{/if}{$cleardeal_data.percentage}%
                </span>
            {/if}

            {* Icon at end *}
            {if $cleardeal_show_icon && $cleardeal_icon_position == 'end'}
                <span class="cleardeal-icon{if $cleardeal_show_chart && $cleardeal_chart_trigger == 'icon'} cleardeal-chart-clickable{/if}">
                    {if $cleardeal_icon_type == 'file' && $cleardeal_icon_image}
                        <img src="{$cleardeal_module_dir}views/img/{$cleardeal_icon_image}" alt="" class="cleardeal-icon-img">
                    {else}
                        <i class="bi bi-{$cleardeal_icon_class}"></i>
                    {/if}
                    {if $cleardeal_tooltip}
                        <span class="cleardeal-tooltip">{$cleardeal_tooltip}</span>
                    {/if}
                </span>
            {/if}
        </div>
    </div>
{/if}
