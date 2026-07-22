<?php
/**
 * ClearDeal - EU Omnibus Directive Compliance
 *
 * Admin Controller - handles settings and logs
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'cleardeal/classes/ClearDealPriceHistory.php';

class AdminClearDealController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function init()
    {
        parent::init();

        // Handle AJAX requests
        if (Tools::getValue('ajax')) {
            $this->ajax = true;
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        // Translate button
        $this->page_header_toolbar_btn['translate'] = [
            'href' => $this->context->link->getAdminLink('AdminTranslations', true, [], [
                'type' => 'modules',
                'module' => $this->module->name,
                'lang' => $this->context->language->iso_code,
            ]),
            'desc' => $this->trans('Translate', [], 'Admin.Actions'),
            'icon' => 'process-icon-flag',
        ];

        // Manage hooks button
        $this->page_header_toolbar_btn['hooks'] = [
            'href' => $this->context->link->getAdminLink('AdminModulesPositions') . '&show_modules=' . (int) $this->module->id,
            'desc' => $this->trans('Manage hooks', [], 'Admin.Modules.Feature'),
            'icon' => 'anchor',
        ];
    }

    public function initContent()
    {
        parent::initContent();

        // Handle actions
        $this->processActions();

        // Add CSS and JS
        $this->addCSS($this->module->getPathUri() . 'views/css/admin.css');
        $this->addJS($this->module->getPathUri() . 'views/js/admin.js');

        // Get current tab
        $current_tab = Tools::getValue('tab', 'settings');

        // Get logs for logs tab
        $logs = [];
        $logs_count = 0;
        $current_page = 1;
        $per_page = 50;
        $filters = [];
        $stats = [];

        if ($current_tab === 'logs') {
            $current_page = max(1, (int) Tools::getValue('page', 1));

            // Get filters
            $filters = [
                'product_search' => Tools::getValue('filter_product', ''),
                'date_from' => Tools::getValue('filter_date_from', ''),
                'date_to' => Tools::getValue('filter_date_to', ''),
            ];

            $logs = ClearDealPriceHistory::getLogs($current_page, $per_page, 'captured_at', 'DESC', $filters);
            $logs_count = ClearDealPriceHistory::getLogsCount($filters);

            // Get cached statistics
            $force_refresh = Tools::getValue('refresh_stats') === '1';
            $stats = ClearDealPriceHistory::getStatistics($force_refresh);
        }

        // Generate CRON URL
        $cron_token = Tools::hash(Configuration::get('PS_SHOP_NAME'));
        $cron_url = $this->context->link->getModuleLink($this->module->name, 'cron', ['token' => $cron_token]);

        // Get categories for exclusion settings
        $all_categories = Category::getCategories($this->context->language->id, true, false);
        $excluded_categories = ClearDeal::getExcludedCategoriesWithNames();
        $excluded_category_ids = array_column($excluded_categories, 'id');

        // Find a product with active discount for preview
        $preview_product_url = $this->getPreviewProductUrl();

        // Assign template variables
        $this->context->smarty->assign([
            'module_dir' => $this->module->getPathUri(),
            'module_version' => $this->module->version,
            'current_tab' => $current_tab,
            'settings' => $this->getConfigValues(),
            'languages' => Language::getLanguages(false),
            'default_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'current_lang' => (int) $this->context->language->id,
            'logs' => $logs,
            'logs_count' => $logs_count,
            'logs_page' => $current_page,
            'logs_per_page' => $per_page,
            'logs_total_pages' => ceil($logs_count / $per_page),
            'logs_filters' => $filters,
            'admin_link' => $this->context->link->getAdminLink('AdminClearDeal'),
            'token' => $this->token,
            'cron_url' => $cron_url,
            'generated_css' => $this->generatePreviewCss(),
            'all_categories' => $all_categories,
            'excluded_categories' => $excluded_categories,
            'excluded_category_ids' => $excluded_category_ids,
            'stats' => $stats,
            'preview_product_url' => $preview_product_url,
        ]);

        $this->setTemplate('configure.tpl');
    }

    public function processActions()
    {
        // Save settings
        if (Tools::isSubmit('submitClearDealSettings')) {
            $this->processSaveSettings();
        }

        // Save appearance settings
        if (Tools::isSubmit('submitClearDealAppearance')) {
            $this->processSaveAppearance();
        }

        // Export CSV
        if (Tools::getValue('action') === 'export') {
            $this->doExportCsv();
        }

        // Import CSV
        if (Tools::isSubmit('submitImportCsv')) {
            $this->processImportCsv();
        }

        // Delete logs
        if (Tools::getValue('action') === 'delete_logs') {
            $this->processDeleteLogs();
        }

        // Clean old logs
        if (Tools::getValue('action') === 'clean_old') {
            $this->processCleanOldLogs();
        }

        // Delete single log
        if (Tools::getValue('action') === 'delete_single') {
            $this->processDeleteSingleLog();
        }

        // Log all prices manually
        if (Tools::getValue('action') === 'log_all') {
            $this->processLogAllPrices();
        }
    }

    public function processLogAllPrices()
    {
        $result = ClearDealPriceHistory::logAllPrices();

        $this->confirmations[] = sprintf(
            $this->module->l('Price logging completed. Logged: %d, Skipped (excluded categories): %d, Errors: %d'),
            $result['logged'],
            $result['skipped'],
            $result['errors']
        );
    }

    public function processSaveAppearance()
    {
        // Base settings
        Configuration::updateValue('CLEARDEAL_STYLE_PRESET', pSQL(Tools::getValue('CLEARDEAL_STYLE_PRESET', 'default')));
        Configuration::updateValue('CLEARDEAL_USE_CUSTOM_COLORS', (int) Tools::getValue('CLEARDEAL_USE_CUSTOM_COLORS', 0));

        // Default preset colors
        Configuration::updateValue('CLEARDEAL_COLOR_BACKGROUND', pSQL(Tools::getValue('CLEARDEAL_COLOR_BACKGROUND', '#f6f9fc')));
        Configuration::updateValue('CLEARDEAL_COLOR_BORDER', pSQL(Tools::getValue('CLEARDEAL_COLOR_BORDER', '#e3e8ee')));
        Configuration::updateValue('CLEARDEAL_COLOR_PRIMARY', pSQL(Tools::getValue('CLEARDEAL_COLOR_PRIMARY', '#635bff')));
        Configuration::updateValue('CLEARDEAL_COLOR_BADGE', pSQL(Tools::getValue('CLEARDEAL_COLOR_BADGE', '#fce4ec')));
        Configuration::updateValue('CLEARDEAL_USE_GRADIENT', (int) Tools::getValue('CLEARDEAL_USE_GRADIENT', 0));
        Configuration::updateValue('CLEARDEAL_COLOR_GRADIENT_END', pSQL(Tools::getValue('CLEARDEAL_COLOR_GRADIENT_END', '#e0e5eb')));

        // Minimal preset colors
        Configuration::updateValue('CLEARDEAL_COLOR_MINIMAL_ICON', pSQL(Tools::getValue('CLEARDEAL_COLOR_MINIMAL_ICON', '#9ca3af')));
        Configuration::updateValue('CLEARDEAL_COLOR_MINIMAL_LABEL', pSQL(Tools::getValue('CLEARDEAL_COLOR_MINIMAL_LABEL', '#9ca3af')));
        Configuration::updateValue('CLEARDEAL_COLOR_MINIMAL_PRICE', pSQL(Tools::getValue('CLEARDEAL_COLOR_MINIMAL_PRICE', '#374151')));
        Configuration::updateValue('CLEARDEAL_COLOR_MINIMAL_BADGE', pSQL(Tools::getValue('CLEARDEAL_COLOR_MINIMAL_BADGE', '#c41535')));

        // Bold preset colors
        Configuration::updateValue('CLEARDEAL_COLOR_BOLD_START', pSQL(Tools::getValue('CLEARDEAL_COLOR_BOLD_START', '#6366f1')));
        Configuration::updateValue('CLEARDEAL_COLOR_BOLD_END', pSQL(Tools::getValue('CLEARDEAL_COLOR_BOLD_END', '#a855f7')));
        Configuration::updateValue('CLEARDEAL_COLOR_BOLD_BADGE', pSQL(Tools::getValue('CLEARDEAL_COLOR_BOLD_BADGE', '#ffffff')));

        // Tooltip colors
        Configuration::updateValue('CLEARDEAL_TOOLTIP_BG', pSQL(Tools::getValue('CLEARDEAL_TOOLTIP_BG', '#1a1f36')));

        // Modal header color
        Configuration::updateValue('CLEARDEAL_MODAL_HEADER_COLOR', pSQL(Tools::getValue('CLEARDEAL_MODAL_HEADER_COLOR', '#635bff')));

        // Chart colors
        Configuration::updateValue('CLEARDEAL_CHART_LINE_COLOR', pSQL(Tools::getValue('CLEARDEAL_CHART_LINE_COLOR', '#635bff')));
        Configuration::updateValue('CLEARDEAL_CHART_FILL_COLOR', pSQL(Tools::getValue('CLEARDEAL_CHART_FILL_COLOR', '#635bff')));

        // Custom CSS
        Configuration::updateValue('CLEARDEAL_CUSTOM_CSS', Tools::getValue('CLEARDEAL_CUSTOM_CSS', ''), true);

        // Clear cache
        Tools::clearSmartyCache();

        $this->confirmations[] = $this->module->l('Appearance settings saved successfully.');
    }

    public function processSaveSettings()
    {
        $languages = Language::getLanguages(false);

        // Basic settings
        Configuration::updateValue('CLEARDEAL_DAYS', (int) Tools::getValue('CLEARDEAL_DAYS', 30));
        Configuration::updateValue('CLEARDEAL_DISPLAY_MODE', pSQL(Tools::getValue('CLEARDEAL_DISPLAY_MODE', 'on-discount')));
        Configuration::updateValue('CLEARDEAL_PRICE_TYPE', pSQL(Tools::getValue('CLEARDEAL_PRICE_TYPE', 'gross')));
        Configuration::updateValue('CLEARDEAL_SHOW_PERCENT', (int) Tools::getValue('CLEARDEAL_SHOW_PERCENT', 0));
        Configuration::updateValue('CLEARDEAL_PRECISION', (int) Tools::getValue('CLEARDEAL_PRECISION', 0));

        // Icon settings
        Configuration::updateValue('CLEARDEAL_SHOW_ICON', (int) Tools::getValue('CLEARDEAL_SHOW_ICON', 0));
        Configuration::updateValue('CLEARDEAL_ICON_POSITION', pSQL(Tools::getValue('CLEARDEAL_ICON_POSITION', 'start')));
        Configuration::updateValue('CLEARDEAL_ICON_TYPE', pSQL(Tools::getValue('CLEARDEAL_ICON_TYPE', 'bootstrap')));
        Configuration::updateValue('CLEARDEAL_ICON_CLASS', pSQL(Tools::getValue('CLEARDEAL_ICON_CLASS', 'info')));

        // Handle icon image upload
        if (isset($_FILES['CLEARDEAL_ICON_IMAGE']) && !empty($_FILES['CLEARDEAL_ICON_IMAGE']['tmp_name'])) {
            $ext = pathinfo($_FILES['CLEARDEAL_ICON_IMAGE']['name'], PATHINFO_EXTENSION);
            $allowed = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];

            if (in_array(strtolower($ext), $allowed)) {
                $file_name = 'icon_' . md5(time()) . '.' . $ext;
                $dest = _PS_MODULE_DIR_ . 'cleardeal/views/img/' . $file_name;

                if (move_uploaded_file($_FILES['CLEARDEAL_ICON_IMAGE']['tmp_name'], $dest)) {
                    // Delete old icon
                    $old_icon = Configuration::get('CLEARDEAL_ICON_IMAGE');
                    if ($old_icon && file_exists(_PS_MODULE_DIR_ . 'cleardeal/views/img/' . $old_icon)) {
                        unlink(_PS_MODULE_DIR_ . 'cleardeal/views/img/' . $old_icon);
                    }
                    Configuration::updateValue('CLEARDEAL_ICON_IMAGE', $file_name);
                }
            }
        }

        // Delete icon
        if (Tools::getValue('delete_icon')) {
            $old_icon = Configuration::get('CLEARDEAL_ICON_IMAGE');
            if ($old_icon && file_exists(_PS_MODULE_DIR_ . 'cleardeal/views/img/' . $old_icon)) {
                unlink(_PS_MODULE_DIR_ . 'cleardeal/views/img/' . $old_icon);
            }
            Configuration::updateValue('CLEARDEAL_ICON_IMAGE', '');
        }

        // Multilingual labels
        $label_product = [];
        $label_listing = [];
        $label_quickview = [];
        $tooltip = [];

        foreach ($languages as $lang) {
            $id = (int) $lang['id_lang'];
            $label_product[$id] = Tools::getValue('CLEARDEAL_LABEL_PRODUCT_' . $id, '');
            $label_listing[$id] = Tools::getValue('CLEARDEAL_LABEL_LISTING_' . $id, '');
            $label_quickview[$id] = Tools::getValue('CLEARDEAL_LABEL_QUICKVIEW_' . $id, '');
            $tooltip[$id] = Tools::getValue('CLEARDEAL_ICON_TOOLTIP_' . $id, '');
        }

        Configuration::updateValue('CLEARDEAL_LABEL_PRODUCT', $label_product);
        Configuration::updateValue('CLEARDEAL_LABEL_LISTING', $label_listing);
        Configuration::updateValue('CLEARDEAL_LABEL_QUICKVIEW', $label_quickview);
        Configuration::updateValue('CLEARDEAL_ICON_TOOLTIP', $tooltip);

        // Advanced settings
        Configuration::updateValue('CLEARDEAL_LOGGING_MODE', pSQL(Tools::getValue('CLEARDEAL_LOGGING_MODE', 'immediate')));
        Configuration::updateValue('CLEARDEAL_LOG_RETENTION', (int) Tools::getValue('CLEARDEAL_LOG_RETENTION', 365));

        // Category exclusions
        $excluded = Tools::getValue('CLEARDEAL_EXCLUDED_CATEGORIES', '[]');
        // Validate JSON
        $decoded = json_decode($excluded, true);
        if (is_array($decoded)) {
            // Sanitize - only integers
            $decoded = array_map('intval', $decoded);
            $decoded = array_filter($decoded);
            $decoded = array_values(array_unique($decoded));
            Configuration::updateValue('CLEARDEAL_EXCLUDED_CATEGORIES', json_encode($decoded));
        }

        // Chart settings
        Configuration::updateValue('CLEARDEAL_SHOW_CHART', (int) Tools::getValue('CLEARDEAL_SHOW_CHART', 0));
        Configuration::updateValue('CLEARDEAL_CHART_TRIGGER', pSQL(Tools::getValue('CLEARDEAL_CHART_TRIGGER', 'icon')));

        // Clear Smarty cache to apply new settings
        Tools::clearSmartyCache();

        $this->confirmations[] = $this->module->l('Settings saved successfully.');
    }

    public function doExportCsv()
    {
        // Get filters from request to export filtered results
        $filters = [
            'product_search' => Tools::getValue('filter_product', ''),
            'date_from' => Tools::getValue('filter_date_from', ''),
            'date_to' => Tools::getValue('filter_date_to', ''),
        ];

        $csv = ClearDealPriceHistory::exportToCsv($filters);

        // Create filename with filter info
        $filename = 'cleardeal_logs_' . date('Y-m-d');
        if ($filters['product_search'] || $filters['date_from'] || $filters['date_to']) {
            $filename .= '_filtered';
        }
        $filename .= '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Add BOM for Excel UTF-8 compatibility
        echo "\xEF\xBB\xBF";
        echo $csv;
        exit;
    }

    public function processImportCsv()
    {
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->module->l('Error uploading file.');
            return;
        }

        $imported = ClearDealPriceHistory::importFromCsv($_FILES['import_file']['tmp_name']);

        $this->confirmations[] = sprintf($this->module->l('Successfully imported %d log entries.'), $imported);
    }

    public function processDeleteLogs()
    {
        $ids = Tools::getValue('log_ids', []);

        if (empty($ids)) {
            $this->errors[] = $this->module->l('No logs selected.');
            return;
        }

        if (ClearDealPriceHistory::deleteByIds($ids)) {
            $this->confirmations[] = sprintf($this->module->l('Deleted %d log entries.'), count($ids));
        } else {
            $this->errors[] = $this->module->l('Error deleting logs.');
        }
    }

    public function processCleanOldLogs()
    {
        $days = (int) Configuration::get('CLEARDEAL_LOG_RETENTION') ?: 365;

        if (ClearDealPriceHistory::cleanOldLogs($days)) {
            $this->confirmations[] = sprintf($this->module->l('Deleted logs older than %d days.'), $days);
        } else {
            $this->errors[] = $this->module->l('Error cleaning old logs.');
        }
    }

    public function processDeleteSingleLog()
    {
        $id = (int) Tools::getValue('id');

        if (!$id) {
            $this->errors[] = $this->module->l('Invalid log ID.');
            return;
        }

        if (ClearDealPriceHistory::deleteByIds([$id])) {
            $this->confirmations[] = $this->module->l('Log entry deleted.');
        } else {
            $this->errors[] = $this->module->l('Error deleting log entry.');
        }
    }

    public function getConfigValues()
    {
        $languages = Language::getLanguages(false);

        $values = [
            'CLEARDEAL_DAYS' => Configuration::get('CLEARDEAL_DAYS') ?: 30,
            'CLEARDEAL_DISPLAY_MODE' => Configuration::get('CLEARDEAL_DISPLAY_MODE') ?: 'on-discount',
            'CLEARDEAL_PRICE_TYPE' => Configuration::get('CLEARDEAL_PRICE_TYPE') ?: 'gross',
            'CLEARDEAL_SHOW_PERCENT' => Configuration::get('CLEARDEAL_SHOW_PERCENT'),
            'CLEARDEAL_PRECISION' => Configuration::get('CLEARDEAL_PRECISION') ?: 0,
            'CLEARDEAL_SHOW_ICON' => Configuration::get('CLEARDEAL_SHOW_ICON'),
            'CLEARDEAL_ICON_POSITION' => Configuration::get('CLEARDEAL_ICON_POSITION') ?: 'start',
            'CLEARDEAL_ICON_TYPE' => Configuration::get('CLEARDEAL_ICON_TYPE') ?: 'bootstrap',
            'CLEARDEAL_ICON_CLASS' => Configuration::get('CLEARDEAL_ICON_CLASS') ?: 'info-circle',
            'CLEARDEAL_ICON_IMAGE' => Configuration::get('CLEARDEAL_ICON_IMAGE') ?: '',
            'CLEARDEAL_LOGGING_MODE' => Configuration::get('CLEARDEAL_LOGGING_MODE') ?: 'immediate',
            'CLEARDEAL_LOG_RETENTION' => Configuration::get('CLEARDEAL_LOG_RETENTION') ?: 365,
            // Appearance settings
            'CLEARDEAL_STYLE_PRESET' => Configuration::get('CLEARDEAL_STYLE_PRESET') ?: 'default',
            'CLEARDEAL_USE_CUSTOM_COLORS' => Configuration::get('CLEARDEAL_USE_CUSTOM_COLORS'),
            'CLEARDEAL_CUSTOM_CSS' => Configuration::get('CLEARDEAL_CUSTOM_CSS') ?: '',
            // Default preset colors
            'CLEARDEAL_COLOR_BACKGROUND' => Configuration::get('CLEARDEAL_COLOR_BACKGROUND') ?: '#f6f9fc',
            'CLEARDEAL_COLOR_BORDER' => Configuration::get('CLEARDEAL_COLOR_BORDER') ?: '#e3e8ee',
            'CLEARDEAL_COLOR_PRIMARY' => Configuration::get('CLEARDEAL_COLOR_PRIMARY') ?: '#635bff',
            'CLEARDEAL_COLOR_BADGE' => Configuration::get('CLEARDEAL_COLOR_BADGE') ?: '#fce4ec',
            'CLEARDEAL_USE_GRADIENT' => Configuration::get('CLEARDEAL_USE_GRADIENT'),
            'CLEARDEAL_COLOR_GRADIENT_END' => Configuration::get('CLEARDEAL_COLOR_GRADIENT_END') ?: '#e0e5eb',
            // Minimal preset colors
            'CLEARDEAL_COLOR_MINIMAL_ICON' => Configuration::get('CLEARDEAL_COLOR_MINIMAL_ICON') ?: '#9ca3af',
            'CLEARDEAL_COLOR_MINIMAL_LABEL' => Configuration::get('CLEARDEAL_COLOR_MINIMAL_LABEL') ?: '#6b7280',
            'CLEARDEAL_COLOR_MINIMAL_PRICE' => Configuration::get('CLEARDEAL_COLOR_MINIMAL_PRICE') ?: '#1f2937',
            'CLEARDEAL_COLOR_MINIMAL_BADGE' => Configuration::get('CLEARDEAL_COLOR_MINIMAL_BADGE') ?: '#dc2626',
            // Bold preset colors
            'CLEARDEAL_COLOR_BOLD_START' => Configuration::get('CLEARDEAL_COLOR_BOLD_START') ?: '#6366f1',
            'CLEARDEAL_COLOR_BOLD_END' => Configuration::get('CLEARDEAL_COLOR_BOLD_END') ?: '#a855f7',
            'CLEARDEAL_COLOR_BOLD_BADGE' => Configuration::get('CLEARDEAL_COLOR_BOLD_BADGE') ?: '#fbbf24',
            // Category exclusions
            'CLEARDEAL_EXCLUDED_CATEGORIES' => Configuration::get('CLEARDEAL_EXCLUDED_CATEGORIES') ?: '[]',
            // Chart settings
            'CLEARDEAL_SHOW_CHART' => Configuration::get('CLEARDEAL_SHOW_CHART'),
            'CLEARDEAL_CHART_TRIGGER' => Configuration::get('CLEARDEAL_CHART_TRIGGER') ?: 'icon',
            // Tooltip colors
            'CLEARDEAL_TOOLTIP_BG' => Configuration::get('CLEARDEAL_TOOLTIP_BG') ?: '#1a1f36',
            // Modal header color
            'CLEARDEAL_MODAL_HEADER_COLOR' => Configuration::get('CLEARDEAL_MODAL_HEADER_COLOR') ?: '#635bff',
            // Chart colors
            'CLEARDEAL_CHART_LINE_COLOR' => Configuration::get('CLEARDEAL_CHART_LINE_COLOR') ?: '#635bff',
            'CLEARDEAL_CHART_FILL_COLOR' => Configuration::get('CLEARDEAL_CHART_FILL_COLOR') ?: '#635bff',
        ];

        // Multilingual values
        foreach ($languages as $lang) {
            $id = (int) $lang['id_lang'];
            $values['CLEARDEAL_LABEL_PRODUCT'][$id] = Configuration::get('CLEARDEAL_LABEL_PRODUCT', $id) ?: '';
            $values['CLEARDEAL_LABEL_LISTING'][$id] = Configuration::get('CLEARDEAL_LABEL_LISTING', $id) ?: '';
            $values['CLEARDEAL_LABEL_QUICKVIEW'][$id] = Configuration::get('CLEARDEAL_LABEL_QUICKVIEW', $id) ?: '';
            $values['CLEARDEAL_ICON_TOOLTIP'][$id] = Configuration::get('CLEARDEAL_ICON_TOOLTIP', $id) ?: '';
        }

        return $values;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        // Add Material Icons (for admin UI)
        $this->addCSS('https://fonts.googleapis.com/icon?family=Material+Icons');
        // Add Bootstrap Icons (for icon preview/settings)
        $this->addCSS('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
    }

    /**
     * Find a product with active discount and return its frontend URL
     *
     * @return string|null URL to the product or null if no discounted product found
     */
    protected function getPreviewProductUrl()
    {
        $id_shop = (int) $this->context->shop->id;
        $id_lang = (int) $this->context->language->id;

        // Find a product with active specific_price (discount)
        $sql = 'SELECT DISTINCT sp.id_product, sp.id_product_attribute FROM `' . _DB_PREFIX_ . 'specific_price` sp INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON ps.id_product = sp.id_product AND ps.id_shop = ' . $id_shop . ' WHERE sp.reduction > 0 AND (sp.id_shop = 0 OR sp.id_shop = ' . $id_shop . ') AND (sp.from = "0000-00-00 00:00:00" OR sp.from <= NOW()) AND (sp.to = "0000-00-00 00:00:00" OR sp.to >= NOW()) AND ps.active = 1 ORDER BY sp.id_specific_price DESC';

        $result = Db::getInstance()->getRow($sql);

        if (!$result || empty($result['id_product'])) {
            return null;
        }

        $id_product = (int) $result['id_product'];
        $id_product_attribute = (int) $result['id_product_attribute'];

        // Generate frontend URL
        return $this->context->link->getProductLink(
            $id_product,
            null,
            null,
            null,
            $id_lang,
            $id_shop,
            $id_product_attribute
        );
    }

    /**
     * Generate CSS based on current appearance settings
     *
     * @return string
     */
    protected function generatePreviewCss()
    {
        $preset = Configuration::get('CLEARDEAL_STYLE_PRESET') ?: 'default';
        $useCustomColors = (bool) Configuration::get('CLEARDEAL_USE_CUSTOM_COLORS');
        $colorPrimary = Configuration::get('CLEARDEAL_COLOR_PRIMARY') ?: '#635bff';
        $colorBadge = Configuration::get('CLEARDEAL_COLOR_BADGE') ?: '#fce4ec';
        $colorBackground = Configuration::get('CLEARDEAL_COLOR_BACKGROUND') ?: '#f6f9fc';
        $colorBorder = Configuration::get('CLEARDEAL_COLOR_BORDER') ?: '#e3e8ee';

        $css = "/* ClearDeal - Generated CSS */\n\n";

        if ($useCustomColors) {
            // Custom colors mode
            $css .= ".cleardeal-wrapper {\n";
            $css .= "    background: {$colorBackground};\n";
            $css .= "    border: 1px solid {$colorBorder};\n";
            $css .= "}\n\n";
            $css .= ".cleardeal-icon {\n";
            $css .= "    color: {$colorPrimary};\n";
            $css .= "}\n\n";
            $css .= ".cleardeal-percent {\n";
            $css .= "    background: {$colorBadge};\n";
            $css .= "    color: " . $this->getContrastColor($colorBadge) . ";\n";
            $css .= "}\n";
        } else {
            // Preset mode
            switch ($preset) {
                case 'minimal':
                    $css .= ".cleardeal-wrapper {\n";
                    $css .= "    background: transparent;\n";
                    $css .= "    border: none;\n";
                    $css .= "    padding: 4px 0;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-icon {\n";
                    $css .= "    color: #9ca3af;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-label {\n";
                    $css .= "    color: #9ca3af;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-price {\n";
                    $css .= "    color: #374151;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-percent {\n";
                    $css .= "    background: transparent;\n";
                    $css .= "    color: #059669;\n";
                    $css .= "}\n";
                    break;

                case 'bold':
                    $css .= ".cleardeal-wrapper {\n";
                    $css .= "    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);\n";
                    $css .= "    border: none;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-icon {\n";
                    $css .= "    color: rgba(255, 255, 255, 0.9);\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-label {\n";
                    $css .= "    color: rgba(255, 255, 255, 0.8);\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-price {\n";
                    $css .= "    color: #fff;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-percent {\n";
                    $css .= "    background: rgba(255, 255, 255, 0.2);\n";
                    $css .= "    color: #fff;\n";
                    $css .= "}\n";
                    break;

                default: // default (Stripe)
                    $css .= ".cleardeal-wrapper {\n";
                    $css .= "    background: #f6f9fc;\n";
                    $css .= "    border: 1px solid #e3e8ee;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-icon {\n";
                    $css .= "    color: #635bff;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-label {\n";
                    $css .= "    color: #697386;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-price {\n";
                    $css .= "    color: #1a1f36;\n";
                    $css .= "}\n\n";
                    $css .= ".cleardeal-percent {\n";
                    $css .= "    background: #fce4ec;\n";
                    $css .= "    color: #c62828;\n";
                    $css .= "}\n";
                    break;
            }
        }

        return $css;
    }

    /**
     * Calculate contrast color (black or white) for text on colored background
     *
     * @param string $hexColor Hex color code
     * @return string
     */
    protected function getContrastColor($hexColor)
    {
        $hex = ltrim($hexColor, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.5 ? '#1a1f36' : '#ffffff';
    }
}
