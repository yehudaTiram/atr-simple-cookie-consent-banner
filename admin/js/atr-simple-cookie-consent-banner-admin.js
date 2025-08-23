/**
 * Admin JavaScript for ATR Simple Cookie Consent Banner
 *
 * @package ATR_Simple_Cookie_Consent_Banner
 * @since    1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Initialize admin functionality
        initAdmin();
        
        // Handle form submissions
        handleFormSubmissions();
        
        // Handle tab navigation
        handleTabs();
        
    });

    /**
     * Initialize admin functionality
     */
    function initAdmin() {
        console.log('ATR SCB Admin initialized');
        
        // Add any initialization code here
    }

    /**
     * Handle form submissions
     */
    function handleFormSubmissions() {
        $('.atr-scb-admin-form').on('submit', function(e) {
            // Add form validation or AJAX submission logic here
            console.log('Form submitted');
        });
    }

    /**
     * Handle tab navigation
     */
    function handleTabs() {
        $('.atr-scb-admin-tab').on('click', function(e) {
            e.preventDefault();
            
            var target = $(this).data('target');
            var tabContent = $('.atr-scb-admin-tab-content');
            
            // Hide all tab content
            tabContent.hide();
            
            // Show target tab content
            $('#' + target).show();
            
            // Update active tab
            $('.atr-scb-admin-tab').removeClass('active');
            $(this).addClass('active');
        });
    }

    /**
     * Show admin notice
     */
    function showNotice(message, type) {
        type = type || 'info';
        
        var notice = $('<div class="atr-scb-admin-notice notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        
        $('.wrap h1').after(notice);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            notice.fadeOut();
        }, 5000);
    }

    /**
     * Validate form fields
     */
    function validateForm(form) {
        var isValid = true;
        var requiredFields = form.find('[required]');
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                isValid = false;
            } else {
                $(this).removeClass('error');
            }
        });
        
        return isValid;
    }

})(jQuery);
