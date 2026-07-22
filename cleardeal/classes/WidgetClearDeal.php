<?php
/**
 * ClearDeal - Creative Elements Widget
 *
 * Content (labels, tooltips) from module settings.
 * Full styling control in widget for page builder flexibility.
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace ClearDeal\Widget;

use CE\WidgetBase;
use CE\ControlsManager;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WidgetClearDeal extends WidgetBase
{
    const REMOTE_RENDER = true;

    public function l($string)
    {
        return \Translate::getModuleTranslation('cleardeal', $string, 'widgetcleardeal');
    }

    public function getName()
    {
        return 'cleardeal';
    }

    public function getTitle()
    {
        return 'ClearDeal';
    }

    public function getIcon()
    {
        return 'eicon-price-list';
    }

    public function getCategories()
    {
        return ['secretsauce'];
    }

    public function getKeywords()
    {
        return ['price', 'omnibus', 'history', 'lowest', 'directive'];
    }

    protected function _registerControls()
    {
        // =====================================================================
        // CONTENT SECTION - Data Settings
        // =====================================================================
        $this->startControlsSection(
            'section_content',
            [
                'label' => $this->l('Settings'),
            ]
        );

        $this->addControl(
            'info_notice',
            [
                'type' => ControlsManager::RAW_HTML,
                'raw' => '<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;padding:12px;margin-bottom:10px;font-size:12px;color:#0369a1;">' .
                    '<strong>' . $this->l('Note:') . '</strong> ' .
                    $this->l('Labels and tooltip text are configured in ClearDeal module settings.') .
                    '</div>',
            ]
        );

        $this->addControl(
            'preset',
            [
                'label' => $this->l('Preset'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => $this->l('Default'),
                    'minimal' => $this->l('Minimal'),
                    'bold' => $this->l('Bold'),
                    'custom' => $this->l('Custom'),
                ],
                'default' => 'default',
                'separator' => 'after',
            ]
        );

        $this->addControl(
            'days',
            [
                'label' => $this->l('Days'),
                'type' => ControlsManager::NUMBER,
                'default' => 30,
                'min' => 1,
                'max' => 365,
            ]
        );

        $this->addControl(
            'display_mode',
            [
                'label' => $this->l('Display Mode'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => $this->l('Module Default'),
                    'always' => $this->l('Always show'),
                    'on-discount' => $this->l('Only when discounted'),
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
                    'gross' => $this->l('Gross (with tax)'),
                    'net' => $this->l('Net (without tax)'),
                ],
                'default' => 'default',
            ]
        );

        $this->addControl(
            'show_percent',
            [
                'label' => $this->l('Show Percentage'),
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
                    'default' => $this->l('Module Default'),
                    'start' => $this->l('Before text'),
                    'end' => $this->l('After text'),
                ],
                'default' => 'default',
            ]
        );

        $this->addControl(
            'show_chart',
            [
                'label' => $this->l('Enable Chart'),
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
            'chart_trigger',
            [
                'label' => $this->l('Chart Trigger'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'default' => $this->l('Module Default'),
                    'icon' => $this->l('Icon'),
                    'price' => $this->l('Price'),
                    'all' => $this->l('Whole element'),
                ],
                'default' => 'default',
                'condition' => [
                    'show_chart!' => 'no',
                ],
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
                    '{{WRAPPER}} .cleardeal-widget-wrapper' => 'display: flex; justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsSection();

        // =====================================================================
        // STYLE: CONTAINER
        // =====================================================================
        $this->startControlsSection(
            'section_style_container',
            [
                'label' => $this->l('Container'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlBackground::getType(),
            [
                'name' => 'container_background',
                'selector' => '{{WRAPPER}} .cleardeal-widget-content',
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlBorder::getType(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .cleardeal-widget-content',
            ]
        );

        $this->addControl(
            'container_border_radius',
            [
                'label' => $this->l('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlBoxShadow::getType(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .cleardeal-widget-content',
            ]
        );

        $this->addResponsiveControl(
            'container_padding',
            [
                'label' => $this->l('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'elements_gap',
            [
                'label' => $this->l('Elements Gap'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-content' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        // =====================================================================
        // STYLE: LABEL
        // =====================================================================
        $this->startControlsSection(
            'section_style_label',
            [
                'label' => $this->l('Label'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'label_color',
            [
                'label' => $this->l('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlTypography::getType(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .cleardeal-widget-label',
            ]
        );

        $this->endControlsSection();

        // =====================================================================
        // STYLE: PRICE
        // =====================================================================
        $this->startControlsSection(
            'section_style_price',
            [
                'label' => $this->l('Price'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'price_color',
            [
                'label' => $this->l('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlTypography::getType(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .cleardeal-widget-price',
            ]
        );

        $this->endControlsSection();

        // =====================================================================
        // STYLE: PERCENTAGE BADGE
        // =====================================================================
        $this->startControlsSection(
            'section_style_percent',
            [
                'label' => $this->l('Percentage Badge'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'percent_text_color',
            [
                'label' => $this->l('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-percent' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'percent_bg_color',
            [
                'label' => $this->l('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-percent' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlTypography::getType(),
            [
                'name' => 'percent_typography',
                'selector' => '{{WRAPPER}} .cleardeal-widget-percent',
            ]
        );

        $this->addResponsiveControl(
            'percent_padding',
            [
                'label' => $this->l('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-percent' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'percent_border_radius',
            [
                'label' => $this->l('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-percent' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        // =====================================================================
        // STYLE: ICON
        // =====================================================================
        $this->startControlsSection(
            'section_style_icon',
            [
                'label' => $this->l('Icon'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'icon_color',
            [
                'label' => $this->l('Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cleardeal-widget-icon svg' => 'fill: {{VALUE}};',
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
                    'px' => ['min' => 8, 'max' => 100],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cleardeal-widget-icon img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
                ],
            ]
        );

        $this->addResponsiveControl(
            'icon_spacing',
            [
                'label' => $this->l('Spacing'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 30],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-icon-start .cleardeal-widget-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cleardeal-widget-icon-end .cleardeal-widget-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        // =====================================================================
        // STYLE: TOOLTIP
        // =====================================================================
        $this->startControlsSection(
            'section_style_tooltip',
            [
                'label' => $this->l('Tooltip'),
                'tab' => ControlsManager::TAB_STYLE,
            ]
        );

        $this->addControl(
            'tooltip_bg_color',
            [
                'label' => $this->l('Background Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-tooltip' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .cleardeal-widget-tooltip::after' => 'border-top-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'tooltip_text_color',
            [
                'label' => $this->l('Text Color'),
                'type' => ControlsManager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-tooltip' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \CE\GroupControlTypography::getType(),
            [
                'name' => 'tooltip_typography',
                'selector' => '{{WRAPPER}} .cleardeal-widget-tooltip',
            ]
        );

        $this->addResponsiveControl(
            'tooltip_width',
            [
                'label' => $this->l('Width'),
                'type' => ControlsManager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 100, 'max' => 400],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-tooltip' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'tooltip_padding',
            [
                'label' => $this->l('Padding'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'tooltip_border_radius',
            [
                'label' => $this->l('Border Radius'),
                'type' => ControlsManager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .cleardeal-widget-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        // =====================================================================
        // STYLE: CHART
        // =====================================================================
        $this->startControlsSection(
            'section_style_chart',
            [
                'label' => $this->l('Chart'),
                'tab' => ControlsManager::TAB_STYLE,
                'condition' => [
                    'show_chart!' => 'no',
                ],
            ]
        );

        $this->addControl(
            'chart_line_color',
            [
                'label' => $this->l('Line Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#635bff',
            ]
        );

        $this->addControl(
            'chart_fill_color',
            [
                'label' => $this->l('Fill Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#635bff',
            ]
        );

        $this->addControl(
            'chart_heading_modal',
            [
                'label' => $this->l('Modal Header'),
                'type' => ControlsManager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'modal_header_color',
            [
                'label' => $this->l('Header Color'),
                'type' => ControlsManager::COLOR,
                'default' => '#635bff',
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        // Get product from context
        $product = $this->getProductFromContext();

        if (!$product) {
            if (\Tools::getValue('action') === 'previewWidget') {
                $this->renderEditorPlaceholder();
            }
            return;
        }

        // Convert to array if needed
        $product_data = (array) $product;
        if (!isset($product_data['id_product']) && isset($product->id)) {
            $product_data['id_product'] = $product->id;
        }

        // Get module instance
        $module = \Module::getInstanceByName('cleardeal');
        if (!$module) {
            return;
        }

        // Prepare overrides from widget settings
        $overrides = [];
        $overrides['days'] = (int) $settings['days'];

        if ($settings['display_mode'] !== 'default') {
            $overrides['display_mode'] = $settings['display_mode'];
        }
        if ($settings['price_type'] !== 'default') {
            $overrides['price_type'] = $settings['price_type'];
        }
        if ($settings['show_percent'] !== 'default') {
            $overrides['show_percent'] = ($settings['show_percent'] === 'yes');
        }

        // Get ClearDeal data (label from module settings)
        $data = $module->getClearDealData($product_data, 'widget', $overrides);

        if (!$data) {
            return;
        }

        // Resolve settings
        $preset = $settings['preset'] ?: 'default';
        $show_icon = $this->resolveSetting($settings['show_icon'], 'CLEARDEAL_SHOW_ICON', true);
        $show_chart = $this->resolveSetting($settings['show_chart'], 'CLEARDEAL_SHOW_CHART', true);
        $icon_position = $settings['icon_position'] !== 'default'
            ? $settings['icon_position']
            : (\Configuration::get('CLEARDEAL_ICON_POSITION') ?: 'start');
        $chart_trigger = $settings['chart_trigger'] !== 'default'
            ? $settings['chart_trigger']
            : (\Configuration::get('CLEARDEAL_CHART_TRIGGER') ?: 'icon');

        // Build wrapper classes
        $wrapper_classes = ['cleardeal-widget-wrapper'];
        $wrapper_classes[] = 'cleardeal-widget-preset-' . $preset;
        if ($show_chart) {
            $wrapper_classes[] = 'cleardeal-has-chart';
            $wrapper_classes[] = 'cleardeal-chart-trigger-' . $chart_trigger;
        }

        // Build content classes
        $content_classes = ['cleardeal-widget-content'];
        if ($show_icon) {
            $content_classes[] = 'cleardeal-widget-icon-' . $icon_position;
        }

        // Product IDs for chart
        $id_product = isset($product_data['id_product']) ? (int) $product_data['id_product'] : 0;
        $id_attribute = isset($product_data['id_product_attribute']) ? (int) $product_data['id_product_attribute'] : 0;

        // Chart data attributes
        $data_attrs = 'data-product-id="' . $id_product . '" data-attribute-id="' . $id_attribute . '"';
        if ($show_chart) {
            $chart_line = !empty($settings['chart_line_color']) ? $settings['chart_line_color'] : '#635bff';
            $chart_fill = !empty($settings['chart_fill_color']) ? $settings['chart_fill_color'] : '#635bff';
            $modal_header = !empty($settings['modal_header_color']) ? $settings['modal_header_color'] : '#635bff';
            $data_attrs .= ' data-chart-line="' . htmlspecialchars($chart_line) . '"';
            $data_attrs .= ' data-chart-fill="' . htmlspecialchars($chart_fill) . '"';
            $data_attrs .= ' data-modal-header="' . htmlspecialchars($modal_header) . '"';
        }

        // Render
        echo '<div class="' . implode(' ', $wrapper_classes) . '" ' . $data_attrs . '>';
        echo '<div class="' . implode(' ', $content_classes) . '">';

        // Icon (start)
        if ($show_icon && $icon_position === 'start') {
            echo $this->renderIcon($settings['days']);
        }

        // Label (from module settings)
        echo '<span class="cleardeal-widget-label">' . htmlspecialchars($data['label']) . '</span>';

        // Price
        $price_class = ($show_chart && $chart_trigger === 'price') ? 'cleardeal-widget-price cleardeal-chart-clickable' : 'cleardeal-widget-price';
        echo '<span class="' . $price_class . '">' . $data['lowest_price_formatted'] . '</span>';

        // Percentage badge
        if ($data['percentage'] !== null) {
            $percent_class = $data['is_negative'] ? 'cleardeal-widget-percent cleardeal-widget-percent-negative' : 'cleardeal-widget-percent cleardeal-widget-percent-positive';
            echo '<span class="' . $percent_class . '">';
            echo ($data['percentage'] > 0 ? '+' : '') . $data['percentage'] . '%';
            echo '</span>';
        }

        // Icon (end)
        if ($show_icon && $icon_position === 'end') {
            echo $this->renderIcon($settings['days']);
        }

        echo '</div>';
        echo '</div>';
    }

    private function getProductFromContext()
    {
        if (isset($GLOBALS['smarty']->tpl_vars['product'])) {
            return $GLOBALS['smarty']->tpl_vars['product']->value;
        }

        $context = \Context::getContext();
        if (isset($context->controller) && method_exists($context->controller, 'getProduct')) {
            return $context->controller->getProduct();
        }

        $id_product = (int) \Tools::getValue('id_product');
        if ($id_product) {
            return new \Product($id_product, false, $context->language->id);
        }

        return null;
    }

    private function resolveSetting($widget_value, $config_key, $as_bool = false)
    {
        if ($widget_value === 'yes') {
            return true;
        }
        if ($widget_value === 'no') {
            return false;
        }
        $config_val = \Configuration::get($config_key);
        return $as_bool ? (bool) $config_val : $config_val;
    }

    private function renderIcon($days)
    {
        $icon_type = \Configuration::get('CLEARDEAL_ICON_TYPE') ?: 'material';
        $icon_html = '';

        if ($icon_type === 'file') {
            $icon_file = \Configuration::get('CLEARDEAL_ICON_IMAGE');
            if ($icon_file) {
                $icon_html = '<img src="' . _MODULE_DIR_ . 'cleardeal/views/img/' . $icon_file . '" alt="" class="cleardeal-widget-icon-img">';
            }
        } else {
            $icon_class = \Configuration::get('CLEARDEAL_ICON_CLASS') ?: 'info-circle';
            $icon_html = '<i class="bi bi-' . htmlspecialchars($icon_class) . '"></i>';
        }

        if (empty($icon_html)) {
            return '';
        }

        // Tooltip from module settings
        $tooltip = \Configuration::get('CLEARDEAL_ICON_TOOLTIP', \Context::getContext()->language->id);
        if ($tooltip && strpos($tooltip, '%s') !== false) {
            $tooltip = sprintf($tooltip, $days);
        }

        $output = '<span class="cleardeal-widget-icon">';
        $output .= $icon_html;
        if ($tooltip) {
            $output .= '<span class="cleardeal-widget-tooltip">' . htmlspecialchars($tooltip) . '</span>';
        }
        $output .= '</span>';

        return $output;
    }

    private function renderEditorPlaceholder()
    {
        $settings = $this->getSettingsForDisplay();
        $preset = $settings['preset'] ?: 'default';

        echo '<div class="cleardeal-widget-wrapper cleardeal-widget-preset-' . $preset . '">';
        echo '<div class="cleardeal-widget-content cleardeal-widget-icon-start">';
        echo '<span class="cleardeal-widget-icon"><i class="bi bi-info-circle"></i></span>';
        echo '<span class="cleardeal-widget-label">' . $this->l('Lowest price in 30 days:') . '</span>';
        echo '<span class="cleardeal-widget-price">99,99 zł</span>';
        echo '<span class="cleardeal-widget-percent cleardeal-widget-percent-negative">-15%</span>';
        echo '</div>';
        echo '</div>';
    }
}
