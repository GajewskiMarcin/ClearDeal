/**
 * MG Omnibus Admin JS
 */

$(document).ready(function () {
    // 1. Main Tab Switching
    $('.tab-link').on('click', function (e) {
        e.preventDefault();

        // Remove active class from all tabs and contents
        $('.tab-link').removeClass('active');
        $('.tab-content').removeClass('active');

        // Add active class to clicked tab
        $(this).addClass('active');

        // Show corresponding content
        var tabId = $(this).data('tab');
        $('#tab-' + tabId).addClass('active');
    });

    // 3. Matrix UI Logic (Appearance Settings)
    var currentDevice = 'DESKTOP';
    var currentView = 'PRODUCT';

    function updateMatrixVisibility() {
        // Fade out all matrix groups
        $('.matrix-group').fadeOut(200, function () {
            // After fade out, show the specific group with fade in
            $('.matrix-group[data-device="' + currentDevice + '"][data-view="' + currentView + '"]').fadeIn(300);
        });

        // Update active states for device tabs
        $('.view-tab').removeClass('active');
        $('.view-tab[data-device="' + currentDevice + '"]').addClass('active');

        // Update selectors
        $('.matrix-view-selector').val(currentView);
    }

    // Device Tab Click (Desktop/Mobile)
    $(document).on('click', '.view-tab', function (e) {
        e.preventDefault();
        currentDevice = $(this).data('device');
        updateMatrixVisibility();
    });

    // View Selector Change (Product/Listing/Quick)
    $(document).on('change', '.matrix-view-selector', function () {
        currentView = $(this).val();
        updateMatrixVisibility();
    });

    // Initialize Matrix
    updateMatrixVisibility();

    // 4. Color Picker Sync
    $(document).on('input', '.color-picker', function () {
        $(this).closest('.input-group').find('.color-text').val($(this).val());
    });

    $(document).on('input', '.color-text', function () {
        $(this).closest('.input-group').find('.color-picker').val($(this).val());
    });
});

// Helper Functions (Global Scope)
function regenerateToken() {
    if (confirm('Are you sure you want to regenerate the CRON token? You will need to update your CRON tasks.')) {
        var form = $('form.form-horizontal');
        var input = $('<input>').attr({
            type: 'hidden',
            name: 'regenerateCronToken',
            value: '1'
        });
        form.append(input);
        form.submit();
    }
}

function copyToClipboard(btn) {
    var input = $(btn).closest('.input-group').find('input');
    input.select();
    document.execCommand("copy");

    // Visual feedback
    var originalText = $(btn).text();
    $(btn).text('Copied!');
    setTimeout(function () {
        $(btn).text(originalText);
    }, 2000);
}
