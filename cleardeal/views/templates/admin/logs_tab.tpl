{**
 * ClearDeal - Logs Tab Template
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *}

{* Build filter query string for pagination *}
{assign var="filter_params" value=""}
{if $logs_filters.product_search}
    {assign var="filter_params" value="{$filter_params}&filter_product={$logs_filters.product_search|escape:'url'}"}
{/if}
{if $logs_filters.date_from}
    {assign var="filter_params" value="{$filter_params}&filter_date_from={$logs_filters.date_from|escape:'url'}"}
{/if}
{if $logs_filters.date_to}
    {assign var="filter_params" value="{$filter_params}&filter_date_to={$logs_filters.date_to|escape:'url'}"}
{/if}

<div class="cleardeal-logs">
    {* Statistics Dashboard *}
    {if isset($stats) && $stats}
        <div class="cleardeal-stats-grid">
            <div class="cleardeal-stat-card">
                <div class="cleardeal-stat-icon cleardeal-stat-icon-blue">
                    <i class="material-icons">storage</i>
                </div>
                <div class="cleardeal-stat-content">
                    <span class="cleardeal-stat-value">{$stats.total_logs|number_format:0:',':' '}</span>
                    <span class="cleardeal-stat-label">{l s='Total Logs' mod='cleardeal'}</span>
                </div>
            </div>

            <div class="cleardeal-stat-card">
                <div class="cleardeal-stat-icon cleardeal-stat-icon-purple">
                    <i class="material-icons">inventory_2</i>
                </div>
                <div class="cleardeal-stat-content">
                    <span class="cleardeal-stat-value">{$stats.unique_products|number_format:0:',':' '}</span>
                    <span class="cleardeal-stat-label">{l s='Products Tracked' mod='cleardeal'}</span>
                </div>
            </div>

            <div class="cleardeal-stat-card">
                <div class="cleardeal-stat-icon cleardeal-stat-icon-green">
                    <i class="material-icons">trending_up</i>
                </div>
                <div class="cleardeal-stat-content">
                    <span class="cleardeal-stat-value">{$stats.last_7_days|number_format:0:',':' '}</span>
                    <span class="cleardeal-stat-label">{l s='Last 7 Days' mod='cleardeal'}</span>
                </div>
            </div>

            <div class="cleardeal-stat-card">
                <div class="cleardeal-stat-icon cleardeal-stat-icon-orange">
                    <i class="material-icons">calendar_month</i>
                </div>
                <div class="cleardeal-stat-content">
                    <span class="cleardeal-stat-value">{$stats.last_30_days|number_format:0:',':' '}</span>
                    <span class="cleardeal-stat-label">{l s='Last 30 Days' mod='cleardeal'}</span>
                </div>
            </div>
        </div>

        <div class="cleardeal-stats-info">
            <span class="cleardeal-stats-meta">
                {if $stats.oldest_log}
                    {l s='Data from' mod='cleardeal'} {$stats.oldest_log|date_format:"%Y-%m-%d"}
                    {l s='to' mod='cleardeal'} {$stats.newest_log|date_format:"%Y-%m-%d"}
                {/if}
                {if $stats.from_cache}
                    &nbsp;•&nbsp;
                    <span class="cleardeal-cache-info">
                        {l s='Cached' mod='cleardeal'}
                        ({$stats.cache_age|intval} {l s='sec ago' mod='cleardeal'})
                    </span>
                {/if}
            </span>
            <a href="{$admin_link}&tab=logs&refresh_stats=1" class="cleardeal-btn-link">
                <i class="material-icons">refresh</i>
                {l s='Refresh stats' mod='cleardeal'}
            </a>
        </div>
    {/if}

    {* Toolbar *}
    <div class="cleardeal-toolbar">
        <div class="cleardeal-toolbar-left">
            <span class="cleardeal-logs-count">
                {l s='Total:' mod='cleardeal'} <strong>{$logs_count}</strong> {l s='entries' mod='cleardeal'}
            </span>
        </div>
        <div class="cleardeal-toolbar-right">
            <button type="button" class="cleardeal-btn cleardeal-btn-secondary {if $logs_filters.product_search || $logs_filters.date_from || $logs_filters.date_to}cleardeal-btn-filter-active{/if}" id="filter_toggle_btn">
                <i class="material-icons">filter_list</i>
                {l s='Filters' mod='cleardeal'}
                {if $logs_filters.product_search || $logs_filters.date_from || $logs_filters.date_to}
                    <span class="cleardeal-filter-badge"></span>
                {/if}
            </button>
            <a href="{$admin_link}&tab=logs&action=export{$filter_params}" class="cleardeal-btn cleardeal-btn-secondary" title="{if $logs_filters.product_search || $logs_filters.date_from || $logs_filters.date_to}{l s='Export filtered results' mod='cleardeal'}{else}{l s='Export all logs' mod='cleardeal'}{/if}">
                <i class="material-icons">download</i>
                {l s='Export CSV' mod='cleardeal'}
                {if $logs_filters.product_search || $logs_filters.date_from || $logs_filters.date_to}
                    <span class="cleardeal-export-filtered">({l s='filtered' mod='cleardeal'})</span>
                {/if}
            </a>
            <button type="button" class="cleardeal-btn cleardeal-btn-secondary" id="import_btn">
                <i class="material-icons">upload</i>
                {l s='Import CSV' mod='cleardeal'}
            </button>
            <a href="{$admin_link}&tab=logs&action=clean_old" class="cleardeal-btn cleardeal-btn-outline" onclick="return confirm('{l s='Are you sure you want to delete old logs?' mod='cleardeal' js=1}');">
                <i class="material-icons">delete_sweep</i>
                {l s='Clean Old Logs' mod='cleardeal'}
            </a>
            <a href="{$admin_link}&tab=logs&action=log_all" class="cleardeal-btn cleardeal-btn-primary" onclick="return confirm('{l s='This will log current prices for all active products. Continue?' mod='cleardeal' js=1}');">
                <i class="material-icons">playlist_add</i>
                {l s='Log All Prices Now' mod='cleardeal'}
            </a>
        </div>
    </div>

    {* Filters (collapsible) *}
    <div class="cleardeal-filters" id="filters_panel" style="display: {if $logs_filters.product_search || $logs_filters.date_from || $logs_filters.date_to}block{else}none{/if};">
        <form action="{$admin_link}&tab=logs" method="get" class="cleardeal-filters-form">
            <input type="hidden" name="controller" value="AdminClearDeal">
            <input type="hidden" name="token" value="{$token}">
            <input type="hidden" name="tab" value="logs">

            <div class="cleardeal-filters-row">
                <div class="cleardeal-filter-group">
                    <label>{l s='Product (ID or name)' mod='cleardeal'}</label>
                    <input type="text" name="filter_product" value="{$logs_filters.product_search|escape:'html':'UTF-8'}" class="cleardeal-input cleardeal-input-filter" placeholder="{l s='Search...' mod='cleardeal'}">
                </div>

                <div class="cleardeal-filter-group">
                    <label>{l s='Date from' mod='cleardeal'}</label>
                    <input type="date" name="filter_date_from" value="{$logs_filters.date_from|escape:'html':'UTF-8'}" class="cleardeal-input cleardeal-input-filter">
                </div>

                <div class="cleardeal-filter-group">
                    <label>{l s='Date to' mod='cleardeal'}</label>
                    <input type="date" name="filter_date_to" value="{$logs_filters.date_to|escape:'html':'UTF-8'}" class="cleardeal-input cleardeal-input-filter">
                </div>

                <div class="cleardeal-filter-actions">
                    <button type="submit" class="cleardeal-btn cleardeal-btn-primary cleardeal-btn-filter">
                        <i class="material-icons">check</i>
                        {l s='Apply' mod='cleardeal'}
                    </button>
                    {if $logs_filters.product_search || $logs_filters.date_from || $logs_filters.date_to}
                        <a href="{$admin_link}&tab=logs" class="cleardeal-btn cleardeal-btn-outline cleardeal-btn-filter">
                            <i class="material-icons">clear</i>
                            {l s='Clear' mod='cleardeal'}
                        </a>
                    {/if}
                </div>
            </div>
        </form>
    </div>

    {* Import Form (hidden by default) *}
    <div class="cleardeal-import-form" id="import_form" style="display:none;">
        <form action="{$admin_link}&tab=logs" method="post" enctype="multipart/form-data">
            <div class="cleardeal-card">
                <div class="cleardeal-card-header">
                    <i class="material-icons">upload_file</i>
                    <h3>{l s='Import Price Logs' mod='cleardeal'}</h3>
                    <button type="button" class="cleardeal-close-btn" id="close_import">
                        <i class="material-icons">close</i>
                    </button>
                </div>
                <div class="cleardeal-card-body">
                    <div class="cleardeal-field">
                        <label>{l s='CSV File' mod='cleardeal'}</label>
                        <input type="file" name="import_file" accept=".csv" required class="cleardeal-file-input">
                        <span class="cleardeal-hint">
                            {l s='Upload a CSV file exported from ClearDeal or in compatible format.' mod='cleardeal'}
                        </span>
                    </div>
                    <button type="submit" name="submitImportCsv" class="cleardeal-btn cleardeal-btn-primary">
                        <i class="material-icons">upload</i>
                        {l s='Import' mod='cleardeal'}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {* Logs Table *}
    <form action="{$admin_link}&tab=logs&action=delete_logs" method="post" id="logs_form">
        <div class="cleardeal-table-wrapper">
            <table class="cleardeal-table">
                <thead>
                    <tr>
                        <th class="cleardeal-th-check">
                            <input type="checkbox" id="check_all">
                        </th>
                        <th>{l s='ID' mod='cleardeal'}</th>
                        <th>{l s='Product' mod='cleardeal'}</th>
                        <th>{l s='Attr. ID' mod='cleardeal'}</th>
                        <th>{l s='Price (Gross)' mod='cleardeal'}</th>
                        <th>{l s='Price (Net)' mod='cleardeal'}</th>
                        <th>{l s='Currency' mod='cleardeal'}</th>
                        <th>{l s='Country' mod='cleardeal'}</th>
                        <th>{l s='Group' mod='cleardeal'}</th>
                        <th>{l s='Date' mod='cleardeal'}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {if $logs|count > 0}
                        {foreach from=$logs item=log}
                            <tr>
                                <td class="cleardeal-td-check">
                                    <input type="checkbox" name="log_ids[]" value="{$log.id_history}">
                                </td>
                                <td class="cleardeal-td-id">{$log.id_history}</td>
                                <td class="cleardeal-td-product">
                                    <span class="cleardeal-product-id">#{$log.id_product}</span>
                                    <a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $log.id_product, 'updateproduct' => 1])}"
                                       target="_blank"
                                       class="cleardeal-product-link"
                                       title="{l s='Edit product in admin' mod='cleardeal'}">
                                        {$log.product_name|escape:'html':'UTF-8'|truncate:40:'...'}
                                    </a>
                                    <a href="{$link->getProductLink($log.id_product)}"
                                       target="_blank"
                                       class="cleardeal-product-front-link"
                                       title="{l s='View on storefront' mod='cleardeal'}">
                                        <i class="material-icons">open_in_new</i>
                                    </a>
                                </td>
                                <td class="cleardeal-td-center">{$log.id_product_attribute}</td>
                                <td class="cleardeal-td-price">{$log.price_tax_incl|string_format:"%.2f"}</td>
                                <td class="cleardeal-td-price">{$log.price_tax_excl|string_format:"%.2f"}</td>
                                <td class="cleardeal-td-center">{$log.id_currency}</td>
                                <td class="cleardeal-td-center">{$log.id_country}</td>
                                <td class="cleardeal-td-center">{$log.id_group}</td>
                                <td class="cleardeal-td-date">{$log.captured_at|date_format:"%Y-%m-%d %H:%M"}</td>
                                <td class="cleardeal-td-actions">
                                    <a href="{$admin_link}&tab=logs&action=delete_single&id={$log.id_history}"
                                       class="cleardeal-btn-delete-single"
                                       onclick="return confirm('{l s='Delete this log entry?' mod='cleardeal' js=1}');"
                                       title="{l s='Delete' mod='cleardeal'}">
                                        <i class="material-icons">delete_outline</i>
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="11" class="cleardeal-empty">
                                <i class="material-icons">inbox</i>
                                <p>{l s='No price logs found.' mod='cleardeal'}</p>
                                <span>{l s='Price history will be recorded when products are updated.' mod='cleardeal'}</span>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>

        {* Pagination *}
        {if $logs_total_pages > 1}
            <div class="cleardeal-pagination">
                <div class="cleardeal-pagination-info">
                    {l s='Page' mod='cleardeal'} {$logs_page} {l s='of' mod='cleardeal'} {$logs_total_pages}
                </div>
                <div class="cleardeal-pagination-links">
                    {if $logs_page > 1}
                        <a href="{$admin_link}&tab=logs&page=1{$filter_params}" class="cleardeal-page-link">
                            <i class="material-icons">first_page</i>
                        </a>
                        <a href="{$admin_link}&tab=logs&page={$logs_page - 1}{$filter_params}" class="cleardeal-page-link">
                            <i class="material-icons">chevron_left</i>
                        </a>
                    {/if}

                    {for $p=max(1, $logs_page-2) to min($logs_total_pages, $logs_page+2)}
                        <a href="{$admin_link}&tab=logs&page={$p}{$filter_params}" class="cleardeal-page-link {if $p == $logs_page}active{/if}">{$p}</a>
                    {/for}

                    {if $logs_page < $logs_total_pages}
                        <a href="{$admin_link}&tab=logs&page={$logs_page + 1}{$filter_params}" class="cleardeal-page-link">
                            <i class="material-icons">chevron_right</i>
                        </a>
                        <a href="{$admin_link}&tab=logs&page={$logs_total_pages}{$filter_params}" class="cleardeal-page-link">
                            <i class="material-icons">last_page</i>
                        </a>
                    {/if}
                </div>
            </div>
        {/if}

        {* Bulk Actions *}
        {if $logs|count > 0}
            <div class="cleardeal-bulk-actions">
                <button type="submit" class="cleardeal-btn cleardeal-btn-danger" onclick="return confirm('{l s='Are you sure you want to delete selected logs?' mod='cleardeal' js=1}');">
                    <i class="material-icons">delete</i>
                    {l s='Delete Selected' mod='cleardeal'}
                </button>
            </div>
        {/if}
    </form>
</div>
