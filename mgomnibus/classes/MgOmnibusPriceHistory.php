<?php

class MgOmnibusPriceHistory extends ObjectModel
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
        'table' => 'mgomnibus_price_history',
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

    public static $last_sql = '';

    public static function getLowestPrice($id_product, $id_product_attribute, $days, $price_type = 'incl')
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $id_currency = (int)$context->currency->id;
        $id_country = (int)$context->country->id;
        $id_group = (int)$context->customer->id_default_group;
        
        // If user is not logged in, they might be Visitor (1) or Guest (2).
        // But prices might have been logged for Customer (3) or All (0).
        // We should try to find the lowest price applicable to *this* user, 
        // but if we are strict, we might miss prices logged for "All".
        // Let's check for current group OR group 0 (all).
        
        $date_from = date('Y-m-d H:i:s', strtotime('-' . (int)$days . ' days'));
        
        $column = ($price_type === 'excl' || $price_type === 'net') ? 'price_tax_excl' : 'price_tax_incl';
        
        $sql = new DbQuery();
        $sql->select('MIN(' . $column . ')');
        $sql->from('mgomnibus_price_history');
        $sql->where('id_product = ' . (int)$id_product);
        // Fix: Search for specific attribute OR 0 (main product fallback)
        if ((int)$id_product_attribute > 0) {
            $sql->where('(id_product_attribute = ' . (int)$id_product_attribute . ' OR id_product_attribute = 0)');
        } else {
            $sql->where('id_product_attribute = 0');
        }
        $sql->where('id_shop = ' . (int)$id_shop);
        $sql->where('id_currency = ' . (int)$id_currency);
        
        // Country: Current or 0 (All)
        $sql->where('(id_country = ' . (int)$id_country . ' OR id_country = 0)');
        
        // Group: Current or 0 (All)
        $sql->where('(id_group = ' . (int)$id_group . ' OR id_group = 0)');
        
        $sql->where('captured_at >= \'' . pSQL($date_from) . '\'');
        
        self::$last_sql = $sql->build(); // Capture SQL

        $result = Db::getInstance()->getValue($sql);
        
        return $result ? (float)$result : null;
    }
    public static function addPriceChange($id_product)
    {
        if (!$id_product) {
            return;
        }

        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        
        // Determine Target Countries
        $country_ids = [];
        $log_all_countries = Configuration::get('OMNIBUS_LOG_ALL_COUNTRIES');
        if ($log_all_countries === false) $log_all_countries = false; 
        
        if ($log_all_countries) {
            $countries = Country::getCountries($context->language->id, true, false, $id_shop);
            foreach ($countries as $c) {
                $country_ids[] = (int)$c['id_country'];
            }
        } else {
            $cid = (int)Configuration::get('PS_COUNTRY_DEFAULT');
            $country_ids[] = $cid;
        }
        
        if (empty($country_ids)) {
             $cid = (int)Configuration::get('PS_COUNTRY_DEFAULT');
             $country_ids[] = $cid;
        }
        $country_ids = array_unique($country_ids);

        // Determine Target Groups
        $group_ids = [];
        $log_all_groups = Configuration::get('OMNIBUS_LOG_ALL_GROUPS');
        if ($log_all_groups === false) $log_all_groups = false;

        if ($log_all_groups) {
            $groups = Group::getGroups($context->language->id, $id_shop);
            foreach ($groups as $g) {
                $group_ids[] = (int)$g['id_group'];
            }
        } else {
            $group_ids[] = (int)Configuration::get('PS_CUSTOMER_GROUP');
        }
        
        if (empty($group_ids)) {
            $group_ids[] = (int)Configuration::get('PS_CUSTOMER_GROUP');
        }
        $group_ids = array_unique($group_ids);

        $product = new Product($id_product);
        
        // Handle combinations
        $combinations = $product->getAttributeCombinations($context->language->id);
        $attributes_to_log = [0]; 
        
        if (!empty($combinations)) {
            $attributes_to_log = array_map(function($c) { return $c['id_product_attribute']; }, $combinations);
            $attributes_to_log = array_unique($attributes_to_log);
        }

        // Loop through all combinations: Attribute * Country * Group
        foreach ($attributes_to_log as $id_product_attribute) {
            foreach ($country_ids as $id_country) {
                foreach ($group_ids as $id_group) {
                    $specific_price_output = null;
                    
                    // Match old module signature exactly
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
                        null, // id_cart
                        null, // id_address
                        $specific_price_output, // specific_price_output
                        true, // with_ecotax
                        true, // use_group_reduction
                        $context, // context
                        true, // use_customer_price
                        null, // id_customization
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

                    // OPTIMIZATION: Check last record to avoid duplicates
                    $last_record = Db::getInstance()->getRow('
                        SELECT price_tax_incl, captured_at
                        FROM `' . _DB_PREFIX_ . 'mgomnibus_price_history`
                        WHERE id_product = ' . (int)$id_product . '
                        AND id_product_attribute = ' . (int)$id_product_attribute . '
                        AND id_country = ' . (int)$id_country . '
                        AND id_group = ' . (int)$id_group . '
                        AND id_shop = ' . (int)$id_shop . '
                        AND id_currency = ' . (int)$id_currency . '
                        ORDER BY captured_at DESC
                    ');

                    $should_log = true;
                    if ($last_record) {
                        $last_price = (float)$last_record['price_tax_incl'];
                        $last_date = strtotime($last_record['captured_at']);
                        $current_price = (float)$price_tax_incl;
                        $days_diff = (time() - $last_date) / (60 * 60 * 24);

                        // Skip if price is same AND record is fresh (< 30 days)
                        // We use epsilon for float comparison
                        if (abs($last_price - $current_price) < 0.00001 && $days_diff < 30) {
                            $should_log = false;
                        }
                    }

                    if ($should_log) {
                        Db::getInstance()->insert('mgomnibus_price_history', [
                            'id_product' => (int)$id_product,
                            'id_product_attribute' => (int)$id_product_attribute,
                            'price_tax_incl' => (float)$price_tax_incl,
                            'price_tax_excl' => (float)$price_tax_excl,
                            'id_shop' => (int)$id_shop,
                            'id_currency' => (int)$id_currency,
                            'id_country' => (int)$id_country,
                            'id_group' => (int)$id_group,
                            'captured_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }
    }
}
