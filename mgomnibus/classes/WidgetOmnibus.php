<?php

namespace MgOmnibus\Widget;

use CE\WidgetBase;
use CE\ControlsManager;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WidgetOmnibus extends WidgetBase
{
    const REMOTE_RENDER = true;

    public function l($string)
    {
        return \Translate::getModuleTranslation('mgomnibus', $string, 'widgetomnibus');
    }

    public function getName()
    {
        return 'mg_omnibus';
    }

    public function getTitle()
    {
        return 'MG Omnibus';
    }

    public function getIcon()
    {
        return 'eicon-price-tag';
    }

    public function getCategories()
    {
        return ['mg_extensions'];
    }

    protected function _registerControls()
    {
        // --- CONTENT SECTION ---
        $this->startControlsSection(
            'section_content',
            [
                'label' => $this->l('Omnibus Settings'),
            ]
        );

        $this->addControl(
            'omnibus_days',
            [
                'label' => $this->l('Days'),
                'type' => ControlsManager::NUMBER,
                'default' => 30,
            ]
        );

        $this->addControl(
            'display_mode',
            [
                'label' => $this->l('Display Mode'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => $this->l('Module Default'),
                    'always' => $this->l('Always'),
                    'on_sale' => $this->l('Only on reduction'),
                ],
                'default' => 'default',
            ]
        );

        $this->addControl(
            'price_type',
            [
                'label' => $this->l('Price Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => $this->l('Module Default'),
                    'incl' => $this->l('Gross'),
                    'excl' => $this->l('Net'),
                ],
                'default' => 'default',
            ]
        );

        $this->addControl(
            'show_percent_mode',
            [
                'label' => $this->l('Show Percentage'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => $this->l('Module Default'),
                    'always' => $this->l('Yes, Always'),
                    'negative_only' => $this->l('Only if discounted'),
                    'no' => $this->l('No'),
                ],
                'default' => 'default',
            ]
        );
        
        $this->addControl(
            'show_icon',
            [
                'label' => $this->l('Show Icon'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => $this->l('Module Default'),
                    'yes' => $this->l('Yes'),
                    'no' => $this->l('No'),
                ],
                'default' => 'default',
            ]
        );
        
        $this->addControl(
            'icon_position',
            [
                'label' => $this->l('Icon Position'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'start' => $this->l('Start'),
                    'end' => $this->l('End'),
                ],
                'default' => 'start',
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );
        
        $this->addControl(
            'icon_type',
            [
                'label' => $this->l('Icon Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => $this->l('Module Default'),
                    'class' => $this->l('Material Icon Name'),
                    'file' => $this->l('Image File'),
                ],
                'default' => 'default',
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );
        
        $this->addControl(
            'icon_class',
            [
                'label' => $this->l('Icon Name'),
                'type' => ControlsManager::TEXT,
                'default' => 'info',
                'description' => $this->l('E.g. "info", "history", "local_offer". See Google Fonts Icons.'),
                'condition' => [
                    'show_icon' => 'yes',
                    'icon_type' => 'class',
                ],
            ]
        );
        
        $this->addControl(
            'icon_image',
            [
                'label' => $this->l('Icon Image'),
                'type' => ControlsManager::MEDIA,
                'condition' => [
                    'show_icon' => 'yes',
                    'icon_type' => 'file',
                ],
            ]
        );

        $this->addControl(
            'custom_label',
            [
                'label' => $this->l('Custom Label'),
                'type' => ControlsManager::TEXT,
                'description' => $this->l('Leave empty to use module default. Use %s for days.'),
            ]
        );

        $this->addControl(
            'tooltip_text',
            [
                'label' => $this->l('Tooltip Text'),
                'type' => ControlsManager::TEXTAREA,
                'rows' => 3,
                'description' => $this->l('Text to display when hovering the icon.'),
            ]
        );
        
        $this->addResponsiveControl(
            'alignment',
            [
                'label' => $this->l('Alignment'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => $this->l('Left'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => $this->l('Center'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => $this->l('Right'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'flex-start',
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-wrapper' => 'display: flex; justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'vertical_align',
            [
                'label' => $this->l('Vertical Align'),
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => $this->l('Top'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => $this->l('Middle'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => $this->l('Bottom'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-price' => 'display: flex; align-items: {{VALUE}};',
                ],
            ]
        );
        
        $this->addResponsiveControl(
            'elements_spacing',
            [
                'label' => $this->l('Elements Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-price' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
        
        // --- STYLE SECTION: CONTAINER ---
        $this->startControlsSection(
            'section_style_container',
            [
                'label' => $this->l('Container Style'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlBackground::getType(),
            [
                'name' => 'container_background',
                'selector' => '{{WRAPPER}} .mg-omnibus-wrapper',
            ]
        );
        
        $this->addGroupControl(
            \CE\GroupControlBorder::getType(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .mg-omnibus-wrapper',
            ]
        );
        
        $this->addControl(
            'container_border_radius',
            [
                'label' => $this->l('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlBoxShadow::getType(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .mg-omnibus-wrapper',
            ]
        );

        $this->addResponsiveControl(
            'container_padding',
            [
                'label' => $this->l('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->endControlsSection();

        // --- STYLE SECTION: TYPOGRAPHY ---
        $this->startControlsSection(
            'section_style_typography',
            [
                'label' => $this->l('Typography & Colors'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );
        
        // Label Style
        $this->addControl(
            'heading_label_style',
            [
                'label' => $this->l('Label'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'label_color',
            [
                'label' => $this->l('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .mg-omnibus-price',
            ]
        );
        
        // Price Style
        $this->addControl(
            'heading_price_style',
            [
                'label' => $this->l('Price'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );
        
        $this->addControl(
            'price_color',
            [
                'label' => $this->l('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-amount' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->addGroupControl(
            \CE\GroupControlTypography::getType(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .mg-omnibus-amount',
            ]
        );

        // Percentage Style
        $this->addControl(
            'heading_percent_style',
            [
                'label' => $this->l('Percentage'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );
        
        $this->addControl(
            'percent_color',
            [
                'label' => $this->l('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-percent' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->addControl(
            'percent_bg_color',
            [
                'label' => $this->l('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-percent' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->addGroupControl(
            \CE\GroupControlTypography::getType(),
            [
                'name' => 'percent_typography',
                'selector' => '{{WRAPPER}} .mg-omnibus-percent',
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlBorder::getType(),
            [
                'name' => 'percent_border',
                'selector' => '{{WRAPPER}} .mg-omnibus-percent',
            ]
        );
        
        $this->addControl(
            'percent_border_radius',
            [
                'label' => $this->l('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-percent' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->addResponsiveControl(
            'percent_padding',
            [
                'label' => $this->l('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-percent' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
        
        // --- STYLE SECTION: ICON ---
        $this->startControlsSection(
            'section_style_icon',
            [
                'label' => $this->l('Icon Style'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );
        
        $this->addControl(
            'icon_color',
            [
                'label' => $this->l('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .mg-omnibus-icon svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .mg-omnibus-icon-wrapper i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .mg-omnibus-icon-wrapper svg' => 'fill: {{VALUE}};',
                ],
            ]
        );
        
        $this->addResponsiveControl(
            'icon_size',
            [
                'label' => $this->l('Size'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mg-omnibus-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mg-omnibus-icon img' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mg-omnibus-icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mg-omnibus-icon-wrapper svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mg-omnibus-icon-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->endControlsSection();

        // --- STYLE SECTION: TOOLTIP ---
        $this->startControlsSection(
            'section_style_tooltip',
            [
                'label' => $this->l('Tooltip Style'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'tooltip_width',
            [
                'label' => $this->l('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-tooltip' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'tooltip_bg_color',
            [
                'label' => $this->l('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-tooltip' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'tooltip_text_color',
            [
                'label' => $this->l('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-tooltip' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlBorder::getType(),
            [
                'name' => 'tooltip_border',
                'selector' => '{{WRAPPER}} .mg-omnibus-tooltip',
            ]
        );
        
        $this->addResponsiveControl(
            'tooltip_padding',
            [
                'label' => $this->l('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .mg-omnibus-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        
        // Get product from Smarty globals (standard CE way)
        $product = null;
        if (isset($GLOBALS['smarty']->tpl_vars['product'])) {
            $product = $GLOBALS['smarty']->tpl_vars['product']->value;
        }

        // If not found in Smarty (e.g. some editor contexts), try Context
        if (!$product) {
            $context = \Context::getContext();
            if (isset($context->controller) && method_exists($context->controller, 'getProduct')) {
                $product = $context->controller->getProduct();
            }
        }
        
        // If still not found, try ID from URL (last resort for editor)
        if ((!$product || (is_object($product) && !isset($product->id))) && \Tools::getValue('id_product')) {
             $product = new \Product((int)\Tools::getValue('id_product'), false, \Context::getContext()->language->id);
        }

        if (!$product) {
            return;
        }

        // Convert object to array if needed
        $product_data = (array)$product;
        if (!isset($product_data['id_product']) && isset($product->id)) {
            $product_data['id_product'] = $product->id;
        }

        $module = \Module::getInstanceByName('mgomnibus');
        if (!$module) return;

        // Prepare overrides
        $overrides = [];
        if ($settings['display_mode'] !== 'default') $overrides['display_mode'] = $settings['display_mode'];
        if ($settings['price_type'] !== 'default') $overrides['price_type'] = $settings['price_type'];
        if ($settings['show_percent_mode'] !== 'default') $overrides['show_percent_mode'] = $settings['show_percent_mode'];
        if (!empty($settings['custom_label'])) $overrides['label'] = $settings['custom_label'];
        
        $overrides['days'] = $settings['omnibus_days'];

        // Call getOmnibusData
        $data = $module->getOmnibusData($product_data, 'product', $overrides);

        if ($data) {
            // Check Icon Visibility
            $show_icon = $settings['show_icon'];
            if ($show_icon === 'default') {
                $show_icon = \Configuration::get('OMNIBUS_SHOW_ICON') ? 'yes' : 'no';
            }
            
            // Icon HTML
            $icon_html = '';
            if ($show_icon === 'yes') {
                $icon_type = $settings['icon_type'];
                if ($icon_type === 'default') {
                    $icon_type = \Configuration::get('OMNIBUS_ICON_TYPE');
                }

                if ($icon_type === 'file') {
                    $icon_src = '';
                    if ($settings['icon_type'] === 'file' && !empty($settings['icon_image']['url'])) {
                        $icon_src = $settings['icon_image']['url'];
                    } elseif (\Configuration::get('OMNIBUS_ICON_IMAGE')) {
                        $icon_src = _MODULE_DIR_ . 'mgomnibus/views/img/' . \Configuration::get('OMNIBUS_ICON_IMAGE');
                    }
                    
                    if ($icon_src) {
                        $icon_html = '<img src="' . $icon_src . '" alt="Omnibus" />';
                    }
                } else {
                    $icon_class = '';
                    if ($settings['icon_type'] === 'class' && !empty($settings['icon_class'])) {
                        $icon_class = $settings['icon_class'];
                    } else {
                        $icon_class = \Configuration::get('OMNIBUS_ICON_CLASS');
                    }
                    
                    if (strpos($icon_class, 'fa ') !== false || strpos($icon_class, 'fa-') !== false) {
                         // Legacy / FontAwesome fallback
                         $icon_html = '<i class="' . $icon_class . '"></i>';
                    } else {
                         // Material Icons
                         $icon_html = '<i class="material-icons">' . $icon_class . '</i>';
                    }
                }
                
                // Wrap icon in tooltip if needed
                if ($icon_html) {
                    $tooltip_content = $settings['tooltip_text'];
                    if (empty($tooltip_content)) {
                        $tooltip_content = \Configuration::get('OMNIBUS_ICON_TOOLTIP', \Context::getContext()->language->id);
                    }
                    
                    if (!empty($tooltip_content)) {
                        $icon_html = '<span class="mg-omnibus-icon-wrapper">' . $icon_html . '<span class="mg-omnibus-tooltip">' . $tooltip_content . '</span></span>';
                    } else {
                        $icon_html = '<span class="mg-omnibus-icon">' . $icon_html . '</span>';
                    }
                }
            }
            
            $icon_position = $settings['icon_position'] ?? 'start';

            echo '<div class="mg-omnibus-wrapper mg-omnibus-icon-' . $icon_position . '">';
            echo '<div class="mg-omnibus-price">';
            
            if ($icon_position === 'start') {
                echo $icon_html;
            }
            
            echo '<span class="mg-omnibus-label">' . $data['label'] . '</span> ';
            echo '<span class="mg-omnibus-amount">' . $data['lowest_price_formatted'] . '</span>';
            
            if ($data['percentage'] !== null) {
                echo ' <span class="mg-omnibus-percent">' . $data['percentage'] . '%</span>';
            }
            
            if ($icon_position === 'end') {
                echo $icon_html;
            }
            
            echo '</div>';
            echo '</div>';
        }
    }
}
