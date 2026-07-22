<?php
/**
 * ClearDeal - EU Omnibus Directive Compliance
 *
 * Price History Model - stores and retrieves historical price data
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ClearDealPriceHistory extends ObjectModel
{
    public $id_history;
    public $id_product;
    public $id_product_attribute;
    public $price_tax_incl;
    public $price_tax_excl;
    public $id_shop;
    public $id_currency;
    public $id_country;
    public $id_group;
    public $captured_at;

    public static $definition = [
        'table' => 'cleardeal_price_history',
        'primary' => 'id_history',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'price_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'price_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_currency' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_country' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'captured_at' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
        ],
    ];

    /**
     * Get the lowest price for a product within a specified period
     *
     * @param int $id_product Product ID
     * @param int $id_product_attribute Product attribute ID (0 for main product)
     * @param int $days Number of days to look back
     * @param string $price_type Price type: 'gross' or 'net'
     * @return float|null Lowest price or null if not found
     */
    public static function getLowestPrice($id_product, $id_product_attribute, $days, $price_type = 'gross')
    {
        $context = Context::getContext();
        $id_shop = (int) $context->shop->id;
        $id_currency = (int) $context->currency->id;
        $id_country = (int) $context->country->id;
        $id_group = (int) $context->customer->id_default_group;

        $date_from = date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days'));
        $column = ($price_type === 'net') ? 'price_tax_excl' : 'price_tax_incl';

        $sql = new DbQuery();
        $sql->select('MIN(' . $column . ')');
        $sql->from('cleardeal_price_history');
        $sql->where('id_product = ' . (int) $id_product);

        // Search for specific attribute OR 0 (main product fallback)
        if ((int) $id_product_attribute > 0) {
            $sql->where('(id_product_attribute = ' . (int) $id_product_attribute . ' OR id_product_attribute = 0)');
        } else {
            $sql->where('id_product_attribute = 0');
        }

        $sql->where('id_shop = ' . (int) $id_shop);
        $sql->where('id_currency = ' . (int) $id_currency);

        // Country: Current or 0 (All)
        $sql->where('(id_country = ' . (int) $id_country . ' OR id_country = 0)');

        // Group: Current or 0 (All)
        $sql->where('(id_group = ' . (int) $id_group . ' OR id_group = 0)');

        $sql->where('captured_at >= \'' . pSQL($date_from) . '\'');

        $result = Db::getInstance()->getValue($sql);

        return $result ? (float) $result : null;
    }

    /**
     * Log a price change for a product
     *
     * @param int $id_product Product ID
     * @return void
     */
    public static function addPriceChange($id_product)
    {
        if (!$id_product) {
            return;
        }

        $context = Context::getContext();
        $id_shop = (int) $context->shop->id;
        $id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');

        // Get default country
        $id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');

        // Get default customer group
        $id_group = (int) Configuration::get('PS_CUSTOMER_GROUP');

        $product = new Product($id_product);

        // Handle combinations
        $combinations = $product->getAttributeCombinations($context->language->id);
        $attributes_to_log = [0]; // Always log main product

        if (!empty($combinations)) {
            $attributes_to_log = array_merge(
                $attributes_to_log,
                array_unique(array_map(function ($c) {
                    return (int) $c['id_product_attribute'];
                }, $combinations))
            );
        }

        foreach ($attributes_to_log as $id_product_attribute) {
            self::logSinglePrice($id_product, $id_product_attribute, $id_shop, $id_currency, $id_country, $id_group);
        }
    }

    /**
     * Log a single price entry
     */
    private static function logSinglePrice($id_product, $id_product_attribute, $id_shop, $id_currency, $id_country, $id_group)
    {
        $context = Context::getContext();

        // Get current prices
        $price_tax_incl = Product::getPriceStatic(
            $id_product,
            true,
            $id_product_attribute,
            6,
            null,
            false,
            true,
            1,
            false,
            null,
            null,
            null,
            $specific_price_output,
            true,
            true,
            $context,
            true,
            null,
            $id_group,
            $id_country
        );

        $price_tax_excl = Product::getPriceStatic(
            $id_product,
            false,
            $id_product_attribute,
            6,
            null,
            false,
            true,
            1,
            false,
            null,
            null,
            null,
            $specific_price_output,
            true,
            true,
            $context,
            true,
            null,
            $id_group,
            $id_country
        );

        // Check last record to avoid duplicates
        $last_record = Db::getInstance()->getRow('
            SELECT price_tax_incl, captured_at
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_product = ' . (int) $id_product . '
            AND id_product_attribute = ' . (int) $id_product_attribute . '
            AND id_country = ' . (int) $id_country . '
            AND id_group = ' . (int) $id_group . '
            AND id_shop = ' . (int) $id_shop . '
            AND id_currency = ' . (int) $id_currency . '
            ORDER BY captured_at DESC
        ');

        $should_log = true;
        if ($last_record) {
            $last_price = (float) $last_record['price_tax_incl'];
            $last_date = strtotime($last_record['captured_at']);
            $current_price = (float) $price_tax_incl;
            $days_diff = (time() - $last_date) / (60 * 60 * 24);

            // Skip if price is same AND record is fresh (< 30 days)
            if (abs($last_price - $current_price) < 0.00001 && $days_diff < 30) {
                $should_log = false;
            }
        }

        if ($should_log) {
            Db::getInstance()->insert('cleardeal_price_history', [
                'id_product' => (int) $id_product,
                'id_product_attribute' => (int) $id_product_attribute,
                'price_tax_incl' => (float) $price_tax_incl,
                'price_tax_excl' => (float) $price_tax_excl,
                'id_shop' => (int) $id_shop,
                'id_currency' => (int) $id_currency,
                'id_country' => (int) $id_country,
                'id_group' => (int) $id_group,
                'captured_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Get logs for admin panel with pagination and filters
     *
     * @param int $page Page number (1-based)
     * @param int $per_page Items per page
     * @param string $order_by Column to order by
     * @param string $order_way ASC or DESC
     * @param array $filters Associative array of filters (product_search, date_from, date_to)
     * @return array
     */
    public static function getLogs($page = 1, $per_page = 50, $order_by = 'captured_at', $order_way = 'DESC', $filters = [])
    {
        $context = Context::getContext();
        $id_lang = (int) $context->language->id;
        $id_shop = (int) $context->shop->id;

        $offset = ($page - 1) * $per_page;

        // Whitelist for order_by
        $allowed_order = ['id_history', 'id_product', 'product_name', 'price_tax_incl', 'price_tax_excl', 'captured_at'];
        if (!in_array($order_by, $allowed_order)) {
            $order_by = 'captured_at';
        }
        $order_way = strtoupper($order_way) === 'ASC' ? 'ASC' : 'DESC';

        $where = 'WHERE h.id_shop = ' . $id_shop;

        // Product search filter (by ID or name)
        if (!empty($filters['product_search'])) {
            $search = pSQL($filters['product_search']);
            if (is_numeric($search)) {
                $where .= ' AND h.id_product = ' . (int) $search;
            } else {
                $where .= ' AND pl.name LIKE \'%' . $search . '%\'';
            }
        }

        // Date from filter
        if (!empty($filters['date_from'])) {
            $where .= ' AND h.captured_at >= \'' . pSQL($filters['date_from']) . ' 00:00:00\'';
        }

        // Date to filter
        if (!empty($filters['date_to'])) {
            $where .= ' AND h.captured_at <= \'' . pSQL($filters['date_to']) . ' 23:59:59\'';
        }

        $sql = '
            SELECT h.*, pl.name as product_name
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history` h
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                pl.id_product = h.id_product
                AND pl.id_lang = ' . $id_lang . '
                AND pl.id_shop = h.id_shop
            )
            ' . $where . '
            ORDER BY ' . pSQL($order_by) . ' ' . $order_way . '
            LIMIT ' . (int) $offset . ', ' . (int) $per_page;

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get total count of logs with optional filters
     *
     * @param array $filters Associative array of filters
     * @return int
     */
    public static function getLogsCount($filters = [])
    {
        $context = Context::getContext();
        $id_lang = (int) $context->language->id;
        $id_shop = (int) $context->shop->id;

        $where = 'WHERE h.id_shop = ' . $id_shop;

        // Product search filter (by ID or name)
        if (!empty($filters['product_search'])) {
            $search = pSQL($filters['product_search']);
            if (is_numeric($search)) {
                $where .= ' AND h.id_product = ' . (int) $search;
            } else {
                $where .= ' AND pl.name LIKE \'%' . $search . '%\'';
            }
        }

        // Date from filter
        if (!empty($filters['date_from'])) {
            $where .= ' AND h.captured_at >= \'' . pSQL($filters['date_from']) . ' 00:00:00\'';
        }

        // Date to filter
        if (!empty($filters['date_to'])) {
            $where .= ' AND h.captured_at <= \'' . pSQL($filters['date_to']) . ' 23:59:59\'';
        }

        $sql = '
            SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history` h
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                pl.id_product = h.id_product
                AND pl.id_lang = ' . $id_lang . '
                AND pl.id_shop = h.id_shop
            )
            ' . $where;

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * Delete logs older than specified days
     *
     * @param int $days Days to keep
     * @return bool
     */
    public static function cleanOldLogs($days = 365)
    {
        $date = date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days'));

        return Db::getInstance()->execute('
            DELETE FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE captured_at < \'' . pSQL($date) . '\'
        ');
    }

    /**
     * Export logs to CSV format
     *
     * @param array $filters Optional filters (product_search, date_from, date_to)
     * @return string CSV content
     */
    public static function exportToCsv($filters = [])
    {
        $context = Context::getContext();
        $id_lang = (int) $context->language->id;
        $id_shop = (int) $context->shop->id;

        $where = 'WHERE h.id_shop = ' . $id_shop;

        // Product search filter (by ID or name)
        if (!empty($filters['product_search'])) {
            $search = pSQL($filters['product_search']);
            if (is_numeric($search)) {
                $where .= ' AND h.id_product = ' . (int) $search;
            } else {
                $where .= ' AND pl.name LIKE \'%' . $search . '%\'';
            }
        }

        // Date from filter
        if (!empty($filters['date_from'])) {
            $where .= ' AND h.captured_at >= \'' . pSQL($filters['date_from']) . ' 00:00:00\'';
        }

        // Date to filter
        if (!empty($filters['date_to'])) {
            $where .= ' AND h.captured_at <= \'' . pSQL($filters['date_to']) . ' 23:59:59\'';
        }

        $logs = Db::getInstance()->executeS('
            SELECT h.*, pl.name as product_name
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history` h
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                pl.id_product = h.id_product
                AND pl.id_lang = ' . $id_lang . '
                AND pl.id_shop = h.id_shop
            )
            ' . $where . '
            ORDER BY h.captured_at DESC
        ');

        $output = fopen('php://temp', 'r+');

        // Header
        fputcsv($output, [
            'ID',
            'Product ID',
            'Product Name',
            'Attribute ID',
            'Price (Gross)',
            'Price (Net)',
            'Currency ID',
            'Country ID',
            'Group ID',
            'Shop ID',
            'Date',
        ], ';');

        // Data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['id_history'],
                $log['id_product'],
                $log['product_name'],
                $log['id_product_attribute'],
                $log['price_tax_incl'],
                $log['price_tax_excl'],
                $log['id_currency'],
                $log['id_country'],
                $log['id_group'],
                $log['id_shop'],
                $log['captured_at'],
            ], ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Import logs from CSV file
     *
     * @param string $filePath Path to CSV file
     * @return int Number of imported records
     */
    public static function importFromCsv($filePath)
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return 0;
        }

        // Skip header
        $header = fgetcsv($handle, 0, ';');
        if (!$header) {
            $header = fgetcsv($handle, 0, ',');
            rewind($handle);
            fgetcsv($handle, 0, ','); // Skip header again
        }

        $imported = 0;
        $delimiter = (count($header) > 1) ? ';' : ',';

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($data) < 11) {
                continue;
            }

            // Map columns
            $history = new self();
            $history->id_product = (int) $data[1];
            $history->id_product_attribute = (int) $data[3];
            $history->price_tax_incl = (float) str_replace(',', '.', $data[4]);
            $history->price_tax_excl = (float) str_replace(',', '.', $data[5]);
            $history->id_currency = (int) $data[6];
            $history->id_country = (int) $data[7];
            $history->id_group = (int) $data[8];
            $history->id_shop = (int) $data[9];
            $history->captured_at = $data[10];

            if ($history->add()) {
                $imported++;
            }
        }

        fclose($handle);

        return $imported;
    }

    /**
     * Delete multiple logs by IDs
     *
     * @param array $ids Array of log IDs to delete
     * @return bool
     */
    public static function deleteByIds(array $ids)
    {
        if (empty($ids)) {
            return false;
        }

        $ids = array_map('intval', $ids);

        return Db::getInstance()->execute('
            DELETE FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_history IN (' . implode(',', $ids) . ')
        ');
    }

    /**
     * Log price for a specific product and attribute
     * Used by CRON controller
     *
     * @param int $id_product Product ID
     * @param int $id_product_attribute Product attribute ID (0 for main product)
     * @return bool
     */
    public static function logPrice($id_product, $id_product_attribute = 0)
    {
        if (!$id_product) {
            return false;
        }

        $context = Context::getContext();
        $id_shop = (int) $context->shop->id;
        $id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $id_group = (int) Configuration::get('PS_CUSTOMER_GROUP');

        // Get current prices
        $price_tax_incl = Product::getPriceStatic(
            $id_product,
            true,
            $id_product_attribute,
            6,
            null,
            false,
            true
        );

        $price_tax_excl = Product::getPriceStatic(
            $id_product,
            false,
            $id_product_attribute,
            6,
            null,
            false,
            true
        );

        // Check last record to avoid duplicates
        $last_record = Db::getInstance()->getRow('
            SELECT price_tax_incl, captured_at
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_product = ' . (int) $id_product . '
            AND id_product_attribute = ' . (int) $id_product_attribute . '
            AND id_country = ' . (int) $id_country . '
            AND id_group = ' . (int) $id_group . '
            AND id_shop = ' . (int) $id_shop . '
            AND id_currency = ' . (int) $id_currency . '
            ORDER BY captured_at DESC
        ');

        $should_log = true;
        if ($last_record) {
            $last_price = (float) $last_record['price_tax_incl'];
            $last_date = strtotime($last_record['captured_at']);
            $current_price = (float) $price_tax_incl;
            $days_diff = (time() - $last_date) / (60 * 60 * 24);

            // Skip if price is same AND record is fresh (< 30 days)
            if (abs($last_price - $current_price) < 0.00001 && $days_diff < 30) {
                $should_log = false;
            }
        }

        if ($should_log) {
            return Db::getInstance()->insert('cleardeal_price_history', [
                'id_product' => (int) $id_product,
                'id_product_attribute' => (int) $id_product_attribute,
                'price_tax_incl' => (float) $price_tax_incl,
                'price_tax_excl' => (float) $price_tax_excl,
                'id_shop' => (int) $id_shop,
                'id_currency' => (int) $id_currency,
                'id_country' => (int) $id_country,
                'id_group' => (int) $id_group,
                'captured_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return true;
    }

    /**
     * Log prices for all active products
     * Used for manual "Log All Prices" feature
     *
     * @return array ['logged' => int, 'skipped' => int, 'errors' => int]
     */
    public static function logAllPrices()
    {
        $logged = 0;
        $skipped = 0;
        $errors = 0;

        // Get all active products
        $products = Product::getProducts(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            0,
            0,
            'id_product',
            'ASC',
            false,
            true
        );

        foreach ($products as $product) {
            $id_product = (int) $product['id_product'];

            // Check if product is in excluded category
            if (class_exists('ClearDeal') && ClearDeal::isProductExcluded($id_product)) {
                $skipped++;
                continue;
            }

            // Get all combinations
            $combinations = Product::getProductAttributesIds($id_product);

            if (!empty($combinations)) {
                // Log price for each combination
                foreach ($combinations as $combination) {
                    $id_attribute = (int) $combination['id_product_attribute'];
                    $result = self::logPrice($id_product, $id_attribute);
                    if ($result) {
                        $logged++;
                    } else {
                        $errors++;
                    }
                }
            } else {
                // Log price for product without combinations
                $result = self::logPrice($id_product, 0);
                if ($result) {
                    $logged++;
                } else {
                    $errors++;
                }
            }
        }

        return [
            'logged' => $logged,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Get statistics for dashboard (with caching)
     *
     * @param bool $force_refresh Force refresh cache
     * @return array
     */
    public static function getStatistics($force_refresh = false)
    {
        $cache_key = 'CLEARDEAL_STATS_CACHE';
        $cache_time_key = 'CLEARDEAL_STATS_CACHE_TIME';
        $cache_ttl = 3600; // 1 hour

        // Check cache
        if (!$force_refresh) {
            $cached = Configuration::get($cache_key);
            $cached_time = (int) Configuration::get($cache_time_key);

            if ($cached && $cached_time && (time() - $cached_time) < $cache_ttl) {
                $decoded = json_decode($cached, true);
                if (is_array($decoded)) {
                    $decoded['from_cache'] = true;
                    $decoded['cache_age'] = time() - $cached_time;
                    return $decoded;
                }
            }
        }

        $context = Context::getContext();
        $id_shop = (int) $context->shop->id;

        // Total logs count
        $total_logs = (int) Db::getInstance()->getValue('
            SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_shop = ' . $id_shop
        );

        // Unique products count
        $unique_products = (int) Db::getInstance()->getValue('
            SELECT COUNT(DISTINCT id_product)
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_shop = ' . $id_shop
        );

        // Logs in last 7 days
        $last_7_days = (int) Db::getInstance()->getValue('
            SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_shop = ' . $id_shop . '
            AND captured_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ');

        // Logs in last 30 days
        $last_30_days = (int) Db::getInstance()->getValue('
            SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_shop = ' . $id_shop . '
            AND captured_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ');

        // Oldest log date
        $oldest_log = Db::getInstance()->getValue('
            SELECT MIN(captured_at)
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_shop = ' . $id_shop
        );

        // Newest log date
        $newest_log = Db::getInstance()->getValue('
            SELECT MAX(captured_at)
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_shop = ' . $id_shop
        );

        $stats = [
            'total_logs' => $total_logs,
            'unique_products' => $unique_products,
            'last_7_days' => $last_7_days,
            'last_30_days' => $last_30_days,
            'oldest_log' => $oldest_log,
            'newest_log' => $newest_log,
            'from_cache' => false,
            'cache_age' => 0,
            'generated_at' => time(),
        ];

        // Save to cache
        Configuration::updateValue($cache_key, json_encode($stats));
        Configuration::updateValue($cache_time_key, time());

        return $stats;
    }

    /**
     * Get price history for a product (for chart)
     *
     * @param int $id_product Product ID
     * @param int $id_product_attribute Product attribute ID
     * @param int $days Number of days
     * @param string $price_type 'gross' or 'net'
     * @return array
     */
    public static function getPriceHistory($id_product, $id_product_attribute = 0, $days = 30, $price_type = 'gross')
    {
        $context = Context::getContext();
        $id_shop = (int) $context->shop->id;
        $id_currency = (int) $context->currency->id;

        $column = ($price_type === 'net') ? 'price_tax_excl' : 'price_tax_incl';
        $date_from = date('Y-m-d H:i:s', strtotime('-' . (int) $days . ' days'));

        $sql = '
            SELECT captured_at, ' . $column . ' as price
            FROM `' . _DB_PREFIX_ . 'cleardeal_price_history`
            WHERE id_product = ' . (int) $id_product . '
            AND id_product_attribute = ' . (int) $id_product_attribute . '
            AND id_shop = ' . (int) $id_shop . '
            AND id_currency = ' . (int) $id_currency . '
            AND captured_at >= \'' . pSQL($date_from) . '\'
            ORDER BY captured_at ASC
        ';

        return Db::getInstance()->executeS($sql) ?: [];
    }
}
