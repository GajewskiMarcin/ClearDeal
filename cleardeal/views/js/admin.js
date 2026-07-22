/**
 * ClearDeal - Admin JavaScript
 *
 * @author    Marcin Gajewski <kontakt@marcingajewski.pl>
 * @copyright 2025 marcingajewski.pl
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // =========================================================================
    // Language Switcher with Animation
    // =========================================================================
    const langButtons = document.querySelectorAll('.cleardeal-lang-btn');
    const langInputs = document.querySelectorAll('.cleardeal-lang-input');

    langButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const langId = this.getAttribute('data-lang');
            const parentCard = this.closest('.cleardeal-card');
            const cardInputs = parentCard ? parentCard.querySelectorAll('.cleardeal-lang-input') : langInputs;

            // Update active button in this card only
            const cardButtons = parentCard ? parentCard.querySelectorAll('.cleardeal-lang-btn') : langButtons;
            cardButtons.forEach(function(b) {
                b.classList.remove('active');
            });
            this.classList.add('active');

            // Animate out current inputs
            cardInputs.forEach(function(input) {
                if (input.style.display !== 'none') {
                    input.classList.add('switching');
                }
            });

            // After animation, switch inputs
            setTimeout(function() {
                cardInputs.forEach(function(input) {
                    input.classList.remove('switching');
                    if (input.getAttribute('data-lang') === langId) {
                        input.style.display = '';
                        input.classList.add('switching-in');
                        setTimeout(function() {
                            input.classList.remove('switching-in');
                        }, 250);
                    } else {
                        input.style.display = 'none';
                    }
                });
            }, 150);
        });
    });

    // =========================================================================
    // Icon Settings Toggle
    // =========================================================================
    const showIconToggle = document.getElementById('show_icon_toggle');
    const iconOptions = document.querySelectorAll('.icon-options');

    if (showIconToggle) {
        showIconToggle.addEventListener('change', function() {
            iconOptions.forEach(function(el) {
                el.style.display = showIconToggle.checked ? '' : 'none';
            });
        });
    }

    // =========================================================================
    // Icon Type Toggle
    // =========================================================================
    const iconTypeRadios = document.querySelectorAll('input[name="CLEARDEAL_ICON_TYPE"]');
    const bootstrapOptions = document.querySelectorAll('.icon-bootstrap-options');
    const fileOptions = document.querySelectorAll('.icon-file-options');

    iconTypeRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            const isBootstrap = this.value === 'bootstrap';

            bootstrapOptions.forEach(function(el) {
                el.style.display = isBootstrap ? '' : 'none';
            });

            fileOptions.forEach(function(el) {
                el.style.display = isBootstrap ? 'none' : '';
            });
        });
    });

    // =========================================================================
    // Chart Settings Toggle
    // =========================================================================
    const showChartToggle = document.getElementById('show_chart_toggle');
    const chartOptions = document.querySelectorAll('.chart-options');

    if (showChartToggle) {
        showChartToggle.addEventListener('change', function() {
            chartOptions.forEach(function(el) {
                el.style.display = showChartToggle.checked ? '' : 'none';
            });
        });
    }

    // =========================================================================
    // Icon Preview
    // =========================================================================
    const iconClassInput = document.querySelector('input[name="CLEARDEAL_ICON_CLASS"]');
    const iconPreview = document.getElementById('icon_preview');

    if (iconClassInput && iconPreview) {
        iconClassInput.addEventListener('input', function() {
            const value = this.value || 'info-circle';
            iconPreview.className = 'bi bi-' + value;
        });
    }

    // =========================================================================
    // Import Form Toggle
    // =========================================================================
    const importBtn = document.getElementById('import_btn');
    const importForm = document.getElementById('import_form');
    const closeImport = document.getElementById('close_import');

    if (importBtn && importForm) {
        importBtn.addEventListener('click', function() {
            importForm.style.display = importForm.style.display === 'none' ? '' : 'none';
        });
    }

    if (closeImport && importForm) {
        closeImport.addEventListener('click', function() {
            importForm.style.display = 'none';
        });
    }

    // =========================================================================
    // Check All Checkbox
    // =========================================================================
    const checkAll = document.getElementById('check_all');
    const logCheckboxes = document.querySelectorAll('input[name="log_ids[]"]');

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const isChecked = this.checked;
            logCheckboxes.forEach(function(cb) {
                cb.checked = isChecked;
            });
        });
    }

    // Update "check all" when individual checkboxes change
    logCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(logCheckboxes).every(function(c) {
                return c.checked;
            });
            const someChecked = Array.from(logCheckboxes).some(function(c) {
                return c.checked;
            });

            if (checkAll) {
                checkAll.checked = allChecked;
                checkAll.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // =========================================================================
    // Copy to Clipboard (CRON URL)
    // =========================================================================
    const copyButtons = document.querySelectorAll('.cleardeal-btn-copy');

    copyButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-copy');
            const targetInput = document.getElementById(targetId);

            if (targetInput) {
                targetInput.select();
                targetInput.setSelectionRange(0, 99999);

                navigator.clipboard.writeText(targetInput.value).then(function() {
                    btn.classList.add('copied');
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.textContent = 'check';
                    }

                    setTimeout(function() {
                        btn.classList.remove('copied');
                        if (icon) {
                            icon.textContent = 'content_copy';
                        }
                    }, 2000);
                });
            }
        });
    });

    // =========================================================================
    // Icon Position Preview Toggle
    // =========================================================================
    const iconPositionSelect = document.querySelector('select[name="CLEARDEAL_ICON_POSITION"]');
    const previewContent = document.getElementById('preview_content');

    if (iconPositionSelect && previewContent) {
        iconPositionSelect.addEventListener('change', function() {
            const position = this.value;
            // Remove both classes first
            previewContent.classList.remove('cleardeal-preview-icon-start', 'cleardeal-preview-icon-end');
            // Add the appropriate class
            previewContent.classList.add('cleardeal-preview-icon-' + position);
        });
    }

    // =========================================================================
    // Appearance Tab - Setup
    // =========================================================================
    const colorPickers = document.querySelectorAll('.cleardeal-color-picker');
    const colorTexts = document.querySelectorAll('.cleardeal-color-text');
    const presetLabels = document.querySelectorAll('.cleardeal-preset');
    const useCustomColorsCheckbox = document.getElementById('use_custom_colors');
    const customColorsSection = document.getElementById('custom_colors_section');
    const colorGroups = document.querySelectorAll('.cleardeal-color-group');
    const useGradientDefault = document.getElementById('use_gradient_default');
    const gradientOptionsDefault = document.querySelector('.gradient-options-default');

    // Helper: Calculate contrast color for text on colored background
    function getContrastColor(hexColor) {
        const hex = hexColor.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        return luminance > 0.5 ? '#1a1f36' : '#ffffff';
    }

    // Helper: Adjust color brightness (similar to PHP adjustColor)
    function adjustColor(hexColor, percent) {
        const hex = hexColor.replace('#', '');
        let r = parseInt(hex.substr(0, 2), 16);
        let g = parseInt(hex.substr(2, 2), 16);
        let b = parseInt(hex.substr(4, 2), 16);

        // Lighten the color
        r = Math.min(255, Math.round(r + (255 - r) * (percent / 100)));
        g = Math.min(255, Math.round(g + (255 - g) * (percent / 100)));
        b = Math.min(255, Math.round(b + (255 - b) * (percent / 100)));

        return '#' + [r, g, b].map(function(x) {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    }

    // Helper: Get current preset
    function getCurrentPreset() {
        const selected = document.querySelector('input[name="CLEARDEAL_STYLE_PRESET"]:checked');
        return selected ? selected.value : 'default';
    }

    // Helper: Get color value by ID
    function getColorValue(id, defaultVal) {
        const el = document.getElementById(id);
        return el ? el.value : defaultVal;
    }

    // =========================================================================
    // Appearance Tab - Color Picker Sync
    // =========================================================================
    colorPickers.forEach(function(picker) {
        picker.addEventListener('input', function() {
            const textInput = document.getElementById(this.id + '_text');
            if (textInput) {
                textInput.value = this.value.toUpperCase();
            }
            updateLivePreview();
        });
    });

    colorTexts.forEach(function(textInput) {
        textInput.addEventListener('input', function() {
            let value = this.value;
            if (value && !value.startsWith('#')) {
                value = '#' + value;
            }
            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                const colorId = this.getAttribute('data-color');
                const picker = document.getElementById(colorId);
                if (picker) {
                    picker.value = value;
                }
                updateLivePreview();
            }
        });
    });

    // =========================================================================
    // Appearance Tab - Preset Selection
    // =========================================================================
    function updateColorGroupVisibility() {
        const preset = getCurrentPreset();
        colorGroups.forEach(function(group) {
            const forPreset = group.getAttribute('data-for-preset');
            group.style.display = (forPreset === preset) ? '' : 'none';
        });
    }

    presetLabels.forEach(function(label) {
        label.addEventListener('click', function() {
            presetLabels.forEach(function(l) {
                l.classList.remove('active');
            });
            this.classList.add('active');
            updateColorGroupVisibility();
            updateLivePreview();
        });
    });

    // =========================================================================
    // Appearance Tab - Custom Colors Toggle
    // =========================================================================
    if (useCustomColorsCheckbox && customColorsSection) {
        useCustomColorsCheckbox.addEventListener('change', function() {
            customColorsSection.style.display = this.checked ? '' : 'none';
            updateLivePreview();
        });
    }

    // =========================================================================
    // Appearance Tab - Gradient Toggle (Default preset)
    // =========================================================================
    if (useGradientDefault && gradientOptionsDefault) {
        useGradientDefault.addEventListener('change', function() {
            gradientOptionsDefault.style.display = this.checked ? '' : 'none';
            updateLivePreview();
        });
    }

    // =========================================================================
    // Appearance Tab - Live Preview Update
    // =========================================================================
    function updateLivePreview() {
        const preview = document.getElementById('preview_content');
        const modalHeader = document.getElementById('preview_modal_header');
        if (!preview) return;

        const preset = getCurrentPreset();
        const useCustom = useCustomColorsCheckbox && useCustomColorsCheckbox.checked;

        // Preview elements
        const icon = preview.querySelector('.cleardeal-preview-icon');
        const labelEl = preview.querySelector('.cleardeal-preview-label-text');
        const price = preview.querySelector('.cleardeal-preview-price');
        const percent = preview.querySelector('.cleardeal-preview-percent');

        // Default values
        let bgColor = '#f6f9fc';
        let borderStyle = '1px solid #e3e8ee';
        let iconColor = '#635bff';
        let labelColor = '#697386';
        let priceColor = '#1a1f36';
        let badgeBg = '#fce4ec';
        let badgeColor = '#c41535';

        if (preset === 'default') {
            if (useCustom) {
                const bg = getColorValue('color_background', '#f6f9fc');
                const border = getColorValue('color_border', '#e3e8ee');
                const primary = getColorValue('color_primary', '#635bff');
                const badge = getColorValue('color_badge', '#fce4ec');
                const useGradient = useGradientDefault && useGradientDefault.checked;
                const gradientEnd = getColorValue('color_gradient_end', '#e0e5eb');

                if (useGradient) {
                    bgColor = 'linear-gradient(135deg, ' + bg + ' 0%, ' + gradientEnd + ' 100%)';
                } else {
                    bgColor = bg;
                }
                borderStyle = '1px solid ' + border;
                iconColor = primary;
                badgeBg = badge;
                badgeColor = getContrastColor(badge);
            } else {
                // Default preset defaults
                bgColor = '#f6f9fc';
                borderStyle = '1px solid #e3e8ee';
                iconColor = '#635bff';
                badgeBg = '#fce4ec';
                badgeColor = '#c41535';
            }
        } else if (preset === 'minimal') {
            bgColor = 'transparent';
            borderStyle = 'none';
            if (useCustom) {
                iconColor = getColorValue('color_minimal_icon', '#9ca3af');
                labelColor = getColorValue('color_minimal_label', '#9ca3af');
                priceColor = getColorValue('color_minimal_price', '#374151');
                badgeColor = getColorValue('color_minimal_badge', '#c41535');
            } else {
                iconColor = '#9ca3af';
                labelColor = '#9ca3af';
                priceColor = '#374151';
                badgeColor = '#c41535';
            }
            badgeBg = 'transparent';
        } else if (preset === 'bold') {
            borderStyle = 'none';
            if (useCustom) {
                const start = getColorValue('color_bold_start', '#6366f1');
                const end = getColorValue('color_bold_end', '#a855f7');
                bgColor = 'linear-gradient(135deg, ' + start + ' 0%, ' + end + ' 100%)';
                badgeBg = 'rgba(255, 255, 255, 0.2)';
            } else {
                bgColor = 'linear-gradient(135deg, #6366f1 0%, #a855f7 100%)';
                badgeBg = 'rgba(255, 255, 255, 0.2)';
            }
            iconColor = 'rgba(255, 255, 255, 0.9)';
            labelColor = 'rgba(255, 255, 255, 0.8)';
            priceColor = '#fff';
            badgeColor = '#fff';
        }

        // Apply to preview
        preview.style.background = bgColor;
        preview.style.border = borderStyle;
        if (icon) icon.style.color = iconColor;
        if (labelEl) labelEl.style.color = labelColor;
        if (price) price.style.color = priceColor;
        if (percent) {
            percent.style.background = badgeBg;
            percent.style.color = badgeColor;
        }

        // Apply to modal header preview - uses dedicated color setting
        if (modalHeader) {
            const headerInner = modalHeader.querySelector('.cleardeal-preview-modal-header-inner');
            if (headerInner) {
                // Get dedicated modal header color
                const modalHeaderColor = getColorValue('color_modal_header', '#635bff');
                const modalHeaderGradientEnd = adjustColor(modalHeaderColor, 30);
                const modalBg = 'linear-gradient(135deg, ' + modalHeaderColor + ' 0%, ' + modalHeaderGradientEnd + ' 100%)';
                const modalTextColor = getContrastColor(modalHeaderColor);

                headerInner.style.background = modalBg;
                headerInner.style.color = modalTextColor;

                const title = headerInner.querySelector('.cleardeal-preview-modal-title');
                const iconWrap = headerInner.querySelector('.cleardeal-preview-modal-icon');
                const iconEl = headerInner.querySelector('.cleardeal-preview-modal-icon i');
                const closeBtn = headerInner.querySelector('.cleardeal-preview-modal-close');
                const closeIcon = headerInner.querySelector('.cleardeal-preview-modal-close i');

                if (title) title.style.color = modalTextColor;
                if (iconEl) iconEl.style.color = modalTextColor;
                if (closeIcon) closeIcon.style.color = modalTextColor;

                // Adjust icon/close backgrounds
                const isLight = modalTextColor !== '#ffffff';
                if (iconWrap) iconWrap.style.background = isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.2)';
                if (closeBtn) closeBtn.style.background = isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.15)';
            }
        }

        // Apply to tooltip preview
        const tooltipPreview = document.getElementById('tooltip_preview');
        if (tooltipPreview) {
            const tooltipBg = getColorValue('color_tooltip_bg', '#1a1f36');
            const tooltipTextColor = getContrastColor(tooltipBg);
            tooltipPreview.style.background = tooltipBg;
            tooltipPreview.style.color = tooltipTextColor;
        }

        // Apply to chart preview
        const chartLine = document.getElementById('chartLine');
        const chartFill = document.getElementById('chartFill');
        const chartGradientStart = document.getElementById('chartGradientStart');
        const chartGradientEnd = document.getElementById('chartGradientEnd');

        if (chartLine) {
            const lineColor = getColorValue('color_chart_line', '#635bff');
            chartLine.setAttribute('stroke', lineColor);
        }

        if (chartGradientStart && chartGradientEnd) {
            const fillColor = getColorValue('color_chart_fill', '#635bff');
            chartGradientStart.style.stopColor = fillColor;
            chartGradientEnd.style.stopColor = fillColor;
        }
    }

    // Initialize on page load
    updateColorGroupVisibility();
    updateLivePreview();

    // =========================================================================
    // Category Exclusions Management
    // =========================================================================
    const categorySelect = document.getElementById('category_select');
    const addCategoryBtn = document.getElementById('add_category_btn');
    const excludedTagsContainer = document.getElementById('excluded_tags');
    const excludedCategoriesInput = document.getElementById('excluded_categories_input');

    if (addCategoryBtn && categorySelect && excludedTagsContainer && excludedCategoriesInput) {
        // Add category
        addCategoryBtn.addEventListener('click', function() {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryId = categorySelect.value;
            const categoryName = selectedOption ? selectedOption.textContent.replace(/^[—\s]+/, '').trim() : '';

            if (!categoryId) return;

            // Get current excluded categories
            let excluded = JSON.parse(excludedCategoriesInput.value || '[]');

            // Check if already excluded
            if (excluded.includes(parseInt(categoryId))) return;

            // Add to list
            excluded.push(parseInt(categoryId));
            excludedCategoriesInput.value = JSON.stringify(excluded);

            // Remove "no exclusions" message if present
            const noExclusions = excludedTagsContainer.querySelector('.cleardeal-no-exclusions');
            if (noExclusions) {
                noExclusions.remove();
            }

            // Add tag element
            const tag = document.createElement('span');
            tag.className = 'cleardeal-tag';
            tag.innerHTML = categoryName +
                '<button type="button" class="cleardeal-tag-remove" data-id="' + categoryId + '">' +
                '<i class="material-icons">close</i></button>';
            excludedTagsContainer.appendChild(tag);

            // Disable option in select
            selectedOption.disabled = true;

            // Reset select
            categorySelect.value = '';

            // Add remove handler
            tag.querySelector('.cleardeal-tag-remove').addEventListener('click', removeCategoryHandler);
        });

        // Remove category handler
        function removeCategoryHandler() {
            const categoryId = parseInt(this.getAttribute('data-id'));
            const tag = this.closest('.cleardeal-tag');

            // Remove from hidden input
            let excluded = JSON.parse(excludedCategoriesInput.value || '[]');
            excluded = excluded.filter(function(id) { return id !== categoryId; });
            excludedCategoriesInput.value = JSON.stringify(excluded);

            // Remove tag
            tag.remove();

            // Re-enable option in select
            const option = categorySelect.querySelector('option[value="' + categoryId + '"]');
            if (option) {
                option.disabled = false;
            }

            // Show "no exclusions" if empty
            if (excluded.length === 0) {
                const noExclusions = document.createElement('span');
                noExclusions.className = 'cleardeal-no-exclusions';
                noExclusions.textContent = 'No categories excluded';
                excludedTagsContainer.appendChild(noExclusions);
            }
        }

        // Bind remove handlers to existing tags
        const existingRemoveButtons = excludedTagsContainer.querySelectorAll('.cleardeal-tag-remove');
        existingRemoveButtons.forEach(function(btn) {
            btn.addEventListener('click', removeCategoryHandler);
        });
    }

    // =========================================================================
    // Logs Tab - Filters Toggle
    // =========================================================================
    const filterToggleBtn = document.getElementById('filter_toggle_btn');
    const filtersPanel = document.getElementById('filters_panel');

    if (filterToggleBtn && filtersPanel) {
        filterToggleBtn.addEventListener('click', function() {
            const isVisible = filtersPanel.style.display !== 'none';
            filtersPanel.style.display = isVisible ? 'none' : 'block';
            filterToggleBtn.classList.toggle('active', !isVisible);
        });
    }

    // =========================================================================
    // System Info Toggle (Support Tab)
    // =========================================================================
    const systemInfoToggle = document.getElementById('system_info_toggle');
    const systemInfoContent = document.getElementById('system_info_content');

    if (systemInfoToggle && systemInfoContent) {
        systemInfoToggle.addEventListener('click', function() {
            const isVisible = systemInfoContent.style.display !== 'none';
            systemInfoContent.style.display = isVisible ? 'none' : 'block';
            systemInfoToggle.classList.toggle('active', !isVisible);
        });
    }
});
