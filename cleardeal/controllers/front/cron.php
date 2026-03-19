<?php
/**
 * ClearDeal - EU Omnibus Directive Compliance
 *
 * CRON Controller - handles scheduled price logging
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://marcingajewski.pl
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'cleardeal/classes/ClearDealPriceHistory.php';

class ClearDealCronModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ajax = true;

    public function initContent()
    {
        // Verify token
        $token = Tools::getValue('token');
        $expectedToken = Tools::hash(Configuration::get('PS_SHOP_NAME'));

        if ($token !== $expectedToken) {
            $this->ajaxResponse(['error' => 'Invalid token'], 403);
            return;
        }

        $action = Tools::getValue('action', 'log');

        switch ($action) {
            case 'log':
                $this->processLogPrices();
                break;
            case 'clean':
                $this->processCleanLogs();
                break;
            case 'all':
            default:
                $this->processLogPrices();
                $this->processCleanLogs();
                break;
        }
    }

    protected function processLogPrices()
    {
        $logged = 0;
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
            $idProduct = (int) $product['id_product'];

            // Get all combinations
            $combinations = Product::getProductAttributesIds($idProduct);

            if (!empty($combinations)) {
                // Log price for each combination
                foreach ($combinations as $combination) {
                    $idAttribute = (int) $combination['id_product_attribute'];
                    if (ClearDealPriceHistory::logPrice($idProduct, $idAttribute)) {
                        $logged++;
                    } else {
                        $errors++;
                    }
                }
            } else {
                // Log price for product without combinations
                if (ClearDealPriceHistory::logPrice($idProduct, 0)) {
                    $logged++;
                } else {
                    $errors++;
                }
            }
        }

        $this->ajaxResponse([
            'success' => true,
            'action' => 'log',
            'logged' => $logged,
            'errors' => $errors,
            'message' => sprintf('Logged %d prices with %d errors', $logged, $errors),
        ]);
    }

    protected function processCleanLogs()
    {
        $days = (int) Configuration::get('CLEARDEAL_LOG_RETENTION') ?: 365;
        $deleted = ClearDealPriceHistory::cleanOldLogs($days);

        $this->ajaxResponse([
            'success' => true,
            'action' => 'clean',
            'deleted' => $deleted,
            'days' => $days,
            'message' => sprintf('Deleted logs older than %d days', $days),
        ]);
    }

    protected function ajaxResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
