<?php
/**
 * ClearDeal - EU Omnibus Directive Compliance
 *
 * AJAX Controller - handles frontend AJAX requests (chart data)
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://marcingajewski.pl
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'cleardeal/classes/ClearDealPriceHistory.php';

class ClearDealAjaxModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ajax = true;

    public function initContent()
    {
        $action = Tools::getValue('action', '');

        switch ($action) {
            case 'getChartData':
                $this->getChartData();
                break;
            default:
                $this->ajaxResponse(['error' => 'Unknown action'], 400);
        }
    }

    /**
     * Get price history data for chart
     */
    protected function getChartData()
    {
        $id_product = (int) Tools::getValue('id_product', 0);
        $id_product_attribute = (int) Tools::getValue('id_product_attribute', 0);

        if (!$id_product) {
            $this->ajaxResponse(['error' => 'Invalid product ID'], 400);
            return;
        }

        // Check if chart feature is enabled
        if (!Configuration::get('CLEARDEAL_SHOW_CHART')) {
            $this->ajaxResponse(['error' => 'Chart feature is disabled'], 403);
            return;
        }

        // Get configuration
        $days = (int) Configuration::get('CLEARDEAL_DAYS') ?: 30;
        $price_type = Configuration::get('CLEARDEAL_PRICE_TYPE') ?: 'gross';

        // Get price history
        $history = ClearDealPriceHistory::getPriceHistory(
            $id_product,
            $id_product_attribute,
            $days,
            $price_type
        );

        // Get current price for reference
        $current_price = Product::getPriceStatic(
            $id_product,
            ($price_type === 'gross'),
            $id_product_attribute,
            2
        );

        // Get lowest price (fallback to current price if no history)
        $lowest_price = ClearDealPriceHistory::getLowestPrice(
            $id_product,
            $id_product_attribute,
            $days,
            $price_type
        );

        if (!$lowest_price || $lowest_price <= 0) {
            $lowest_price = $current_price;
        }

        // Format data for Chart.js
        $labels = [];
        $prices = [];

        foreach ($history as $entry) {
            $labels[] = date('d.m', strtotime($entry['captured_at']));
            $prices[] = round((float) $entry['price'], 2);
        }

        // Add current price as last point if we have history
        if (!empty($history)) {
            $labels[] = date('d.m');
            $prices[] = round($current_price, 2);
        }

        // Get product name
        $product = new Product($id_product, false, $this->context->language->id);
        $product_name = $product->name;

        // Format currency
        $currency = $this->context->currency;

        $this->ajaxResponse([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'prices' => $prices,
                'current_price' => round($current_price, 2),
                'lowest_price' => $lowest_price ? round($lowest_price, 2) : null,
                'product_name' => $product_name,
                'days' => $days,
                'currency' => [
                    'symbol' => $currency->sign,
                    'iso_code' => $currency->iso_code,
                    'format' => $currency->format,
                ],
            ],
        ]);
    }

    /**
     * Send JSON response
     *
     * @param array $data Response data
     * @param int $statusCode HTTP status code
     */
    protected function ajaxResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
