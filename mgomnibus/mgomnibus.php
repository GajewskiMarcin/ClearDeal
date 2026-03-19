<?php
/**
 * 2025 - marcingajewski.pl
 *
 * Omnibus Directive - Log and display the lowest price.
 * Version 2.0.0
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://marcingajewski.pl
 * @version   2.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/MgOmnibusPriceHistory.php';

class MgOmnibus extends Module
{
    public function __construct()
    {
        $this->name = 'mgomnibus';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'marcingajewski.pl';
        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->php_version_compliancy = ['min' => '8.1.0'];

        parent::__construct();

        $this->displayName = $this->l('MG Omnibus - Lowest Price');
        $this->description = $this->l('Ensures compliance with the EU Omnibus Directive.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall? Logs will be deleted.');
    }

    public function install()
    {
        return parent::install() 
            && $this->installDb() 
            && $this->installTabs()
            && $this->registerHooks();
    }

    public function uninstall()
    {
        return parent::uninstall() 
            && $this->uninstallTabs()
            && $this->uninstallDb();
    }

    private function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mgomnibus_price_history` (
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
            INDEX `idx_captured_at` (`captured_at`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
        return Db::getInstance()->execute($sql);
    }

    private function uninstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'mgomnibus_price_history`');
    }

    private function installTabs()
    {
        // Parent Tab: MG Extension
        $id_parent = (int)Tab::getIdFromClassName('AdminMgExtension');
        if (!$id_parent) {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->class_name = 'AdminMgExtension';
            $parentTab->name = [];
            foreach (Language::getLanguages(true) as $lang) {
                $parentTab->name[$lang['id_lang']] = 'MG Extension';
            }
            // Place under IMPROVE tab
            $parentTab->id_parent = (int)Tab::getIdFromClassName('IMPROVE');
            $parentTab->module = $this->name;
            $parentTab->icon = 'extension'; 
            $parentTab->add();
            $id_parent = $parentTab->id;
        }

        // Child Tab 1: Settings (Links to Module Config)
        $id_settings = (int)Tab::getIdFromClassName('AdminMgOmnibusSettings');
        if (!$id_settings) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminMgOmnibusSettings';
            $tab->name = [];
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = 'MG Omnibus Settings';
            }
            $tab->id_parent = $id_parent;
            $tab->module = $this->name;
            $tab->add();
        }

        // Child Tab 2: MG Omnibus Logs
        $id_logs = (int)Tab::getIdFromClassName('AdminMgOmnibusLogs');
        if (!$id_logs) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminMgOmnibusLogs';
            $tab->name = [];
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = 'MG Omnibus Logs';
            }
            $tab->id_parent = $id_parent;
            $tab->module = $this->name;
            $tab->add();
        }

        return true;
    }

    private function uninstallTabs()
    {
        $tabs = ['AdminMgOmnibusLogs', 'AdminMgOmnibusSettings', 'AdminMgExtension'];
        foreach ($tabs as $className) {
            $id_tab = (int)Tab::getIdFromClassName($className);
            if ($id_tab) {
                $tab = new Tab($id_tab);
                $tab->delete();
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
            'displayMgOmnibusProduct',
            'displayMgOmnibusListing',
            'displayMgOmnibusQuickView',
            // Creative Elements Hook
            'actionCreativeElementsInit'
        ];

        return $this->registerHook($hooks);
    }

    public function hookActionCreativeElementsInit()
    {
        require_once __DIR__ . '/classes/WidgetOmnibus.php';
        
        $categories = \CE\Plugin::instance()->elements_manager->getCategories();
        if (!isset($categories['mg_extensions'])) {
            \CE\Plugin::instance()->elements_manager->addCategory(
                'mg_extensions',
                [
                    'title' => 'MG Extensions',
                    'icon' => 'fa fa-plug',
                ]
            );
        }

        \CE\Plugin::instance()->widgets_manager->registerWidgetType(new \MgOmnibus\Widget\WidgetOmnibus());
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if ($params['type'] !== 'after_price') {
            return;
        }
        return $this->renderOmnibusBlock($params, 'product');
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->renderOmnibusBlock($params, 'product');
    }

    public function hookDisplayProductListReviews($params)
    {
        return $this->renderOmnibusBlock($params, 'listing');
    }

    public function hookDisplayMgOmnibusProduct($params)
    {
        return $this->renderOmnibusBlock($params, 'product');
    }

    public function hookDisplayMgOmnibusListing($params)
    {
        return $this->renderOmnibusBlock($params, 'listing');
    }
    public function hookDisplayMgOmnibusQuickView($params)
    {
        return $this->renderOmnibusBlock($params, 'quickview');
    }

    public function renderOmnibusBlock($params, $hook_type = 'product')
    {


        // 1. Try to get product from params
        $product = $params['product'] ?? null;

        // 2. Fallback: Context Controller (Product Page)
        if (!$product && $this->context->controller instanceof ProductController) {
            $product = $this->context->controller->getProduct();
        }

        // 3. Fallback: Smarty Variable
        if (!$product && isset($this->context->smarty->tpl_vars['product'])) {
            $product = $this->context->smarty->tpl_vars['product']->value;
        }

        if (!$product) {
            return '';
        }

        // Fix: Don't cast to array if it implements ArrayAccess (like ProductListingLazyArray)
        // Casting LazyArray to array exposes protected internal properties instead of data.
        if (is_object($product) && !($product instanceof \ArrayAccess)) {
            $product = (array)$product;
        }

        $data = $this->getOmnibusData($product, $hook_type);
        
        if (!$data) {
            return '';
        }

        $id_lang = $this->context->language->id;
        $tooltip_text = Configuration::get('OMNIBUS_ICON_TOOLTIP', $id_lang);

        $this->context->smarty->assign([
            'omnibus_data' => $data,
            'omnibus_settings' => $this->getConfigFormValues(), // Pass settings for styling
            'omnibus_tooltip' => $tooltip_text
        ]);

        return $this->display(__FILE__, 'views/templates/hook/omnibus_price.tpl');
    }

    public function getOmnibusData($product, $hook_type, $overrides = [])
    {
        if (empty($product)) return null;



        $id_product = 0;
        if (isset($product['id_product'])) {
            $id_product = (int)$product['id_product'];
        } elseif (isset($product['id'])) {
            $id_product = (int)$product['id'];
        }

        if (!$id_product) return null;
        
        // Improved Attribute Detection
        $id_pa = 0;
        if ($hook_type === 'product' || $hook_type === 'quickview') {
            $id_pa = (int)Tools::getValue('id_product_attribute');
            if (!$id_pa && isset($product['id_product_attribute'])) {
                $id_pa = (int)$product['id_product_attribute'];
            }
            // If still 0, and product has combinations, get default
            if (!$id_pa) {
                $id_pa = (int)Product::getDefaultAttribute($id_product);
            }
        } else {
            $id_pa = (int)($product['id_product_attribute'] ?? 0);
        }

        $pt = $overrides['price_type'] ?? $this->getConfigValue('OMNIBUS_PRICE_TYPE', 'default');
        if ($pt === 'default') $pt = 'gross'; // Default to 'gross' (matches TPL default)
        $days = (int)($overrides['days'] ?? $this->getConfigValue('OMNIBUS_DAYS', 30));

        // Prices
        // Fix: Check for both 'excl' and 'net' to exclude tax
        $use_tax = ($pt !== 'excl' && $pt !== 'net');
        $current = Product::getPriceStatic($id_product, $use_tax, $id_pa, 6, null, false, true);
        $regular = Product::getPriceStatic($id_product, $use_tax, $id_pa, 6, null, false, false);
        
        // Fix: Use epsilon for float comparison
        $epsilon = 0.00001;
        $is_discounted = ($current < ($regular - $epsilon));

        // Lowest price
        $lowest = MgOmnibusPriceHistory::getLowestPrice($id_product, $id_pa, $days, $pt);
        
        // Fix: If lowest is 0 or null, it means no history. 
        // If we have no history, strictly speaking we can't show "lowest in 30 days".
        // But usually we fallback to current price to avoid showing 0.
        if (!$lowest || $lowest <= 0) {
            $lowest = $current;
        }

        // Display Mode
        $mode = $overrides['display_mode'] ?? $this->getConfigValue('OMNIBUS_DISPLAY_MODE', 'default');
        if ($mode === 'default') $mode = 'on-discount'; // Default to 'on-discount' if not set or default


        
        // Fix: Logic for 'on-discount' (matches TPL value)
        if ($mode === 'on-discount' && !$is_discounted) {
            return null;
        }

        // Percentage
        $percent = null;
        $prec = (int)$this->getConfigValue('OMNIBUS_PRECISION', 2);
        $show_positive = (bool)$this->getConfigValue('OMNIBUS_SHOW_POSITIVE_PERCENT', false);
        
        // Percent Mode Logic
        $percent_mode = $overrides['show_percent_mode'] ?? $this->getConfigValue('OMNIBUS_SHOW_PERCENT_MODE', 'default');
        if ($percent_mode === 'default') $percent_mode = 'always'; // Default fallback

        if ($percent_mode !== 'no') {
            $diff = $current - $lowest;
            if (abs($diff) > $epsilon) {
                $val = ($diff / $lowest) * 100;
                $rnd = round($val, $prec);
                
                if ($rnd < 0) {
                    // Always show negative (discount)
                    $percent = $rnd;
                } elseif ($rnd > 0 && $show_positive) {
                    // Show positive only if enabled
                    $percent = '+' . $rnd;
                }
            }
            
            // Filter based on mode
            if ($percent_mode === 'negative_only' && (float)$percent >= 0) {
                $percent = null;
            }
        }

        $low_disp = Tools::convertPrice($lowest, $this->context->currency);
        $formatted = $this->context->currentLocale->formatPrice($low_disp, $this->context->currency->iso_code);
        
        $l_key = 'OMNIBUS_LABEL_PRODUCT';
        if ($hook_type === 'listing') {
            $l_key = 'OMNIBUS_LABEL_LISTING';
        } elseif ($hook_type === 'quickview') {
            $l_key = 'OMNIBUS_LABEL_QUICKVIEW';
        }
        
        $label = $overrides['label'] ?? $this->getContextLangValue($l_key);

        if (strpos($label, '%s') !== false) {
            $label = sprintf($label, $days);
        }

        return [
            'label' => $label,
            'lowest_price_formatted' => $formatted,
            'percentage' => $percent,
            'hook_type' => $hook_type,
            'debug' => [
                'id_product' => $id_product,
                'id_pa' => $id_pa,
                'days' => $days,
                'price_type' => $pt,
                'lowest_raw' => $lowest,
                'current' => $current,
                'regular' => $regular,
                'is_discounted' => $is_discounted,
                'mode' => $mode,

                'sql' => MgOmnibusPriceHistory::$last_sql,
                'default_country' => Configuration::get('PS_COUNTRY_DEFAULT'),
                'settings' => [
                    'OMNIBUS_SHOW_ICON' => $this->getConfigValue('OMNIBUS_SHOW_ICON'),
                    'OMNIBUS_ICON_CLASS' => $this->getConfigValue('OMNIBUS_ICON_CLASS'),
                    'OMNIBUS_ICON_IMAGE' => $this->getConfigValue('OMNIBUS_ICON_IMAGE'),
                    'OMNIBUS_CHART_TRIGGER' => $this->getConfigValue('OMNIBUS_CHART_TRIGGER'),
                    'OMNIBUS_SHOW_ICON_PRODUCT_DESKTOP' => $this->getConfigValue('OMNIBUS_SHOW_ICON_PRODUCT_DESKTOP'),
                    'OMNIBUS_SHOW_ICON_PRODUCT_MOBILE' => $this->getConfigValue('OMNIBUS_SHOW_ICON_PRODUCT_MOBILE'),
                ]
            ]
        ];
    }

    private function getContextLangValue($key, $default = '')
    {
        $id_lang = $this->context->language->id;
        $value = Configuration::get($key, $id_lang);
        if (!$value) $value = $default;
        return $value;
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
        
        $this->context->smarty->assign([
            'omnibus_settings' => $this->getConfigFormValues(),
        ]);
        
        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    public function hookActionProductAdd($params)
    {
        $this->logPriceChange($params['id_product']);
    }

    public function hookActionProductUpdate($params)
    {
        $this->logPriceChange($params['id_product']);
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

        $logging_mode = Configuration::get('OMNIBUS_LOGGING_MODE');
        
        if ($logging_mode !== 'immediate') {
            return;
        }

        MgOmnibusPriceHistory::addPriceChange($id_product);
    }

    public function getConfigValue($key, $default = null)
    {
        $value = Configuration::get($key);
        return ($value === false && $default !== null) ? $default : $value;
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitMgOmnibus')) {
            $output .= $this->postProcess();
        }

        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');

        $this->context->smarty->assign([
            'module_dir' => $this->_path,
            'module_version' => $this->version,
            'settings' => $this->getConfigFormValues(),
            'languages' => Language::getLanguages(false),
            'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
        ]);

        return $output . $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
    }

    protected function postProcess()
    {
        // Handle Reset Settings
        if (Tools::getValue('resetSettings') == '1') {
            $keys_to_delete = array_keys($this->getConfigFormValues());
            foreach ($keys_to_delete as $key) {
                Configuration::deleteByName($key);
            }
            return $this->displayConfirmation($this->l('Settings reset to default values.'));
        }

        // Handle Cron Token Regeneration
        if (Tools::getValue('regenerateCronToken') == '1') {
            Configuration::updateValue('OMNIBUS_CRON_TOKEN', Tools::passwdGen(32));
            return $this->displayConfirmation($this->l('New CRON token generated.'));
        }

        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            if (in_array($key, [
                'OMNIBUS_LABEL_PRODUCT', 
                'OMNIBUS_LABEL_LISTING', 
                'OMNIBUS_LABEL_QUICKVIEW',
                'OMNIBUS_ICON_TOOLTIP'
            ])) {
                $values = [];
                foreach (Language::getLanguages(false) as $lang) {
                    $values[$lang['id_lang']] = Tools::getValue($key . '_' . $lang['id_lang']);
                }
                Configuration::updateValue($key, $values, true);
            } elseif ($key === 'OMNIBUS_ICON_IMAGE') {
                // Skip direct update, handled below
                continue;
            } elseif ($key === 'OMNIBUS_CUSTOM_CSS') {
                Configuration::updateValue($key, Tools::getValue($key), true);
            } elseif ($key === 'OMNIBUS_LOG_SCOPE') {
                $scope = Tools::getValue($key);
                if (is_array($scope)) {
                    Configuration::updateValue($key, json_encode($scope));
                } else {
                    Configuration::updateValue($key, json_encode([]));
                }
            } else {
                // Handle standard and matrix keys
                $value = Tools::getValue($key);
                Configuration::updateValue($key, $value);
            }
        }

        // Handle File Upload
        if (isset($_FILES['OMNIBUS_ICON_IMAGE']) && isset($_FILES['OMNIBUS_ICON_IMAGE']['tmp_name']) && !empty($_FILES['OMNIBUS_ICON_IMAGE']['tmp_name'])) {
            $ext = substr($_FILES['OMNIBUS_ICON_IMAGE']['name'], strrpos($_FILES['OMNIBUS_ICON_IMAGE']['name'], '.') + 1);
            $file_name = md5($_FILES['OMNIBUS_ICON_IMAGE']['name']) . '.' . $ext;
            
            if (!file_exists($this->local_path . 'views/img')) {
                mkdir($this->local_path . 'views/img', 0777, true);
            }

            // Use copy instead of move_uploaded_file to avoid Symfony FileNotFoundException
            // because Symfony might try to access the tmp file later in the request lifecycle.
            if (copy($_FILES['OMNIBUS_ICON_IMAGE']['tmp_name'], $this->local_path . 'views/img/' . $file_name)) {
                Configuration::updateValue('OMNIBUS_ICON_IMAGE', $file_name);
            }
        }
        
        // Handle File Deletion
        if (Tools::getValue('DELETE_OMNIBUS_ICON_IMAGE')) {
            $old_file = Configuration::get('OMNIBUS_ICON_IMAGE');
            if ($old_file && file_exists($this->local_path . 'views/img/' . $old_file)) {
                unlink($this->local_path . 'views/img/' . $old_file);
            }
            Configuration::updateValue('OMNIBUS_ICON_IMAGE', '');
        }

        return $this->displayConfirmation($this->l('Settings updated successfully.'));
    }

    protected function getConfigFormValues()
    {
        $values = [
            'OMNIBUS_DAYS' => Configuration::get('OMNIBUS_DAYS', 30),
            'OMNIBUS_DISPLAY_MODE' => Configuration::get('OMNIBUS_DISPLAY_MODE', 'always'),
            'OMNIBUS_PRICE_TYPE' => Configuration::get('OMNIBUS_PRICE_TYPE', 'gross'),
            'OMNIBUS_SHOW_PERCENT_MODE' => Configuration::get('OMNIBUS_SHOW_PERCENT_MODE', 'always'),
            'OMNIBUS_PRECISION' => Configuration::get('OMNIBUS_PRECISION', 2),
            'OMNIBUS_SHOW_ICON' => Configuration::get('OMNIBUS_SHOW_ICON', true),
            'OMNIBUS_ICON_POSITION' => Configuration::get('OMNIBUS_ICON_POSITION', 'start'),
            'OMNIBUS_ICON_TYPE' => Configuration::get('OMNIBUS_ICON_TYPE', 'file'),
            'OMNIBUS_SHOW_POSITIVE_PERCENT' => Configuration::get('OMNIBUS_SHOW_POSITIVE_PERCENT', false),

            // Percent Change Visibility
            'OMNIBUS_SHOW_PERCENT_PRODUCT_DESKTOP' => Configuration::get('OMNIBUS_SHOW_PERCENT_PRODUCT_DESKTOP', true),
            'OMNIBUS_SHOW_PERCENT_PRODUCT_MOBILE' => Configuration::get('OMNIBUS_SHOW_PERCENT_PRODUCT_MOBILE', true),
            'OMNIBUS_SHOW_PERCENT_LISTING_DESKTOP' => Configuration::get('OMNIBUS_SHOW_PERCENT_LISTING_DESKTOP', false),
            'OMNIBUS_SHOW_PERCENT_LISTING_MOBILE' => Configuration::get('OMNIBUS_SHOW_PERCENT_LISTING_MOBILE', false),
            'OMNIBUS_SHOW_PERCENT_QUICKVIEW_DESKTOP' => Configuration::get('OMNIBUS_SHOW_PERCENT_QUICKVIEW_DESKTOP', false),
            'OMNIBUS_SHOW_PERCENT_QUICKVIEW_MOBILE' => Configuration::get('OMNIBUS_SHOW_PERCENT_QUICKVIEW_MOBILE', false),

            // Icon Visibility
            'OMNIBUS_SHOW_ICON_PRODUCT_DESKTOP' => Configuration::get('OMNIBUS_SHOW_ICON_PRODUCT_DESKTOP', true),
            'OMNIBUS_SHOW_ICON_PRODUCT_MOBILE' => Configuration::get('OMNIBUS_SHOW_ICON_PRODUCT_MOBILE', true),
            'OMNIBUS_SHOW_ICON_LISTING_DESKTOP' => Configuration::get('OMNIBUS_SHOW_ICON_LISTING_DESKTOP', false),
            'OMNIBUS_SHOW_ICON_LISTING_MOBILE' => Configuration::get('OMNIBUS_SHOW_ICON_LISTING_MOBILE', false),
            'OMNIBUS_SHOW_ICON_QUICKVIEW_DESKTOP' => Configuration::get('OMNIBUS_SHOW_ICON_QUICKVIEW_DESKTOP', false),
            'OMNIBUS_SHOW_ICON_QUICKVIEW_MOBILE' => Configuration::get('OMNIBUS_SHOW_ICON_QUICKVIEW_MOBILE', false),
            
            // Advanced Settings
            'OMNIBUS_LOGGING_MODE' => Configuration::get('OMNIBUS_LOGGING_MODE', 'all'),
            'OMNIBUS_LOG_SCOPE' => json_decode(Configuration::get('OMNIBUS_LOG_SCOPE', json_encode(['product_update', 'cron_execution', 'configuration_change'])), true) ?? [],
            'OMNIBUS_LOG_ALL_COUNTRIES' => Configuration::get('OMNIBUS_LOG_ALL_COUNTRIES', true),
            'OMNIBUS_LOG_ALL_GROUPS' => Configuration::get('OMNIBUS_LOG_ALL_GROUPS', true),
            'OMNIBUS_LOG_RETENTION' => Configuration::get('OMNIBUS_LOG_RETENTION', 365),
            'OMNIBUS_CRON_TOKEN' => $this->getCronToken(),
            'OMNIBUS_CUSTOM_CSS' => Configuration::get('OMNIBUS_CUSTOM_CSS', ''),

            // Appearance Settings (Part 1)
            'OMNIBUS_BG_COLOR' => Configuration::get('OMNIBUS_BG_COLOR', '#ffffff'),
            'OMNIBUS_BORDER_COLOR' => Configuration::get('OMNIBUS_BORDER_COLOR', '#e5e7eb'),
            'OMNIBUS_BORDER_WIDTH' => Configuration::get('OMNIBUS_BORDER_WIDTH', 1),
            'OMNIBUS_WIDTH_MODE' => Configuration::get('OMNIBUS_WIDTH_MODE', 'full'),
            'OMNIBUS_PADDING_TOP' => Configuration::get('OMNIBUS_PADDING_TOP', 10),
            'OMNIBUS_PADDING_RIGHT' => Configuration::get('OMNIBUS_PADDING_RIGHT', 10),
            'OMNIBUS_PADDING_BOTTOM' => Configuration::get('OMNIBUS_PADDING_BOTTOM', 10),
            'OMNIBUS_PADDING_LEFT' => Configuration::get('OMNIBUS_PADDING_LEFT', 10),
            
            'OMNIBUS_ICON_CLASS' => Configuration::get('OMNIBUS_ICON_CLASS', 'info'),
            'OMNIBUS_ICON_IMAGE' => Configuration::get('OMNIBUS_ICON_IMAGE', ''),
            'OMNIBUS_ICON_SIZE' => Configuration::get('OMNIBUS_ICON_SIZE', 16),
            'OMNIBUS_ICON_SIZE' => Configuration::get('OMNIBUS_ICON_SIZE', 16),
            'OMNIBUS_ICON_COLOR' => Configuration::get('OMNIBUS_ICON_COLOR', '#3b82f6'),
            'OMNIBUS_CHART_TRIGGER' => Configuration::get('OMNIBUS_CHART_TRIGGER', 'icon'),
            
            'OMNIBUS_LABEL_FONT_SIZE' => Configuration::get('OMNIBUS_LABEL_FONT_SIZE', 14),
            'OMNIBUS_LABEL_COLOR' => Configuration::get('OMNIBUS_LABEL_COLOR', '#6b7280'),
            
            'OMNIBUS_PRICE_FONT_SIZE' => Configuration::get('OMNIBUS_PRICE_FONT_SIZE', 16),
            'OMNIBUS_PRICE_COLOR' => Configuration::get('OMNIBUS_PRICE_COLOR', '#111827'),
            
            'OMNIBUS_PERCENT_FONT_SIZE' => Configuration::get('OMNIBUS_PERCENT_FONT_SIZE', 12),
            'OMNIBUS_PERCENT_COLOR' => Configuration::get('OMNIBUS_PERCENT_COLOR', '#dc2626'),
            'OMNIBUS_PERCENT_BG_COLOR' => Configuration::get('OMNIBUS_PERCENT_BG_COLOR', '#fef2f2'),
            'OMNIBUS_PERCENT_PADDING_TOP' => Configuration::get('OMNIBUS_PERCENT_PADDING_TOP', 2),
            'OMNIBUS_PERCENT_PADDING_RIGHT' => Configuration::get('OMNIBUS_PERCENT_PADDING_RIGHT', 4),
            'OMNIBUS_PERCENT_PADDING_BOTTOM' => Configuration::get('OMNIBUS_PERCENT_PADDING_BOTTOM', 2),
            'OMNIBUS_PERCENT_PADDING_LEFT' => Configuration::get('OMNIBUS_PERCENT_PADDING_LEFT', 4),
            
            'OMNIBUS_LINK_FONT_SIZE' => Configuration::get('OMNIBUS_LINK_FONT_SIZE', 14),
            'OMNIBUS_LINK_COLOR' => Configuration::get('OMNIBUS_LINK_COLOR', '#3b82f6'),
            'OMNIBUS_LINK_BG_COLOR' => Configuration::get('OMNIBUS_LINK_BG_COLOR', '#eff6ff'),
            'OMNIBUS_LINK_PADDING_TOP' => Configuration::get('OMNIBUS_LINK_PADDING_TOP', 4),
            'OMNIBUS_LINK_PADDING_RIGHT' => Configuration::get('OMNIBUS_LINK_PADDING_RIGHT', 8),
            'OMNIBUS_LINK_PADDING_BOTTOM' => Configuration::get('OMNIBUS_LINK_PADDING_BOTTOM', 4),
            'OMNIBUS_LINK_PADDING_LEFT' => Configuration::get('OMNIBUS_LINK_PADDING_LEFT', 8),

            'OMNIBUS_LINK_PADDING_LEFT' => Configuration::get('OMNIBUS_LINK_PADDING_LEFT', 8),
        ];

        // Multilingual fields
        foreach (Language::getLanguages(false) as $lang) {
            $values['OMNIBUS_LABEL_PRODUCT'][$lang['id_lang']] = Configuration::get('OMNIBUS_LABEL_PRODUCT', $lang['id_lang']);
            $values['OMNIBUS_LABEL_LISTING'][$lang['id_lang']] = Configuration::get('OMNIBUS_LABEL_LISTING', $lang['id_lang']);
            $values['OMNIBUS_LABEL_QUICKVIEW'][$lang['id_lang']] = Configuration::get('OMNIBUS_LABEL_QUICKVIEW', $lang['id_lang']);
            $values['OMNIBUS_ICON_TOOLTIP'][$lang['id_lang']] = Configuration::get('OMNIBUS_ICON_TOOLTIP', $lang['id_lang']);
        }

        // Merge Matrix Values
        $values = array_merge($values, $this->getAppearanceMatrixValues());

        return $values;
    }

    protected function getAppearanceMatrixValues()
    {
        $devices = ['DESKTOP', 'MOBILE'];
        $views = ['PRODUCT', 'LISTING', 'QUICK'];
        
        $settings = [
            'WIDTH_MODE' => 'auto',
            'BORDER_WIDTH' => 1,
            'BG_COLOR' => '#ffffff',
            'BORDER_COLOR' => '#e5e7eb',
            'PADDING_TOP' => 10,
            'PADDING_RIGHT' => 10,
            'PADDING_BOTTOM' => 10,
            'PADDING_LEFT' => 10,
            'ELEMENT_SPACING' => 8,
            
            'LABEL_FONT_SIZE' => 14,
            'LABEL_COLOR' => '#6b7280',
            
            'PRICE_FONT_SIZE' => 16,
            'PRICE_COLOR' => '#111827',
            'PRICE_PADDING_TOP' => 5,
            'PRICE_PADDING_RIGHT' => 5,
            'PRICE_PADDING_BOTTOM' => 5,
            'PRICE_PADDING_LEFT' => 5,
            
            'PERCENT_FONT_SIZE' => 12,
            'PERCENT_COLOR' => '#dc2626',
            'PERCENT_BG_COLOR' => '#fef2f2',
            'PERCENT_PADDING_TOP' => 2,
            'PERCENT_PADDING_RIGHT' => 4,
            'PERCENT_PADDING_BOTTOM' => 2,
            'PERCENT_PADDING_LEFT' => 4,
            
            'PERCENT_BORDER_RADIUS' => 0,
            'PERCENT_BORDER_WIDTH' => 0,
            'PERCENT_BORDER_COLOR' => 'transparent',
            
            'ICON_SIZE' => 16,
            'ICON_COLOR' => '#3b82f6',
            
            'TOOLTIP_FONT_SIZE' => 12,
            'TOOLTIP_WIDTH' => 200,
            'TOOLTIP_COLOR' => '#ffffff',
            'TOOLTIP_BG_COLOR' => '#333333',
            'TOOLTIP_BORDER_WIDTH' => 1,
            'TOOLTIP_BORDER_COLOR' => '#666666',
            'TOOLTIP_PADDING_TOP' => 6,
            'TOOLTIP_PADDING_RIGHT' => 10,
            'TOOLTIP_PADDING_BOTTOM' => 6,
            'TOOLTIP_PADDING_LEFT' => 10,
        ];

        $values = [];
        foreach ($devices as $device) {
            foreach ($views as $view) {
                foreach ($settings as $key => $default) {
                    $configKey = 'OMNIBUS_' . $key . '_' . $device . '_' . $view;
                    // For Mobile, we might want different defaults, but for now use global defaults
                    // Ideally, we could inherit from Desktop if Mobile is not set, but PrestaShop config is flat.
                    // We'll handle inheritance in CSS generation or JS.
                    $values[$configKey] = Configuration::get($configKey, $default);
                }
            }
        }
        return $values;
    }

    protected function getCronToken()
    {
        $token = Configuration::get('OMNIBUS_CRON_TOKEN');
        if (!$token) {
            $token = Tools::passwdGen(32);
            Configuration::updateValue('OMNIBUS_CRON_TOKEN', $token);
        }
        return $token;
    }
}
