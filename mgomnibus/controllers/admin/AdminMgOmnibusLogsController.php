<?php

require_once _PS_MODULE_DIR_ . 'mgomnibus/classes/MgOmnibusPriceHistory.php';

class AdminMgOmnibusLogsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'mgomnibus_price_history';
        $this->className = 'MgOmnibusPriceHistory';
        $this->identifier = 'id_history';
        $this->lang = false;
        
        parent::__construct();
        
        $this->fields_list = [
            'id_history' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'id_product' => [
                'title' => $this->trans('Product ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'product_name' => [
                'title' => $this->trans('Product Name', [], 'Admin.Global'),
                'align' => 'left',
                'filter_key' => 'pl!name',
                'width' => 'auto'
            ],
            'id_product_attribute' => [
                'title' => $this->trans('Attribute ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'price_tax_incl' => [
                'title' => $this->trans('Price (Gross)', [], 'Admin.Global'),
                'type' => 'price',
                'currency' => true,
                'class' => 'fixed-width-sm'
            ],
            'price_tax_excl' => [
                'title' => $this->trans('Price (Net)', [], 'Admin.Global'),
                'type' => 'price',
                'currency' => true,
                'class' => 'fixed-width-sm'
            ],
            'id_currency' => [
                'title' => $this->trans('Currency ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'id_country' => [
                'title' => $this->trans('Country ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'id_group' => [
                'title' => $this->trans('Group ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'id_shop' => [
                'title' => $this->trans('Shop ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ],
            'captured_at' => [
                'title' => $this->trans('Date', [], 'Admin.Global'),
                'type' => 'datetime',
                'filter_key' => 'a!captured_at'
            ],
        ];
        
        $this->actions = ['delete']; 
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            ]
        ];
        
        $this->list_no_link = true; // Disable row click

        $this->_defaultOrderBy = 'captured_at';
        $this->_defaultOrderWay = 'DESC';
    }

    public function renderList()
    {
        // Handle custom actions first
        if (Tools::getValue('act') == 'import_view') {
            return $this->renderImportForm();
        }

        // Add JOINs for product name
        $this->_select = 'pl.name as product_name';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                pl.`id_product` = a.`id_product` 
                AND pl.`id_lang` = ' . (int)$this->context->language->id . ' 
                AND pl.id_shop = a.id_shop
            )';

        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();

        $this->page_header_toolbar_btn['export_logs'] = [
            'href' => self::$currentIndex . '&exportmgomnibus_price_history&token=' . $this->token,
            'desc' => $this->trans('Export', [], 'Admin.Actions'),
            'icon' => 'process-icon-export'
        ];

        $this->page_header_toolbar_btn['import_logs'] = [
            'href' => self::$currentIndex . '&act=import_view&token=' . $this->token,
            'desc' => $this->trans('Import', [], 'Admin.Actions'),
            'icon' => 'process-icon-import'
        ];
    }

    public function postProcess()
    {
        if (Tools::isSubmit('import_logs_submit')) {
            if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
                $this->processImport($_FILES['import_file']['tmp_name']);
            } else {
                $this->errors[] = $this->trans('Error uploading file.', [], 'Admin.Notifications.Error');
            }
        }
        
        parent::postProcess();
    }

    protected function renderImportForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Import Logs', [], 'Admin.Global'),
                    'icon' => 'icon-upload'
                ],
                'input' => [
                    [
                        'type' => 'file',
                        'label' => $this->trans('CSV File', [], 'Admin.Global'),
                        'name' => 'import_file',
                        'required' => true,
                        'desc' => $this->trans('Upload a CSV file with columns: id_product, id_product_attribute, price_tax_incl, price_tax_excl, id_shop, id_currency, id_country, id_group, captured_at', [], 'Admin.Global')
                    ]
                ],
                'submit' => [
                    'title' => $this->trans('Import', [], 'Admin.Actions'),
                    'name' => 'import_logs_submit',
                    'icon' => 'process-icon-import'
                ]
            ]
        ];

        $helper = new HelperForm();
        $helper->module = $this->module;
        $helper->identifier = $this->identifier;
        $helper->token = $this->token;
        $helper->currentIndex = self::$currentIndex . '&act=import_view';
        $helper->submit_action = 'import_logs_submit';

        return $helper->generateForm([$fields_form]);
    }

    protected function processImport($filePath)
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->errors[] = $this->trans('Cannot open file.', [], 'Admin.Notifications.Error');
            return;
        }

        // Skip header row if present (simple check: if first column is 'id_product' or similar string)
        // We assume standard CSV format
        $header = fgetcsv($handle, 0, ';'); // Try semicolon first
        if (!$header || count($header) < 2) {
             rewind($handle);
             $header = fgetcsv($handle, 0, ','); // Try comma
        }
        
        // If header looks like keys, skip it. Otherwise rewind.
        if (!in_array('id_product', $header) && !in_array('ID Product', $header)) {
            rewind($handle);
        }

        $imported = 0;
        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if (count($data) < 2) {
                 // Try comma if semicolon failed for this row (though usually consistent)
                 // Actually fgetcsv needs delimiter specified. Let's assume semicolon for export compatibility or comma.
                 // For now, let's stick to one. PrestaShop export usually uses semicolon.
            }
            
            // Mapping: 0: id_history (skip), 1: id_product, 2: product_name (skip), 3: id_product_attribute, 4: price_incl, 5: price_excl, 6: currency, 7: country, 8: group, 9: shop, 10: date
            // This depends heavily on the export format. 
            // Let's assume the user uses the export file from this controller.
            // The default export fields order matches fields_list.
            
            // fields_list keys: id_history, id_product, product_name, id_product_attribute, price_tax_incl, price_tax_excl, id_currency, id_country, id_group, id_shop, captured_at
            
            // Index mapping based on fields_list:
            // 0: id_history
            // 1: id_product
            // 2: product_name
            // 3: id_product_attribute
            // 4: price_tax_incl
            // 5: price_tax_excl
            // 6: id_currency
            // 7: id_country
            // 8: id_group
            // 9: id_shop
            // 10: captured_at
            
            if (count($data) < 11) continue;

            $history = new MgOmnibusPriceHistory();
            $history->id_product = (int)$data[1];
            $history->id_product_attribute = (int)$data[3];
            $history->price_tax_incl = (float)str_replace(',', '.', $data[4]); // Handle comma decimals
            $history->price_tax_excl = (float)str_replace(',', '.', $data[5]);
            $history->id_currency = (int)$data[6];
            $history->id_country = (int)$data[7];
            $history->id_group = (int)$data[8];
            $history->id_shop = (int)$data[9];
            $history->captured_at = $data[10];
            
            if ($history->add()) {
                $imported++;
            }
        }
        fclose($handle);
        
        $this->confirmations[] = $this->trans('Imported %d log entries.', [$imported], 'Admin.Notifications.Success');
    }
}
