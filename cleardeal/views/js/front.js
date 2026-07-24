/**
 * ClearDeal - Frontend JavaScript (Price History Chart)
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License 3.0 (AFL-3.0)
 */

(function() {
    'use strict';

    // Chart instance
    let chartInstance = null;
    let modalElement = null;

    /**
     * Initialize ClearDeal chart functionality
     */
    function init() {
        // Create modal container
        createModal();

        // Bind click events to chart triggers
        bindChartTriggers();
    }

    /**
     * Create the chart modal element
     */
    function createModal() {
        if (document.getElementById('cleardeal-chart-modal')) {
            return;
        }

        const labels = window.cleardeal_chart_labels || {};

        const modal = document.createElement('div');
        modal.id = 'cleardeal-chart-modal';
        modal.className = 'cleardeal-modal';
        modal.innerHTML = `
            <div class="cleardeal-modal-overlay"></div>
            <div class="cleardeal-modal-container">
                <div class="cleardeal-modal-header">
                    <h3 class="cleardeal-modal-title">
                        <span class="cleardeal-modal-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                        </span>
                        ${labels.title || 'Price History'}
                    </h3>
                    <button type="button" class="cleardeal-modal-close" title="${labels.close || 'Close'}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="cleardeal-modal-body">
                    <div class="cleardeal-chart-loading">
                        <div class="cleardeal-spinner"></div>
                        <span>${labels.loading || 'Loading...'}</span>
                    </div>
                    <div class="cleardeal-chart-content" style="display: none;">
                        <div class="cleardeal-chart-stats">
                            <div class="cleardeal-chart-stat">
                                <span class="cleardeal-chart-stat-label">${labels.current_price || 'Current price'}</span>
                                <span class="cleardeal-chart-stat-value" id="cleardeal-current-price">-</span>
                            </div>
                            <div class="cleardeal-chart-stat cleardeal-chart-stat-lowest">
                                <span class="cleardeal-chart-stat-label" id="cleardeal-lowest-label">${labels.lowest_price || 'Lowest price'}</span>
                                <span class="cleardeal-chart-stat-value" id="cleardeal-lowest-price">-</span>
                            </div>
                        </div>
                        <div class="cleardeal-chart-wrapper">
                            <canvas id="cleardeal-price-chart"></canvas>
                        </div>
                    </div>
                    <div class="cleardeal-chart-nodata" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p>${labels.no_data || 'No price history data available'}</p>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modalElement = modal;

        // Bind close events
        modal.querySelector('.cleardeal-modal-close').addEventListener('click', closeModal);
        modal.querySelector('.cleardeal-modal-overlay').addEventListener('click', closeModal);

        // ESC key close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalElement.classList.contains('active')) {
                closeModal();
            }
        });
    }

    /**
     * Bind click events to chart triggers
     */
    function bindChartTriggers() {
        document.addEventListener('click', function(e) {
            const wrapper = e.target.closest('.cleardeal-has-chart');
            if (!wrapper) return;

            const trigger = wrapper.classList.contains('cleardeal-chart-trigger-all') ? 'all' :
                           wrapper.classList.contains('cleardeal-chart-trigger-price') ? 'price' : 'icon';

            let shouldOpen = false;

            if (trigger === 'all') {
                shouldOpen = true;
            } else if (trigger === 'price' && (e.target.closest('.cleardeal-price') || e.target.closest('.cleardeal-widget-price'))) {
                shouldOpen = true;
            } else if (trigger === 'icon' && (e.target.closest('.cleardeal-icon') || e.target.closest('.cleardeal-widget-icon'))) {
                shouldOpen = true;
            } else if (e.target.closest('.cleardeal-chart-indicator')) {
                shouldOpen = true;
            }

            if (shouldOpen) {
                e.preventDefault();
                e.stopPropagation();
                const productId = wrapper.getAttribute('data-product-id');
                const attributeId = wrapper.getAttribute('data-attribute-id') || 0;
                openChart(productId, attributeId, wrapper);
            }
        });
    }

    /**
     * Open chart modal and fetch data
     */
    function openChart(productId, attributeId, sourceWrapper) {
        if (!modalElement) return;

        // Apply modal header color from widget data attributes
        applyModalColors(sourceWrapper);

        // Show modal
        modalElement.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Show loading, hide content
        modalElement.querySelector('.cleardeal-chart-loading').style.display = 'flex';
        modalElement.querySelector('.cleardeal-chart-content').style.display = 'none';
        modalElement.querySelector('.cleardeal-chart-nodata').style.display = 'none';

        // Destroy existing chart
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        // Fetch chart data
        fetchChartData(productId, attributeId, sourceWrapper);
    }

    /**
     * Apply modal header colors from widget data attributes
     */
    function applyModalColors(sourceWrapper) {
        if (!modalElement || !sourceWrapper) return;

        var modalHeaderColor = sourceWrapper.getAttribute('data-modal-header');
        if (!modalHeaderColor) return;

        var header = modalElement.querySelector('.cleardeal-modal-header');
        if (!header) return;

        // Generate gradient end color (lighter shade)
        var gradientEnd = adjustColor(modalHeaderColor, 30);
        var textColor = getContrastColor(modalHeaderColor);
        var isLight = (textColor === '#ffffff');

        header.style.background = 'linear-gradient(135deg, ' + modalHeaderColor + ' 0%, ' + gradientEnd + ' 100%)';
        header.style.color = textColor;

        var title = header.querySelector('.cleardeal-modal-title');
        if (title) title.style.color = textColor;

        var icon = header.querySelector('.cleardeal-modal-icon');
        if (icon) {
            icon.style.background = isLight ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.1)';
        }
        var iconSvg = header.querySelector('.cleardeal-modal-icon svg');
        if (iconSvg) iconSvg.style.color = textColor;

        var closeBtn = header.querySelector('.cleardeal-modal-close');
        if (closeBtn) {
            closeBtn.style.background = isLight ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.1)';
            closeBtn.style.color = textColor;
        }
    }

    /**
     * Adjust hex color brightness
     */
    function adjustColor(hex, amount) {
        hex = hex.replace('#', '');
        var r = Math.min(255, parseInt(hex.substring(0, 2), 16) + amount);
        var g = Math.min(255, parseInt(hex.substring(2, 4), 16) + amount);
        var b = Math.min(255, parseInt(hex.substring(4, 6), 16) + amount);
        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }

    /**
     * Get contrast color (black or white) for given background
     */
    function getContrastColor(hex) {
        hex = hex.replace('#', '');
        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);
        var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        return luminance > 0.5 ? '#1a1f36' : '#ffffff';
    }

    /**
     * Close chart modal
     */
    function closeModal() {
        if (!modalElement) return;

        modalElement.classList.remove('active');
        document.body.style.overflow = '';

        // Destroy chart to free memory
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
    }

    /**
     * Fetch chart data from server
     */
    function fetchChartData(productId, attributeId, sourceWrapper) {
        const url = window.cleardeal_ajax_url + '?action=getChartData&id_product=' + productId + '&id_product_attribute=' + attributeId;

        fetch(url)
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success && result.data) {
                    renderChart(result.data, sourceWrapper);
                } else {
                    showNoData();
                }
            })
            .catch(function(error) {
                console.error('ClearDeal: Error fetching chart data', error);
                showNoData();
            });
    }

    /**
     * Render the price chart
     */
    function renderChart(data, sourceWrapper) {
        if (!modalElement) return;

        const labels = window.cleardeal_chart_labels || {};

        // Hide loading, show content
        modalElement.querySelector('.cleardeal-chart-loading').style.display = 'none';
        modalElement.querySelector('.cleardeal-chart-content').style.display = 'block';
        modalElement.querySelector('.cleardeal-chart-nodata').style.display = 'none';

        // Update stats
        const currencySymbol = data.currency.symbol || '';
        document.getElementById('cleardeal-current-price').textContent = formatPrice(data.current_price, currencySymbol);
        document.getElementById('cleardeal-lowest-price').textContent = data.lowest_price ? formatPrice(data.lowest_price, currencySymbol) : '-';

        // Update lowest price label with days
        const lowestLabel = labels.lowest_price || 'Lowest price in %s days';
        document.getElementById('cleardeal-lowest-label').textContent = lowestLabel.replace('%s', data.days);

        // Check if we have data points
        if (!data.labels || data.labels.length === 0) {
            showNoData();
            return;
        }

        // Render chart
        const ctx = document.getElementById('cleardeal-price-chart').getContext('2d');

        // Chart colors - widget data attributes override global config
        const chartColors = window.cleardeal_chart_colors || {};
        const lineColor = (sourceWrapper && sourceWrapper.getAttribute('data-chart-line')) || chartColors.line || '#635bff';
        const fillColor = (sourceWrapper && sourceWrapper.getAttribute('data-chart-fill')) || chartColors.fill || '#635bff';

        // Convert hex to rgba for gradient
        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
        }

        const gradientFill = ctx.createLinearGradient(0, 0, 0, 300);
        gradientFill.addColorStop(0, hexToRgba(fillColor, 0.15));
        gradientFill.addColorStop(1, hexToRgba(fillColor, 0));

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: labels.price_label || 'Price',
                    data: data.prices,
                    borderColor: lineColor,
                    backgroundColor: gradientFill,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: lineColor,
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: lineColor,
                    pointHoverBorderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 31, 54, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return formatPrice(context.raw, currencySymbol);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#697386',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#697386',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return formatPrice(value, currencySymbol);
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Show no data message
     */
    function showNoData() {
        if (!modalElement) return;

        modalElement.querySelector('.cleardeal-chart-loading').style.display = 'none';
        modalElement.querySelector('.cleardeal-chart-content').style.display = 'none';
        modalElement.querySelector('.cleardeal-chart-nodata').style.display = 'flex';
    }

    /**
     * Format price with currency symbol
     */
    function formatPrice(value, symbol) {
        const formatted = parseFloat(value).toFixed(2);
        // Simple formatting - symbol after value for most EU currencies
        return formatted + ' ' + symbol;
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
