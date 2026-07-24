<?php
/**
 * ClearDeal - EU Omnibus Directive Compliance
 *
 * Displays the lowest price from a specified period.
 * Compatible with PrestaShop 8.x and 9.x
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License 3.0 (AFL-3.0)
 * @version   1.0.0
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to kontakt@marcingajewski.pl
 * so we can send you a copy immediately.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/ClearDealPriceHistory.php';

class ClearDeal extends Module
{
    public function __construct()
    {
        $this->name = 'cleardeal';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'marcingajewski.pl';
        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => '9.99.99'];
        $this->php_version_compliancy = ['min' => '8.1.0'];

        parent::__construct();

        $this->displayName = $this->l('ClearDeal - Lowest Price');
        $this->description = $this->l('Ensures compliance with the EU Omnibus Directive by displaying the lowest price from a specified period.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall? All price history logs will be deleted.');
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->installTabs()
            && $this->registerHooks()
            && $this->setDefaultConfig();
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallTabs()
            && $this->uninstallDb()
            && $this->deleteConfig();
    }

    private function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'cleardeal_price_history` (
            `id_history` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product` INT(11) UNSIGNED NOT NULL,
            `id_product_attribute` INT(11) UNSIGNED NOT NULL DEFAULT 0,
            `price_tax_incl` DECIMAL(20, 6) NOT NULL,
            `price_tax_excl` DECIMAL(20, 6) NOT NULL,
            `id_shop` INT(11) UNSIGNED NOT NULL,
            `id_currency` INT(11) UNSIGNED NOT NULL,
            `id_country` INT(11) UNSIGNED NOT NULL,
            `id_group` INT(11) UNSIGNED NOT NULL,
            `captured_at` DATETIME NOT NULL,
            PRIMARY KEY (`id_history`),
            INDEX `idx_product` (`id_product`, `id_product_attribute`),
            INDEX `idx_captured_at` (`captured_at`),
            INDEX `idx_lookup` (`id_product`, `id_product_attribute`, `id_shop`, `id_currency`, `captured_at`),
            INDEX `idx_lowest_price` (`id_product`, `id_product_attribute`, `id_shop`, `id_currency`, `id_country`, `id_group`, `captured_at`),
            INDEX `idx_dedup` (`id_product`, `id_product_attribute`, `id_shop`, `id_currency`, `id_country`, `id_group`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql);
    }

    /**
     * Upgrade database indexes for better performance
     */
    public function upgradeIndexes()
    {
        $db = Db::getInstance();
        $table = _DB_PREFIX_ . 'cleardeal_price_history';

        // Check and add idx_lowest_price if not exists
        $indexes = $db->executeS('SHOW INDEX FROM `' . $table . '` WHERE Key_name = "idx_lowest_price"');
        if (empty($indexes)) {
            $db->execute('ALTER TABLE `' . $table . '` ADD INDEX `idx_lowest_price` (`id_product`, `id_product_attribute`, `id_shop`, `id_currency`, `id_country`, `id_group`, `captured_at`)');
        }

        // Check and add idx_dedup if not exists
        $indexes = $db->executeS('SHOW INDEX FROM `' . $table . '` WHERE Key_name = "idx_dedup"');
        if (empty($indexes)) {
            $db->execute('ALTER TABLE `' . $table . '` ADD INDEX `idx_dedup` (`id_product`, `id_product_attribute`, `id_shop`, `id_currency`, `id_country`, `id_group`)');
        }

        return true;
    }

    private function uninstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'cleardeal_price_history`');
    }

    private function installTabs()
    {
        // Step 1: Find or create Secret Sauce parent tab
        $improve_id = (int) Tab::getIdFromClassName('IMPROVE');
        if (!$improve_id) {
            $improve_id = (int) Tab::getIdFromClassName('AdminParentModulesSf');
        }
        if (!$improve_id) {
            $improve_id = 0;
        }

        $secret_sauce_class = 'AdminSecretSauce';
        $secret_sauce_id = (int) Tab::getIdFromClassName($secret_sauce_class);

        // Create Secret Sauce if it doesn't exist
        if (!$secret_sauce_id) {
            $secretSauce = new Tab();
            $secretSauce->active = 1;
            $secretSauce->class_name = $secret_sauce_class;
            $secretSauce->id_parent = $improve_id;
            $secretSauce->module = ''; // Important: No owner!
            if (property_exists($secretSauce, 'icon')) {
                $secretSauce->icon = 'science';
            }
            foreach (Language::getLanguages(false) as $lang) {
                $secretSauce->name[(int) $lang['id_lang']] = 'Secret Sauce';
            }
            if (!$secretSauce->add()) {
                return false;
            }
            $secret_sauce_id = (int) $secretSauce->id;
        }

        // Step 2: Add ClearDeal tab under Secret Sauce
        $existing_id = Tab::getIdFromClassName('AdminClearDeal');
        if ($existing_id) {
            $tab = new Tab($existing_id);
            $tab->id_parent = $secret_sauce_id;
            $tab->active = 1;
            $tab->save();
        } else {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminClearDeal';
            $tab->module = $this->name;
            $tab->id_parent = $secret_sauce_id;
            if (property_exists($tab, 'icon')) {
                $tab->icon = 'price_check';
            }
            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[(int) $lang['id_lang']] = 'ClearDeal';
            }
            if (!$tab->add()) {
                return false;
            }
        }

        return true;
    }

    private function uninstallTabs()
    {
        // Remove ClearDeal tab
        $id_tab = (int) Tab::getIdFromClassName('AdminClearDeal');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        // Remove Secret Sauce only if it has no other children
        $id_secret_sauce = (int) Tab::getIdFromClassName('AdminSecretSauce');
        if ($id_secret_sauce) {
            $children = Tab::getTabs(Context::getContext()->language->id, $id_secret_sauce);
            if (empty($children)) {
                $ss = new Tab($id_secret_sauce);
                $ss->delete();
            }
        }

        return true;
    }

    private function registerHooks()
    {
        $hooks = [
            'displayHeader',
            'actionProductAdd',
            'actionProductUpdate',
            'actionObjectSpecificPriceAddAfter',
            'actionObjectSpecificPriceUpdateAfter',
            'actionObjectSpecificPriceDeleteAfter',
            'displayProductPriceBlock',
            'displayProductAdditionalInfo',
            'displayProductListReviews',
            // Custom Hooks
            'displayClearDealProduct',
            'displayClearDealListing',
            'displayClearDealQuickView',
            // Creative Elements Hook
            'actionCreativeElementsInit',
        ];

        return $this->registerHook($hooks);
    }

    private function setDefaultConfig()
    {
        $languages = Language::getLanguages(false);

        $defaults = [
            'CLEARDEAL_DAYS' => 30,
            'CLEARDEAL_DISPLAY_MODE' => 'on-discount',
            'CLEARDEAL_PRICE_TYPE' => 'gross',
            'CLEARDEAL_SHOW_PERCENT' => 1,
            'CLEARDEAL_PRECISION' => 0,
            'CLEARDEAL_SHOW_ICON' => 1,
            'CLEARDEAL_ICON_POSITION' => 'start',
            'CLEARDEAL_ICON_TYPE' => 'bootstrap',
            'CLEARDEAL_ICON_CLASS' => 'info-circle',
            'CLEARDEAL_ICON_IMAGE' => '',
            'CLEARDEAL_LOGGING_MODE' => 'immediate',
            'CLEARDEAL_LOG_RETENTION' => 365,
            'CLEARDEAL_EXCLUDED_CATEGORIES' => '[]',
            'CLEARDEAL_SHOW_CHART' => 0,
            'CLEARDEAL_CHART_TRIGGER' => 'icon',
        ];

        foreach ($defaults as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        // Multilingual defaults
        $label_product = [];
        $label_listing = [];
        $label_quickview = [];
        $tooltip = [];

        foreach ($languages as $lang) {
            $id = (int) $lang['id_lang'];
            $label_product[$id] = $this->l('Lowest price in last %s days:');
            $label_listing[$id] = $this->l('From %s days:');
            $label_quickview[$id] = $this->l('Lowest price (%s days):');
            $tooltip[$id] = $this->l('This is the lowest price of this product in the last %s days before the current promotion, in accordance with the EU Omnibus Directive.');
        }

        Configuration::updateValue('CLEARDEAL_LABEL_PRODUCT', $label_product);
        Configuration::updateValue('CLEARDEAL_LABEL_LISTING', $label_listing);
        Configuration::updateValue('CLEARDEAL_LABEL_QUICKVIEW', $label_quickview);
        Configuration::updateValue('CLEARDEAL_ICON_TOOLTIP', $tooltip);

        return true;
    }

    private function deleteConfig()
    {
        $keys = [
            'CLEARDEAL_DAYS',
            'CLEARDEAL_DISPLAY_MODE',
            'CLEARDEAL_PRICE_TYPE',
            'CLEARDEAL_SHOW_PERCENT',
            'CLEARDEAL_PRECISION',
            'CLEARDEAL_SHOW_ICON',
            'CLEARDEAL_ICON_POSITION',
            'CLEARDEAL_ICON_TYPE',
            'CLEARDEAL_ICON_CLASS',
            'CLEARDEAL_ICON_IMAGE',
            'CLEARDEAL_ICON_TOOLTIP',
            'CLEARDEAL_LABEL_PRODUCT',
            'CLEARDEAL_LABEL_LISTING',
            'CLEARDEAL_LABEL_QUICKVIEW',
            'CLEARDEAL_LOGGING_MODE',
            'CLEARDEAL_LOG_RETENTION',
            'CLEARDEAL_EXCLUDED_CATEGORIES',
            'CLEARDEAL_SHOW_CHART',
            'CLEARDEAL_CHART_TRIGGER',
            'CLEARDEAL_STATS_CACHE',
            'CLEARDEAL_STATS_CACHE_TIME',
            'CLEARDEAL_LAST_CLEANUP',
        ];

        foreach ($keys as $key) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    // =========================================================================
    // HOOKS - Creative Elements
    // =========================================================================

    public function hookActionCreativeElementsInit()
    {
        if (!file_exists(__DIR__ . '/classes/WidgetClearDeal.php')) {
            return;
        }

        require_once __DIR__ . '/classes/WidgetClearDeal.php';

        $categories = \CE\Plugin::instance()->elements_manager->getCategories();
        if (!isset($categories['secretsauce'])) {
            \CE\Plugin::instance()->elements_manager->addCategory(
                'secretsauce',
                [
                    'title' => 'Secret Sauce',
                    'icon' => 'eicon-flask',
                ]
            );
        }

        \CE\Plugin::instance()->widgets_manager->registerWidgetType(
            new \ClearDeal\Widget\WidgetClearDeal()
        );
    }

    // =========================================================================
    // HOOKS - Frontend Display
    // =========================================================================

    public function hookDisplayHeader()
    {
        // Add Bootstrap Icons CSS
        $this->context->controller->registerStylesheet(
            'bootstrap-icons',
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
            ['server' => 'remote', 'priority' => 80]
        );

        // Add base CSS
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');

        // Add chart dependencies if enabled
        if (Configuration::get('CLEARDEAL_SHOW_CHART')) {
            // Chart.js from CDN
            $this->context->controller->registerJavascript(
                'chartjs',
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
                ['server' => 'remote', 'position' => 'bottom', 'priority' => 80]
            );

            // Our frontend JS
            $this->context->controller->addJS($this->_path . 'views/js/front.js');

            // Pass AJAX URL and config to frontend
            Media::addJsDef([
                'cleardeal_ajax_url' => $this->context->link->getModuleLink($this->name, 'ajax'),
                'cleardeal_chart_labels' => [
                    'title' => $this->l('Price History'),
                    'price_label' => $this->l('Price'),
                    'current_price' => $this->l('Current price'),
                    'lowest_price' => $this->l('Lowest price in %s days'),
                    'close' => $this->l('Close'),
                    'no_data' => $this->l('No price history data available'),
                    'loading' => $this->l('Loading...'),
                ],
                'cleardeal_chart_colors' => [
                    'line' => Configuration::get('CLEARDEAL_CHART_LINE_COLOR') ?: '#635bff',
                    'fill' => Configuration::get('CLEARDEAL_CHART_FILL_COLOR') ?: '#635bff',
                ],
            ]);
        }

        // Generate and return dynamic CSS based on appearance settings
        $dynamicCss = $this->generateFrontendCss();
        $output = '';

        if (!empty($dynamicCss)) {
            $output .= '<style type="text/css">' . $dynamicCss . '</style>';
        }


        return $output;
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if (!isset($params['type']) || $params['type'] !== 'after_price') {
            return '';
        }

        return $this->renderClearDealBlock($params, 'product');
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->renderClearDealBlock($params, 'product');
    }

    public function hookDisplayProductListReviews($params)
    {
        return $this->renderClearDealBlock($params, 'listing');
    }

    public function hookDisplayClearDealProduct($params)
    {
        return $this->renderClearDealBlock($params, 'product');
    }

    public function hookDisplayClearDealListing($params)
    {
        return $this->renderClearDealBlock($params, 'listing');
    }

    public function hookDisplayClearDealQuickView($params)
    {
        return $this->renderClearDealBlock($params, 'quickview');
    }

    // =========================================================================
    // HOOKS - Price Logging
    // =========================================================================

    public function hookActionProductAdd($params)
    {
        $this->logPriceChange($params['id_product'] ?? 0);
    }

    public function hookActionProductUpdate($params)
    {
        $this->logPriceChange($params['id_product'] ?? 0);
    }

    public function hookActionObjectSpecificPriceAddAfter($params)
    {
        if (isset($params['object']->id_product)) {
            $this->logPriceChange($params['object']->id_product);
        }
    }

    public function hookActionObjectSpecificPriceUpdateAfter($params)
    {
        if (isset($params['object']->id_product)) {
            $this->logPriceChange($params['object']->id_product);
        }
    }

    public function hookActionObjectSpecificPriceDeleteAfter($params)
    {
        if (isset($params['object']->id_product)) {
            $this->logPriceChange($params['object']->id_product);
        }
    }

    protected function logPriceChange($id_product)
    {
        if (!$id_product) {
            return;
        }

        $logging_mode = Configuration::get('CLEARDEAL_LOGGING_MODE');
        if ($logging_mode !== 'immediate') {
            return;
        }

        // Check if product is in excluded category
        if (self::isProductExcluded($id_product)) {
            return;
        }

        ClearDealPriceHistory::addPriceChange($id_product);

        // Lazy cleanup - run once per day
        $this->maybeCleanOldLogs();
    }

    /**
     * Perform lazy cleanup of old logs (once per day)
     */
    protected function maybeCleanOldLogs()
    {
        $last_cleanup = (int) Configuration::get('CLEARDEAL_LAST_CLEANUP');
        $now = time();

        // Run cleanup only once per day (86400 seconds)
        if ($last_cleanup && ($now - $last_cleanup) < 86400) {
            return;
        }

        // Update timestamp first to prevent multiple cleanups
        Configuration::updateValue('CLEARDEAL_LAST_CLEANUP', $now);

        // Perform cleanup
        $retention_days = (int) Configuration::get('CLEARDEAL_LOG_RETENTION') ?: 365;
        ClearDealPriceHistory::cleanOldLogs($retention_days);
    }

    // =========================================================================
    // RENDERING
    // =========================================================================

    public function renderClearDealBlock($params, $hook_type = 'product', $overrides = [])
    {
        $product = $params['product'] ?? null;

        // Fallback: Context Controller (Product Page)
        if (!$product && $this->context->controller instanceof ProductController) {
            $product = $this->context->controller->getProduct();
        }

        // Fallback: Smarty Variable
        if (!$product && isset($this->context->smarty->tpl_vars['product'])) {
            $product = $this->context->smarty->tpl_vars['product']->value;
        }

        if (!$product) {
            return '';
        }

        // Don't cast LazyArray to array
        if (is_object($product) && !($product instanceof \ArrayAccess)) {
            $product = (array) $product;
        }

        $data = $this->getClearDealData($product, $hook_type, $overrides);

        if (!$data) {
            return '';
        }

        $tooltipBg = Configuration::get('CLEARDEAL_TOOLTIP_BG') ?: '#1a1f36';
        $tooltipTextColor = $this->getContrastColor($tooltipBg);

        $this->context->smarty->assign([
            'cleardeal_data' => $data,
            'cleardeal_show_icon' => (bool) Configuration::get('CLEARDEAL_SHOW_ICON'),
            'cleardeal_icon_position' => Configuration::get('CLEARDEAL_ICON_POSITION') ?: 'start',
            'cleardeal_icon_type' => Configuration::get('CLEARDEAL_ICON_TYPE') ?: 'bootstrap',
            'cleardeal_icon_class' => Configuration::get('CLEARDEAL_ICON_CLASS') ?: 'info-circle',
            'cleardeal_icon_image' => Configuration::get('CLEARDEAL_ICON_IMAGE'),
            'cleardeal_tooltip' => $this->getTooltipText(),
            'cleardeal_tooltip_bg' => $tooltipBg,
            'cleardeal_tooltip_color' => $tooltipTextColor,
            'cleardeal_module_dir' => $this->_path,
            'cleardeal_show_chart' => (bool) Configuration::get('CLEARDEAL_SHOW_CHART'),
            'cleardeal_chart_trigger' => Configuration::get('CLEARDEAL_CHART_TRIGGER') ?: 'icon',
        ]);

        return $this->display(__FILE__, 'views/templates/hook/cleardeal_price.tpl');
    }

    public function getClearDealData($product, $hook_type, $overrides = [])
    {
        if (empty($product)) {
            return null;
        }

        // Get product ID
        $id_product = 0;
        if (isset($product['id_product'])) {
            $id_product = (int) $product['id_product'];
        } elseif (isset($product['id'])) {
            $id_product = (int) $product['id'];
        }

        if (!$id_product) {
            return null;
        }

        // Check if product is in excluded category
        if (self::isProductExcluded($id_product)) {
            return null;
        }

        // Get attribute ID
        $id_pa = 0;
        if ($hook_type === 'product' || $hook_type === 'quickview') {
            $id_pa = (int) Tools::getValue('id_product_attribute');
            if (!$id_pa && isset($product['id_product_attribute'])) {
                $id_pa = (int) $product['id_product_attribute'];
            }
            if (!$id_pa) {
                $id_pa = (int) Product::getDefaultAttribute($id_product);
            }
        } else {
            $id_pa = (int) ($product['id_product_attribute'] ?? 0);
        }

        // Configuration
        $price_type = $overrides['price_type'] ?? Configuration::get('CLEARDEAL_PRICE_TYPE') ?: 'gross';
        $days = (int) ($overrides['days'] ?? Configuration::get('CLEARDEAL_DAYS') ?: 30);
        $display_mode = $overrides['display_mode'] ?? Configuration::get('CLEARDEAL_DISPLAY_MODE') ?: 'on-discount';
        $show_percent = isset($overrides['show_percent']) ? $overrides['show_percent'] : (bool) Configuration::get('CLEARDEAL_SHOW_PERCENT');
        $precision = (int) Configuration::get('CLEARDEAL_PRECISION');

        // Get prices
        $use_tax = ($price_type !== 'net');
        $current = Product::getPriceStatic($id_product, $use_tax, $id_pa, 6, null, false, true);
        $regular = Product::getPriceStatic($id_product, $use_tax, $id_pa, 6, null, false, false);

        // Check if discounted
        $epsilon = 0.00001;
        $is_discounted = ($current < ($regular - $epsilon));

        // Display mode check
        if ($display_mode === 'on-discount' && !$is_discounted) {
            return null;
        }

        // Get lowest price from history
        $lowest = ClearDealPriceHistory::getLowestPrice($id_product, $id_pa, $days, $price_type);

        if (!$lowest || $lowest <= 0) {
            $lowest = $current;
        }

        // Calculate percentage change
        $percent = null;
        if ($show_percent) {
            $diff = $current - $lowest;
            if (abs($diff) > $epsilon && $lowest > 0) {
                $val = ($diff / $lowest) * 100;
                $percent = round($val, $precision);
            }
        }

        // Format price
        $low_disp = Tools::convertPrice($lowest, $this->context->currency);
        $formatted = $this->context->currentLocale->formatPrice($low_disp, $this->context->currency->iso_code);

        // Get label
        $label = $overrides['label'] ?? $this->getLabelForHook($hook_type);
        if (strpos($label, '%s') !== false) {
            $label = sprintf($label, $days);
        }

        return [
            'label' => $label,
            'lowest_price_formatted' => $formatted,
            'lowest_price_raw' => $lowest,
            'percentage' => $percent,
            'hook_type' => $hook_type,
            'is_negative' => ($percent !== null && $percent < 0),
            'id_product' => $id_product,
            'id_product_attribute' => $id_pa,
        ];
    }

    private function getLabelForHook($hook_type)
    {
        $id_lang = $this->context->language->id;

        switch ($hook_type) {
            case 'listing':
                return Configuration::get('CLEARDEAL_LABEL_LISTING', $id_lang) ?: $this->l('From %s days:');
            case 'quickview':
                return Configuration::get('CLEARDEAL_LABEL_QUICKVIEW', $id_lang) ?: $this->l('Lowest price (%s days):');
            default:
                return Configuration::get('CLEARDEAL_LABEL_PRODUCT', $id_lang) ?: $this->l('Lowest price in last %s days:');
        }
    }

    private function getTooltipText()
    {
        $id_lang = $this->context->language->id;
        $days = Configuration::get('CLEARDEAL_DAYS') ?: 30;
        $tooltip = Configuration::get('CLEARDEAL_ICON_TOOLTIP', $id_lang);

        if ($tooltip && strpos($tooltip, '%s') !== false) {
            $tooltip = sprintf($tooltip, $days);
        }

        return $tooltip;
    }

    // =========================================================================
    // ADMIN CONFIGURATION
    // =========================================================================

    public function getContent()
    {
        // Redirect to our custom admin controller
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminClearDeal')
        );
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function getConfigValue($key, $default = null)
    {
        $value = Configuration::get($key);
        return ($value === false && $default !== null) ? $default : $value;
    }

    /**
     * Check if a product is in an excluded category
     *
     * @param int $id_product Product ID
     * @return bool True if product should be excluded
     */
    public static function isProductExcluded($id_product)
    {
        $excluded = Configuration::get('CLEARDEAL_EXCLUDED_CATEGORIES');
        if (!$excluded || $excluded === '[]') {
            return false;
        }

        $excluded_ids = json_decode($excluded, true);
        if (empty($excluded_ids) || !is_array($excluded_ids)) {
            return false;
        }

        // Get product categories
        $product_categories = Product::getProductCategories($id_product);

        // Check if any product category is in excluded list
        foreach ($product_categories as $cat_id) {
            if (in_array((int) $cat_id, $excluded_ids)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get excluded categories with names
     *
     * @return array Array of excluded categories with id and name
     */
    public static function getExcludedCategoriesWithNames()
    {
        $excluded = Configuration::get('CLEARDEAL_EXCLUDED_CATEGORIES');
        if (!$excluded || $excluded === '[]') {
            return [];
        }

        $excluded_ids = json_decode($excluded, true);
        if (empty($excluded_ids) || !is_array($excluded_ids)) {
            return [];
        }

        $id_lang = Context::getContext()->language->id;
        $result = [];

        foreach ($excluded_ids as $id_category) {
            $category = new Category($id_category, $id_lang);
            if (Validate::isLoadedObject($category)) {
                $result[] = [
                    'id' => (int) $id_category,
                    'name' => $category->name,
                ];
            }
        }

        return $result;
    }

    // =========================================================================
    // FRONTEND CSS GENERATION
    // =========================================================================

    /**
     * Generate dynamic CSS based on appearance settings
     *
     * @return string CSS code
     */
    protected function generateFrontendCss()
    {
        $preset = Configuration::get('CLEARDEAL_STYLE_PRESET') ?: 'default';
        $useCustomColors = (bool) Configuration::get('CLEARDEAL_USE_CUSTOM_COLORS');
        $customCss = Configuration::get('CLEARDEAL_CUSTOM_CSS') ?: '';

        // Default preset colors
        $colorBackground = Configuration::get('CLEARDEAL_COLOR_BACKGROUND') ?: '#f6f9fc';
        $colorBorder = Configuration::get('CLEARDEAL_COLOR_BORDER') ?: '#e3e8ee';
        $colorPrimary = Configuration::get('CLEARDEAL_COLOR_PRIMARY') ?: '#635bff';
        $colorBadge = Configuration::get('CLEARDEAL_COLOR_BADGE') ?: '#fce4ec';
        $useGradient = (bool) Configuration::get('CLEARDEAL_USE_GRADIENT');
        $colorGradientEnd = Configuration::get('CLEARDEAL_COLOR_GRADIENT_END') ?: '#e0e5eb';

        // Minimal preset colors
        $colorMinimalIcon = Configuration::get('CLEARDEAL_COLOR_MINIMAL_ICON') ?: '#9ca3af';
        $colorMinimalLabel = Configuration::get('CLEARDEAL_COLOR_MINIMAL_LABEL') ?: '#6b7280';
        $colorMinimalPrice = Configuration::get('CLEARDEAL_COLOR_MINIMAL_PRICE') ?: '#1f2937';
        $colorMinimalBadge = Configuration::get('CLEARDEAL_COLOR_MINIMAL_BADGE') ?: '#dc2626';

        // Bold preset colors
        $colorBoldStart = Configuration::get('CLEARDEAL_COLOR_BOLD_START') ?: '#6366f1';
        $colorBoldEnd = Configuration::get('CLEARDEAL_COLOR_BOLD_END') ?: '#a855f7';
        $colorBoldBadge = Configuration::get('CLEARDEAL_COLOR_BOLD_BADGE') ?: '#fbbf24';

        $css = '';

        switch ($preset) {
            case 'minimal':
                // Minimal: transparent background, no border - text only
                $css .= ".cleardeal-content {\n";
                $css .= "    background: transparent !important;\n";
                $css .= "    border: none !important;\n";
                $css .= "    padding: 4px 0 !important;\n";
                $css .= "}\n";

                if ($useCustomColors) {
                    $css .= ".cleardeal-icon i {\n";
                    $css .= "    color: {$colorMinimalIcon} !important;\n";
                    $css .= "}\n";
                    $css .= ".cleardeal-label {\n";
                    $css .= "    color: {$colorMinimalLabel} !important;\n";
                    $css .= "}\n";
                    $css .= ".cleardeal-price {\n";
                    $css .= "    color: {$colorMinimalPrice} !important;\n";
                    $css .= "}\n";
                    $css .= ".cleardeal-percent-negative,\n";
                    $css .= ".cleardeal-percent-positive {\n";
                    $css .= "    background: transparent !important;\n";
                    $css .= "    color: {$colorMinimalBadge} !important;\n";
                    $css .= "}\n";
                } else {
                    $css .= ".cleardeal-icon i {\n";
                    $css .= "    color: #9ca3af !important;\n";
                    $css .= "}\n";
                    $css .= ".cleardeal-label {\n";
                    $css .= "    color: #6b7280 !important;\n";
                    $css .= "}\n";
                    $css .= ".cleardeal-price {\n";
                    $css .= "    color: #1f2937 !important;\n";
                    $css .= "}\n";
                    $css .= ".cleardeal-percent-negative {\n";
                    $css .= "    background: transparent !important;\n";
                    $css .= "    color: #dc2626 !important;\n";
                    $css .= "}\n";
                    $css .= ".cleardeal-percent-positive {\n";
                    $css .= "    background: transparent !important;\n";
                    $css .= "    color: #059669 !important;\n";
                    $css .= "}\n";
                }
                break;

            case 'bold':
                // Bold: gradient background, white text
                $gradientStart = $useCustomColors ? $colorBoldStart : '#6366f1';
                $gradientEnd = $useCustomColors ? $colorBoldEnd : '#a855f7';
                $badgeColor = $useCustomColors ? $colorBoldBadge : '#fbbf24';

                $css .= ".cleardeal-content {\n";
                $css .= "    background: linear-gradient(135deg, {$gradientStart} 0%, {$gradientEnd} 100%) !important;\n";
                $css .= "    border: none !important;\n";
                $css .= "}\n";
                $css .= ".cleardeal-icon i {\n";
                $css .= "    color: rgba(255, 255, 255, 0.9) !important;\n";
                $css .= "}\n";
                $css .= ".cleardeal-label {\n";
                $css .= "    color: rgba(255, 255, 255, 0.8) !important;\n";
                $css .= "}\n";
                $css .= ".cleardeal-price {\n";
                $css .= "    color: #fff !important;\n";
                $css .= "}\n";
                $css .= ".cleardeal-percent-negative,\n";
                $css .= ".cleardeal-percent-positive {\n";
                $css .= "    background: {$badgeColor} !important;\n";
                $css .= "    color: " . $this->getContrastColor($badgeColor) . " !important;\n";
                $css .= "}\n";
                break;

            default:
                // Default: background + border
                if ($useCustomColors) {
                    if ($useGradient) {
                        $css .= ".cleardeal-content {\n";
                        $css .= "    background: linear-gradient(135deg, {$colorBackground} 0%, {$colorGradientEnd} 100%) !important;\n";
                        $css .= "    border-color: {$colorBorder} !important;\n";
                        $css .= "}\n";
                    } else {
                        $css .= ".cleardeal-content {\n";
                        $css .= "    background: {$colorBackground} !important;\n";
                        $css .= "    border-color: {$colorBorder} !important;\n";
                        $css .= "}\n";
                    }
                    $css .= ".cleardeal-icon i {\n";
                    $css .= "    color: {$colorPrimary} !important;\n";
                    $css .= "}\n";
                    $css .= ".cleardeal-percent-negative {\n";
                    $css .= "    background: {$colorBadge} !important;\n";
                    $css .= "    color: " . $this->getContrastColor($colorBadge) . " !important;\n";
                    $css .= "}\n";
                }
                break;
        }

        // Modal header styling - uses dedicated color setting
        $modalHeaderColor = Configuration::get('CLEARDEAL_MODAL_HEADER_COLOR') ?: '#635bff';
        $modalHeaderGradientEnd = $this->adjustColor($modalHeaderColor, 30);
        $modalTextColor = $this->getContrastColor($modalHeaderColor);
        $isLightText = ($modalTextColor === '#ffffff');

        $css .= ".cleardeal-modal-header {\n";
        $css .= "    background: linear-gradient(135deg, {$modalHeaderColor} 0%, {$modalHeaderGradientEnd} 100%) !important;\n";
        $css .= "}\n";
        $css .= ".cleardeal-modal-header,\n";
        $css .= ".cleardeal-modal-title {\n";
        $css .= "    color: {$modalTextColor} !important;\n";
        $css .= "}\n";
        $css .= ".cleardeal-modal-icon {\n";
        $css .= "    background: " . ($isLightText ? 'rgba(255, 255, 255, 0.2)' : 'rgba(0, 0, 0, 0.1)') . " !important;\n";
        $css .= "}\n";
        $css .= ".cleardeal-modal-icon svg {\n";
        $css .= "    color: {$modalTextColor} !important;\n";
        $css .= "}\n";
        $css .= ".cleardeal-modal-close {\n";
        $css .= "    background: " . ($isLightText ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.1)') . " !important;\n";
        $css .= "    color: {$modalTextColor} !important;\n";
        $css .= "}\n";
        $css .= ".cleardeal-modal-close:hover {\n";
        $css .= "    background: " . ($isLightText ? 'rgba(255, 255, 255, 0.25)' : 'rgba(0, 0, 0, 0.15)') . " !important;\n";
        $css .= "}\n";

        // Add tooltip styling
        $tooltipBg = Configuration::get('CLEARDEAL_TOOLTIP_BG') ?: '#1a1f36';
        $tooltipTextColor = $this->getContrastColor($tooltipBg);

        $css .= ".cleardeal-tooltip,\n";
        $css .= ".cleardeal-widget-tooltip {\n";
        $css .= "    background: {$tooltipBg};\n";
        $css .= "    color: {$tooltipTextColor};\n";
        $css .= "    opacity: 1;\n";
        $css .= "}\n";
        $css .= ".cleardeal-icon:hover .cleardeal-tooltip,\n";
        $css .= ".cleardeal-widget-icon:hover .cleardeal-widget-tooltip {\n";
        $css .= "    visibility: visible;\n";
        $css .= "}\n";
        $css .= ".cleardeal-tooltip::after,\n";
        $css .= ".cleardeal-widget-tooltip::after {\n";
        $css .= "    border-top-color: {$tooltipBg};\n";
        $css .= "}\n";

        // Add custom CSS if provided
        if (!empty($customCss)) {
            $css .= "\n/* Custom CSS */\n" . $customCss;
        }

        return $css;
    }

    /**
     * Adjust color brightness
     *
     * @param string $hexColor Color in hex format
     * @param int $percent Positive to lighten, negative to darken
     * @return string Adjusted color in hex format
     */
    protected function adjustColor($hexColor, $percent)
    {
        $hex = ltrim($hexColor, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + ($r * $percent / 100)));
        $g = max(0, min(255, $g + ($g * $percent / 100)));
        $b = max(0, min(255, $b + ($b * $percent / 100)));

        return sprintf('#%02x%02x%02x', (int) $r, (int) $g, (int) $b);
    }

    /**
     * Calculate contrast color for text on given background
     *
     * @param string $hexColor Background color in hex format
     * @return string Dark or light text color
     */
    protected function getContrastColor($hexColor)
    {
        $hex = ltrim($hexColor, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate relative luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.5 ? '#1a1f36' : '#ffffff';
    }
}
