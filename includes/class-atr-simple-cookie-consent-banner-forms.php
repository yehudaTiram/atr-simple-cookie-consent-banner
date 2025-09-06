<?php
/**
 * Global Form Integration for ATR Simple Cookie Consent Banner
 *
 * @package ATR_Simple_Cookie_Consent_Banner
 * @since 2.0.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Class ATR_Simple_Cookie_Consent_Banner_Forms
 *
 * Handles the integration of privacy policy checkboxes into all forms on the website.
 *
 * @since 2.0.0
 */
class ATR_Simple_Cookie_Consent_Banner_Forms
{
    /**
     * Plugin name
     *
     * @var string
     */
    private $plugin_name;

    /**
     * Plugin options
     *
     * @var array
     */
    private $options;

    /**
     * Initialize the class and set its properties.
     *
     * @since 2.0.0
     */
    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
        $this->options = get_option($plugin_name, array());
        
        // Only proceed if global form integration is enabled
        if (!empty($this->options['global_form_integration'])) {
            $this->init_hooks();
        }
    }

    /**
     * Initialize WordPress hooks
     *
     * @since 2.0.0
     */
    private function init_hooks()
    {
        // Comment form integration
        add_filter('comment_form_defaults', array($this, 'add_privacy_checkbox_to_comments'));
        add_action('comment_post', array($this, 'validate_comment_privacy_checkbox'));
        
        // Contact Form 7 integration
        add_action('wpcf7_form_elements', array($this, 'add_privacy_checkbox_to_cf7'));
        add_filter('wpcf7_validate', array($this, 'validate_cf7_privacy_checkbox'), 10, 2);
        
        // Gravity Forms integration
        add_filter('gform_field_content', array($this, 'add_privacy_checkbox_to_gravity_forms'), 10, 2);
        add_filter('gform_entry_is_spam', array($this, 'validate_gravity_forms_privacy_checkbox'), 10, 3);
        
        // Ninja Forms integration
        add_action('ninja_forms_display_field', array($this, 'add_privacy_checkbox_to_ninja_forms'));
        add_filter('ninja_forms_process', array($this, 'validate_ninja_forms_privacy_checkbox'));
        
        // Elementor Forms integration
        add_action('elementor_pro/forms/process', array($this, 'validate_elementor_form_privacy_checkbox'), 10, 2);
        add_action('elementor_pro/forms/render_field/type=submit', array($this, 'add_privacy_checkbox_to_elementor_forms'), 10, 2);
        
        // Generic form integration using JavaScript
        add_action('wp_footer', array($this, 'inject_generic_form_script'));
    }

    /**
     * Add privacy checkbox to comment forms
     *
     * @since 2.0.0
     * @param array $defaults Comment form defaults
     * @return array Modified defaults
     */
    public function add_privacy_checkbox_to_comments($defaults)
    {
        $privacy_policy_url = get_privacy_policy_url();
        $checkbox_html = $this->get_privacy_checkbox_html($privacy_policy_url, 'comment');
        
        $defaults['comment_notes_after'] .= $checkbox_html;
        
        return $defaults;
    }

    /**
     * Validate privacy checkbox in comment forms
     *
     * @since 2.0.0
     * @param int $comment_id Comment ID
     */
    public function validate_comment_privacy_checkbox($comment_id)
    {
        if (!isset($_POST['privacy_policy_comment']) || $_POST['privacy_policy_comment'] !== '1') {
            wp_die(__('You must accept the privacy policy before posting a comment.', 'atr-simple-cookie-consent-banner'));
        }
    }

    /**
     * Add privacy checkbox to Contact Form 7
     *
     * @since 2.0.0
     * @param string $content Form content
     * @return string Modified content
     */
    public function add_privacy_checkbox_to_cf7($content)
    {
        // Skip if global form integration is disabled
        if (empty($this->options['global_form_integration'])) {
            return $content;
        }

        // Create a temporary DOM document to analyze the form
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $form_element = $dom->getElementsByTagName('form')->item(0);
        if (!$form_element) {
            return $content;
        }

        // Check if this form should get a privacy checkbox
        if (!$this->should_add_privacy_checkbox($form_element)) {
            return $content;
        }

        $privacy_policy_url = get_privacy_policy_url();
        $checkbox_html = $this->get_privacy_checkbox_html($privacy_policy_url, 'cf7');
        
        // Insert before the submit button
        $content = str_replace('[submit', $checkbox_html . '[submit', $content);
        
        return $content;
    }

    /**
     * Validate privacy checkbox in Contact Form 7
     *
     * @since 2.0.0
     * @param object $result Validation result
     * @param object $tag Form tag
     * @return object Modified result
     */
    public function validate_cf7_privacy_checkbox($result, $tag)
    {
        if (!isset($_POST['privacy_policy_cf7']) || $_POST['privacy_policy_cf7'] !== '1') {
            $result->invalidate('privacy_policy', __('You must accept the privacy policy before submitting the form.', 'atr-simple-cookie-consent-banner'));
        }
        
        return $result;
    }

    /**
     * Add privacy checkbox to Gravity Forms
     *
     * @since 2.0.0
     * @param string $content Field content
     * @param object $field Field object
     * @return string Modified content
     */
    public function add_privacy_checkbox_to_gravity_forms($content, $field)
    {
        // Skip if global form integration is disabled
        if (empty($this->options['global_form_integration'])) {
            return $content;
        }

        // Only add to submit button field
        if ($field->type === 'submit') {
            // Check if this form should get a privacy checkbox by analyzing the form
            $form = $field->form;
            if ($form && !$this->should_add_privacy_checkbox_to_gravity_form($form)) {
                return $content;
            }

            $privacy_policy_url = get_privacy_policy_url();
            $checkbox_html = $this->get_privacy_checkbox_html($privacy_policy_url, 'gravity');
            
            $content = $checkbox_html . $content;
        }
        
        return $content;
    }

    /**
     * Validate privacy checkbox in Gravity Forms
     *
     * @since 2.0.0
     * @param bool $is_spam Whether the entry is spam
     * @param array $form Form data
     * @param array $entry Entry data
     * @return bool Whether the entry is spam
     */
    public function validate_gravity_forms_privacy_checkbox($is_spam, $form, $entry)
    {
        if (!isset($_POST['privacy_policy_gravity']) || $_POST['privacy_policy_gravity'] !== '1') {
            $is_spam = true; // Mark as spam if privacy policy not accepted
        }
        
        return $is_spam;
    }

    /**
     * Add privacy checkbox to Ninja Forms
     *
     * @since 2.0.0
     * @param array $field Field data
     */
    public function add_privacy_checkbox_to_ninja_forms($field)
    {
        // Skip if global form integration is disabled
        if (empty($this->options['global_form_integration'])) {
            return;
        }

        // Only add to submit button field
        if ($field['type'] === 'submit') {
            // Check if this form should get a privacy checkbox
            $form_id = $field['form_id'] ?? 0;
            if ($form_id && !$this->should_add_privacy_checkbox_to_ninja_form($form_id)) {
                return;
            }

            $privacy_policy_url = get_privacy_policy_url();
            $checkbox_html = $this->get_privacy_checkbox_html($privacy_policy_url, 'ninja');
            
            echo $checkbox_html;
        }
    }

    /**
     * Validate privacy checkbox in Ninja Forms
     *
     * @since 2.0.0
     * @param array $form_data Form data
     * @return array Modified form data
     */
    public function validate_ninja_forms_privacy_checkbox($form_data)
    {
        if (!isset($_POST['privacy_policy_ninja']) || $_POST['privacy_policy_ninja'] !== '1') {
            $form_data['errors']['privacy_policy'] = __('You must accept the privacy policy before submitting the form.', 'atr-simple-cookie-consent-banner');
        }
        
        return $form_data;
    }

    /**
     * Add privacy checkbox to Elementor Forms
     *
     * @since 2.0.0
     * @param object $field Field object
     * @param array $field_data Field data
     */
    public function add_privacy_checkbox_to_elementor_forms($field, $field_data)
    {
        // Skip if global form integration is disabled
        if (empty($this->options['global_form_integration'])) {
            return;
        }

        // Only add to submit button field
        if ($field_data['field_type'] === 'submit') {
            // Check if this form should get a privacy checkbox
            $form_id = $field_data['form_id'] ?? 0;
            if ($form_id && !$this->should_add_privacy_checkbox_to_elementor_form($form_id)) {
                return;
            }

            $privacy_policy_url = get_privacy_policy_url();
            $checkbox_html = $this->get_privacy_checkbox_html($privacy_policy_url, 'elementor');
            
            echo $checkbox_html;
        }
    }

    /**
     * Validate privacy checkbox in Elementor Forms
     *
     * @since 2.0.0
     * @param object $record Form record
     * @param object $handler Form handler
     */
    public function validate_elementor_form_privacy_checkbox($record, $handler)
    {
        if (!isset($_POST['privacy_policy_elementor']) || $_POST['privacy_policy_elementor'] !== '1') {
            $record->add_error('privacy_policy', __('You must accept the privacy policy before submitting the form.', 'atr-simple-cookie-consent-banner'));
        }
    }

        /**
     * Inject generic JavaScript for other forms
     *
     * @since 2.0.0
     */
    public function inject_generic_form_script()
    {
        $privacy_policy_url = get_privacy_policy_url();
        $privacy_policy_text = __('Privacy Policy', 'atr-simple-cookie-consent-banner');
        $accept_text = __('I have read and agree to the', 'atr-simple-cookie-consent-banner');
        $required_text = __('You must accept the privacy policy before submitting the form.', 'atr-simple-cookie-consent-banner');
        
        ?>
        <script>
        (function() {
            // Define variables in JavaScript scope
            const privacyPolicyUrl = '<?php echo esc_js($privacy_policy_url); ?>';
            const privacyPolicyText = '<?php echo esc_js($privacy_policy_text); ?>';
            const acceptText = '<?php echo esc_js($accept_text); ?>';
            const requiredText = '<?php echo esc_js($required_text); ?>';
            
            // Smart form detection function
            function shouldAddPrivacyCheckboxToForm(form) {
                // Check for search forms
                if (isSearchForm(form)) {
                    return false;
                }
                
                // Check for login forms
                if (isLoginForm(form)) {
                    return false;
                }
                
                // Check for navigation forms
                if (isNavigationForm(form)) {
                    return false;
                }
                
                // Check for personal data collection
                return collectsPersonalData(form);
            }
            
            // Check if form is a search form
            function isSearchForm(form) {
                const action = (form.action || '').toLowerCase();
                const inputs = form.querySelectorAll('input');
                let hasSearchFields = false;
                let hasPersonalFields = false;
                
                // Check form action
                if (action.includes('search') || action.includes('query') || action.includes('find')) {
                    return true;
                }
                
                // Check input fields
                inputs.forEach(input => {
                    const name = (input.name || '').toLowerCase();
                    const type = input.type || '';
                    
                    if (name.includes('search') || name.includes('query') || name.includes('q') || name.includes('s')) {
                        hasSearchFields = true;
                    }
                    
                    if (type === 'email' || name.includes('name') || name.includes('phone') || name.includes('message')) {
                        hasPersonalFields = true;
                    }
                });
                
                // Check for textarea (personal data)
                if (form.querySelector('textarea')) {
                    hasPersonalFields = true;
                }
                
                // Check form ID and classes
                const formId = (form.id || '').toLowerCase();
                const formClass = (form.className || '').toLowerCase();
                const searchIndicators = ['search', 'searchform', 'search-form', 'searchbox'];
                
                for (const indicator of searchIndicators) {
                    if (formId.includes(indicator) || formClass.includes(indicator)) {
                        return !hasPersonalFields; // Only skip if no personal data
                    }
                }
                
                return hasSearchFields && !hasPersonalFields;
            }
            
            // Check if form is a login form
            function isLoginForm(form) {
                const inputs = form.querySelectorAll('input');
                let hasLoginFields = false;
                let hasPersonalFields = false;
                
                inputs.forEach(input => {
                    const name = (input.name || '').toLowerCase();
                    const type = input.type || '';
                    
                    if (name.includes('log') || name.includes('user') || name.includes('pass') || name.includes('pwd')) {
                        hasLoginFields = true;
                    }
                    
                    if (name.includes('name') || name.includes('phone') || name.includes('message') || type === 'file') {
                        hasPersonalFields = true;
                    }
                });
                
                // Check for textarea (personal data)
                if (form.querySelector('textarea')) {
                    hasPersonalFields = true;
                }
                
                // Check form ID and classes
                const formId = (form.id || '').toLowerCase();
                const formClass = (form.className || '').toLowerCase();
                const loginIndicators = ['login', 'loginform', 'login-form', 'signin', 'signin-form', 'authentication'];
                
                for (const indicator of loginIndicators) {
                    if (formId.includes(indicator) || formClass.includes(indicator)) {
                        return !hasPersonalFields; // Only skip if no personal data
                    }
                }
                
                return hasLoginFields && !hasPersonalFields;
            }
            
            // Check if form is a navigation form
            function isNavigationForm(form) {
                const inputs = form.querySelectorAll('input');
                const selects = form.querySelectorAll('select');
                let hasNavFields = false;
                let hasPersonalFields = false;
                
                inputs.forEach(input => {
                    const name = (input.name || '').toLowerCase();
                    const type = input.type || '';
                    
                    if (name.includes('page') || name.includes('category') || name.includes('tag')) {
                        hasNavFields = true;
                    }
                    
                    if (type === 'email' || name.includes('name') || name.includes('phone') || name.includes('message') || type === 'file') {
                        hasPersonalFields = true;
                    }
                });
                
                selects.forEach(select => {
                    const name = (select.name || '').toLowerCase();
                    if (name.includes('category') || name.includes('tag')) {
                        hasNavFields = true;
                    }
                });
                
                // Check for textarea (personal data)
                if (form.querySelector('textarea')) {
                    hasPersonalFields = true;
                }
                
                // Check form ID and classes
                const formId = (form.id || '').toLowerCase();
                const formClass = (form.className || '').toLowerCase();
                const navIndicators = ['navigation', 'nav', 'filter', 'sort', 'pagination', 'category-filter'];
                
                for (const indicator of navIndicators) {
                    if (formId.includes(indicator) || formClass.includes(indicator)) {
                        return !hasPersonalFields; // Only skip if no personal data
                    }
                }
                
                return hasNavFields && !hasPersonalFields;
            }
            
            // Check if form collects personal data
            function collectsPersonalData(form) {
                const inputs = form.querySelectorAll('input');
                const textareas = form.querySelectorAll('textarea');
                
                // Check input fields
                for (const input of inputs) {
                    const name = (input.name || '').toLowerCase();
                    const type = input.type || '';
                    
                    if (type === 'email' || 
                        name.includes('name') || 
                        name.includes('phone') || 
                        name.includes('address') || 
                        name.includes('message') || 
                        type === 'file') {
                        return true;
                    }
                }
                
                // Check for textarea
                if (textareas.length > 0) {
                    return true;
                }
                
                // Check form ID and classes for personal data indicators
                const formId = (form.id || '').toLowerCase();
                const formClass = (form.className || '').toLowerCase();
                
                const personalIndicators = [
                    'contact', 'contactform', 'contact-form', 'enquiry', 'inquiry', 'support',
                    'comment', 'commentform', 'comment-form', 'reply',
                    'newsletter', 'subscribe', 'subscription', 'signup', 'mailing-list',
                    'register', 'registration', 'sign-up', 'user-registration',
                    'order', 'checkout', 'cart', 'purchase', 'billing', 'shipping'
                ];
                
                for (const indicator of personalIndicators) {
                    if (formId.includes(indicator) || formClass.includes(indicator)) {
                        return true;
                    }
                }
                
                return false;
            }
            
            // Function to check if a form should be skipped
            function shouldSkipForm(form) {
                // Skip WooCommerce checkout forms
                const formId = (form.id || '').toLowerCase();
                const formClass = (form.className || '').toLowerCase();
                const formAction = (form.action || '').toLowerCase();
                
                // WooCommerce checkout indicators
                const woocommerceIndicators = [
                    'woocommerce-checkout', 'checkout', 'order_review', 'order_review_ajax',
                    'customer_details', 'billing', 'shipping', 'payment', 'place_order',
                    'woocommerce', 'wc-', 'checkout-form', 'order-form'
                ];
                
                for (const indicator of woocommerceIndicators) {
                    if (formId.includes(indicator) || formClass.includes(indicator) || formAction.includes(indicator)) {
                        return true;
                    }
                }
                
                // Check for WooCommerce-specific form fields
                const woocommerceFields = [
                    'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
                    'shipping_first_name', 'shipping_last_name', 'shipping_email', 'shipping_phone',
                    'order_notes', 'payment_method', 'terms', 'place_order'
                ];
                
                const inputs = form.querySelectorAll('input');
                for (const input of inputs) {
                    const name = input.name || '';
                    if (woocommerceFields.includes(name)) {
                        return true;
                    }
                }
                
                // Check if form is within WooCommerce checkout page
                let parent = form.parentElement;
                while (parent) {
                    const parentId = (parent.id || '').toLowerCase();
                    const parentClass = (parent.className || '').toLowerCase();
                    
                    if (parentId.includes('woocommerce') || parentClass.includes('woocommerce') ||
                        parentId.includes('checkout') || parentClass.includes('checkout')) {
                        return true;
                    }
                    
                    parent = parent.parentElement;
                }
                
                // Skip payment gateway iframes
                parent = form.parentElement;
                while (parent) {
                    if (parent.tagName === 'IFRAME') {
                        const src = (parent.src || '').toLowerCase();
                        const title = (parent.title || '').toLowerCase();
                        const name = (parent.name || '').toLowerCase();
                        
                        const paymentIndicators = [
                            'stripe', 'paypal', 'square', 'adyen', 'braintree', 'klarna',
                            'checkout', 'payment', 'gateway', 'processor', 'merchant',
                            'secure', 'ssl', 'https', 'payment-form', 'checkout-form'
                        ];
                        
                        for (const indicator of paymentIndicators) {
                            if (src.includes(indicator) || title.includes(indicator) || name.includes(indicator)) {
                                return true;
                            }
                        }
                    }
                    
                    parent = parent.parentElement;
                }
                
                return false;
            }
            
            function addPrivacyCheckboxToForm(form) {
                // Skip forms that already have privacy checkboxes
                if (form.querySelector('input[name*="privacy"]')) {
                    return;
                }
                
                // Skip the cookie consent banner form and any forms within the banner system
                if (form.closest('#scb-banner') || 
                    form.closest('#scb-overlay') || 
                    form.closest('.scb-settings') ||
                    form.closest('#scb-form') ||
                    form.id === 'scb-form' ||
                    form.classList.contains('scb-form') ||
                    form.querySelector('#scb-form')) {
                    return;
                }
                
                // Skip forms that contain cookie consent related content
                if (form.textContent.includes('עוגיות') || 
                    form.textContent.includes('cookies') ||
                    form.textContent.includes('Accept All') ||
                    form.textContent.includes('קבל הכל') ||
                    form.textContent.includes('Preferences') ||
                    form.textContent.includes('העדפות')) {
                    return;
                }
                
                // Smart form detection - only add to forms that collect personal data
                if (!shouldAddPrivacyCheckboxToForm(form)) {
                    return;
                }
                
                // Find submit button or submit input
                const submitBtn = form.querySelector('input[type="submit"], button[type="submit"], .submit, .submit-button, .elementor-button, .elementor-size-sm, .elementor-size-md, .elementor-size-lg, .elementor-size-xl');
                
                if (submitBtn) {
                    const checkboxHtml = `
                        <div class="privacy-policy-checkbox" style="margin: 15px 0;">
                            <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px;">
                                <input type="checkbox" name="privacy_policy_generic" value="1" required style="margin-top: 2px;">
                                <span>${acceptText} <a href="${privacyPolicyUrl}" target="_blank" rel="noopener">${privacyPolicyText}</a></span>
                            </label>
                        </div>
                    `;
                    
                    // Insert before submit button
                    submitBtn.insertAdjacentHTML('beforebegin', checkboxHtml);
                    
                    // Mark form as processed
                    form.setAttribute('data-privacy-added', 'true');
                    
                    // Add validation
                    form.addEventListener('submit', function(e) {
                        const checkbox = form.querySelector('input[name="privacy_policy_generic"]');
                        if (!checkbox || !checkbox.checked) {
                            e.preventDefault();
                            alert(requiredText);
                            return false;
                        }
                    });
                }
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                // Find all forms that don't already have privacy checkboxes
                const forms = document.querySelectorAll('form:not([data-privacy-added])');
                
                forms.forEach(function(form) {
                    // Skip WooCommerce checkout and payment forms
                    if (shouldSkipForm(form)) {
                        return;
                    }
                    addPrivacyCheckboxToForm(form);
                });
                
                // Special handling for Elementor forms that might be loaded dynamically
                if (typeof elementorFrontend !== 'undefined') {
                    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope, $) {
                        const elementorForms = $scope.find('form:not([data-privacy-added])');
                        elementorForms.each(function() {
                            // Skip the cookie consent banner form and any forms within the banner system
                            if (this.closest('#scb-banner') || 
                                this.closest('#scb-overlay') || 
                                this.closest('.scb-settings') ||
                                this.closest('#scb-form') ||
                                this.id === 'scb-form' ||
                                this.classList.contains('scb-form') ||
                                this.querySelector('#scb-form')) {
                                return;
                            }
                            addPrivacyCheckboxToForm(this);
                        });
                    });
                }
            });
        })();
        </script>
        <?php
    }

    /**
     * Generate privacy checkbox HTML
     *
     * @since 2.0.0
     * @param string $privacy_policy_url Privacy policy URL
     * @param string $form_type Form type identifier
     * @return string Checkbox HTML
     */
    private function get_privacy_checkbox_html($privacy_policy_url, $form_type)
    {
        $privacy_policy_text = __('Privacy Policy', 'atr-simple-cookie-consent-banner');
        $accept_text = __('I have read and agree to the', 'atr-simple-cookie-consent-banner');
        
        return sprintf(
            '<div class="privacy-policy-checkbox" style="margin: 15px 0;">
                <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px;">
                    <input type="checkbox" name="privacy_policy_%s" value="1" required style="margin-top: 2px;">
                    <span>%s <a href="%s" target="_blank" rel="noopener">%s</a></span>
                </label>
            </div>',
            esc_attr($form_type),
            esc_html($accept_text),
            esc_url($privacy_policy_url),
            esc_html($privacy_policy_text)
        );
    }

	/**
	 * Check if a form should get a privacy checkbox based on its content and purpose
	 *
	 * @param DOMElement $form The form element to analyze
	 * @return bool True if the form should get a privacy checkbox
	 */
	private function should_add_privacy_checkbox($form) {
		// Skip forms that are part of the cookie consent system
		if ($this->is_cookie_consent_form($form)) {
			return false;
		}

		// Skip WooCommerce checkout and payment forms
		if ($this->is_woocommerce_checkout_form($form)) {
			return false;
		}

		// Skip payment gateway iframes
		if ($this->is_payment_gateway_iframe($form)) {
			return false;
		}

		// Skip forms with obvious non-personal data purposes
		if ($this->is_non_personal_form($form)) {
			return false;
		}

		// Add checkbox to forms that collect personal data
		return $this->collects_personal_data($form);
	}

	/**
	 * Check if a form is part of the cookie consent system
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a cookie consent form
	 */
	private function is_cookie_consent_form($form) {
		// Check for cookie consent related IDs and classes
		$form_id = $form->getAttribute('id') ?: '';
		$form_class = $form->getAttribute('class') ?: '';
		
		$cookie_consent_indicators = [
			'scb-banner', 'scb-overlay', 'scb-settings', 'scb-form',
			'cookie-consent', 'cookie-banner', 'privacy-banner'
		];

		foreach ($cookie_consent_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		// Check if form contains cookie consent related text
		$form_text = strtolower($form->textContent);
		$cookie_text_indicators = [
			'עוגיות', 'cookies', 'accept all', 'קבל הכל', 'preferences', 'העדפות',
			'cookie consent', 'privacy policy', 'gdpr', 'consent'
		];

		foreach ($cookie_text_indicators as $indicator) {
			if (strpos($form_text, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a form is obviously for non-personal data collection
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a non-personal form
	 */
	private function is_non_personal_form($form) {
		// Check for search forms
		if ($this->is_search_form($form)) {
			return true;
		}

		// Check for login/authentication forms
		if ($this->is_login_form($form)) {
			return true;
		}

		// Check for simple navigation forms
		if ($this->is_navigation_form($form)) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a form is a search form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a search form
	 */
	private function is_search_form($form) {
		// Check form action and method
		$action = strtolower($form->getAttribute('action') ?: '');
		$method = strtolower($form->getAttribute('method') ?: '');
		
		// Search forms typically have these characteristics
		if (strpos($action, 'search') !== false || 
			strpos($action, 'query') !== false ||
			strpos($action, 'find') !== false) {
			return true;
		}

		// Check for search-specific input fields
		$search_inputs = $form->getElementsByTagName('input');
		$has_search_fields = false;
		$has_personal_fields = false;
		
		foreach ($search_inputs as $input) {
			$name = $input->getAttribute('name') ?: '';
			$type = $input->getAttribute('type') ?: '';
			
			// Check for search fields
			if (strpos($name, 'search') !== false || 
				strpos($name, 'query') !== false || 
				strpos($name, 'q') !== false || 
				strpos($name, 's') !== false) {
				$has_search_fields = true;
			}
			
			// Check for personal data fields
			if ($type === 'email' || 
				strpos($name, 'name') !== false || 
				strpos($name, 'phone') !== false || 
				strpos($name, 'message') !== false) {
				$has_personal_fields = true;
			}
		}
		
		// Check for textarea fields (personal data)
		$textareas = $form->getElementsByTagName('textarea');
		if ($textareas->length > 0) {
			$has_personal_fields = true;
		}
		
		if ($has_search_fields && !$has_personal_fields) {
			return true;
		}

		// Check for search-specific classes and IDs
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$search_indicators = ['search', 'searchform', 'search-form', 'searchform', 'searchbox'];
		foreach ($search_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a form is a login/authentication form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a login form
	 */
	private function is_login_form($form) {
		// Check for login-specific fields
		$login_inputs = $form->getElementsByTagName('input');
		$has_login_fields = false;
		$has_personal_fields = false;
		
		foreach ($login_inputs as $input) {
			$name = $input->getAttribute('name') ?: '';
			$type = $input->getAttribute('type') ?: '';
			
			// Check for login fields
			if (strpos($name, 'log') !== false || 
				strpos($name, 'user') !== false || 
				strpos($name, 'pass') !== false || 
				strpos($name, 'pwd') !== false) {
				$has_login_fields = true;
			}
			
			// Check for personal data fields
			if (strpos($name, 'name') !== false || 
				strpos($name, 'phone') !== false || 
				strpos($name, 'message') !== false || 
				$type === 'file') {
				$has_personal_fields = true;
			}
		}
		
		// Check for textarea fields (personal data)
		$textareas = $form->getElementsByTagName('textarea');
		if ($textareas->length > 0) {
			$has_personal_fields = true;
		}
		
		if ($has_login_fields && !$has_personal_fields) {
			return true;
		}

		// Check for login-specific classes and IDs
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$login_indicators = ['login', 'loginform', 'login-form', 'signin', 'signin-form', 'authentication'];
		foreach ($login_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a form is a simple navigation form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a navigation form
	 */
	private function is_navigation_form($form) {
		// Check for navigation-specific fields
		$nav_inputs = $form->getElementsByTagName('input');
		$selects = $form->getElementsByTagName('select');
		$has_nav_fields = false;
		$has_personal_fields = false;
		
		foreach ($nav_inputs as $input) {
			$name = $input->getAttribute('name') ?: '';
			$type = $input->getAttribute('type') ?: '';
			
			// Check for navigation fields
			if (strpos($name, 'page') !== false || 
				strpos($name, 'category') !== false || 
				strpos($name, 'tag') !== false) {
				$has_nav_fields = true;
			}
			
			// Check for personal data fields
			if ($type === 'email' || 
				strpos($name, 'name') !== false || 
				strpos($name, 'phone') !== false || 
				strpos($name, 'message') !== false || 
				$type === 'file') {
				$has_personal_fields = true;
			}
		}
		
		foreach ($selects as $select) {
			$name = $select->getAttribute('name') ?: '';
			if (strpos($name, 'category') !== false || strpos($name, 'tag') !== false) {
				$has_nav_fields = true;
			}
		}
		
		// Check for textarea fields (personal data)
		$textareas = $form->getElementsByTagName('textarea');
		if ($textareas->length > 0) {
			$has_personal_fields = true;
		}
		
		if ($has_nav_fields && !$has_personal_fields) {
			return true;
		}

		// Check for navigation-specific classes and IDs
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$nav_indicators = ['navigation', 'nav', 'filter', 'sort', 'pagination', 'category-filter'];
		foreach ($nav_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a form collects personal data
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if the form collects personal data
	 */
	private function collects_personal_data($form) {
		// Check for personal data input fields
		$inputs = $form->getElementsByTagName('input');
		$textareas = $form->getElementsByTagName('textarea');
		
		foreach ($inputs as $input) {
			$name = $input->getAttribute('name') ?: '';
			$type = $input->getAttribute('type') ?: '';
			
			if ($type === 'email' || 
				strpos($name, 'name') !== false || 
				strpos($name, 'phone') !== false || 
				strpos($name, 'address') !== false || 
				strpos($name, 'message') !== false || 
				$type === 'file') {
				return true;
			}
		}
		
		if ($textareas->length > 0) {
			return true;
		}

		// Check for specific form types that typically collect personal data
		if ($this->is_contact_form($form) || 
			$this->is_comment_form($form) || 
			$this->is_newsletter_form($form) || 
			$this->is_registration_form($form) || 
			$this->is_order_form($form)) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a form is a contact form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a contact form
	 */
	private function is_contact_form($form) {
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$contact_indicators = ['contact', 'contactform', 'contact-form', 'enquiry', 'inquiry', 'support'];
		foreach ($contact_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a form is a comment form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a comment form
	 */
	private function is_comment_form($form) {
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$comment_indicators = ['comment', 'commentform', 'comment-form', 'reply'];
		foreach ($comment_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a form is a newsletter signup form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a newsletter form
	 */
	private function is_newsletter_form($form) {
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$newsletter_indicators = ['newsletter', 'subscribe', 'subscription', 'signup', 'mailing-list'];
		foreach ($newsletter_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a form is a registration form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a registration form
	 */
	private function is_registration_form($form) {
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$registration_indicators = ['register', 'registration', 'signup', 'sign-up', 'user-registration'];
		foreach ($registration_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a form is an order form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's an order form
	 */
	private function is_order_form($form) {
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$order_indicators = ['order', 'checkout', 'cart', 'purchase', 'billing', 'shipping'];
		foreach ($order_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a Gravity Forms form should get a privacy checkbox
	 *
	 * @param object $form Gravity Forms form object
	 * @return bool True if the form should get a privacy checkbox
	 */
	private function should_add_privacy_checkbox_to_gravity_form($form) {
		// Check form title and description for indicators
		$title = strtolower($form->title ?: '');
		$description = strtolower($form->description ?: '');
		
		// Check for personal data collection indicators
		$personal_indicators = [
			'contact', 'enquiry', 'inquiry', 'support', 'help',
			'comment', 'feedback', 'review',
			'newsletter', 'subscribe', 'subscription', 'signup',
			'register', 'registration', 'sign-up',
			'order', 'checkout', 'cart', 'purchase', 'billing'
		];
		
		foreach ($personal_indicators as $indicator) {
			if (strpos($title, $indicator) !== false || strpos($description, $indicator) !== false) {
				return true;
			}
		}
		
		// Check form fields for personal data
		$fields = $form->fields;
		if ($fields) {
			foreach ($fields as $field) {
				if ($field->type === 'email' || 
					strpos($field->label, 'name') !== false ||
					strpos($field->label, 'phone') !== false ||
					strpos($field->label, 'message') !== false ||
					strpos($field->label, 'address') !== false ||
					$field->type === 'fileupload' ||
					$field->type === 'textarea') {
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * Check if a Ninja Forms form should get a privacy checkbox
	 *
	 * @param int $form_id Ninja Forms form ID
	 * @return bool True if the form should get a privacy checkbox
	 */
	private function should_add_privacy_checkbox_to_ninja_form($form_id) {
		// Get form data from Ninja Forms
		$form = ninja_forms_get_form_by_id($form_id);
		if (!$form) {
			return false;
		}
		
		// Check form title and description
		$title = strtolower($form['data']['form_title'] ?? '');
		$description = strtolower($form['data']['form_desc'] ?? '');
		
		// Check for personal data collection indicators
		$personal_indicators = [
			'contact', 'enquiry', 'inquiry', 'support', 'help',
			'comment', 'feedback', 'review',
			'newsletter', 'subscribe', 'subscription', 'signup',
			'register', 'registration', 'sign-up',
			'order', 'checkout', 'cart', 'purchase', 'billing'
		];
		
		foreach ($personal_indicators as $indicator) {
			if (strpos($title, $indicator) !== false || strpos($description, $indicator) !== false) {
				return true;
			}
		}
		
		// Check form fields for personal data
		$fields = ninja_forms_get_fields_by_form_id($form_id);
		if ($fields) {
			foreach ($fields as $field) {
				$field_type = $field['type'] ?? '';
				$field_label = strtolower($field['data']['label'] ?? '');
				
				if ($field_type === 'email' || 
					strpos($field_label, 'name') !== false ||
					strpos($field_label, 'phone') !== false ||
					strpos($field_label, 'message') !== false ||
					strpos($field_label, 'address') !== false ||
					$field_type === 'file_upload' ||
					$field_type === 'textarea') {
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * Check if an Elementor Form should get a privacy checkbox
	 *
	 * @param int $form_id Elementor Form ID
	 * @return bool True if the form should get a privacy checkbox
	 */
	private function should_add_privacy_checkbox_to_elementor_form($form_id) {
		// Get form data from Elementor
		$form_data = get_post_meta($form_id, '_elementor_data', true);
		if (!$form_data) {
			return false;
		}
		
		// Decode the form data
		$form_data = json_decode($form_data, true);
		if (!$form_data) {
			return false;
		}
		
		// Check form title and description
		$title = strtolower(get_the_title($form_id) ?: '');
		
		// Check for personal data collection indicators
		$personal_indicators = [
			'contact', 'enquiry', 'inquiry', 'support', 'help',
			'comment', 'feedback', 'review',
			'newsletter', 'subscribe', 'subscription', 'signup',
			'register', 'registration', 'sign-up',
			'order', 'checkout', 'cart', 'purchase', 'billing'
		];
		
		foreach ($personal_indicators as $indicator) {
			if (strpos($title, $indicator) !== false) {
				return true;
			}
		}
		
		// Check form fields for personal data
		if (is_array($form_data)) {
			foreach ($form_data as $element) {
				if ($this->elementor_element_has_personal_data($element)) {
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * Check if an Elementor element contains personal data fields
	 *
	 * @param array $element Elementor element data
	 * @return bool True if the element has personal data fields
	 */
	private function elementor_element_has_personal_data($element) {
		if (!is_array($element)) {
			return false;
		}
		
		// Check if this element is a form field
		if (isset($element['widgetType']) && $element['widgetType'] === 'form') {
			$settings = $element['settings'] ?? [];
			
			// Check for personal data field types
			$personal_field_types = ['email', 'textarea', 'upload'];
			foreach ($personal_field_types as $field_type) {
				if (isset($settings[$field_type . '_field_type'])) {
					return true;
				}
			}
			
			// Check for personal data field labels
			$personal_labels = ['name', 'phone', 'message', 'address'];
			foreach ($personal_labels as $label) {
				if (isset($settings['field_label']) && strpos(strtolower($settings['field_label']), $label) !== false) {
					return true;
				}
			}
		}
		
		// Recursively check child elements
		if (isset($element['elements']) && is_array($element['elements'])) {
			foreach ($element['elements'] as $child) {
				if ($this->elementor_element_has_personal_data($child)) {
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * Check if a form is a WooCommerce checkout form
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a WooCommerce checkout form
	 */
	private function is_woocommerce_checkout_form($form) {
		// Check form ID and classes for WooCommerce checkout
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		
		$woocommerce_indicators = [
			'woocommerce-checkout', 'checkout', 'order_review', 'order_review_ajax',
			'customer_details', 'billing', 'shipping', 'payment', 'place_order',
			'woocommerce', 'wc-', 'checkout-form', 'order-form'
		];
		
		foreach ($woocommerce_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || strpos($form_class, $indicator) !== false) {
				return true;
			}
		}
		
		// Check for WooCommerce-specific form fields
		$woocommerce_fields = [
			'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
			'shipping_first_name', 'shipping_last_name', 'shipping_email', 'shipping_phone',
			'order_notes', 'payment_method', 'terms', 'place_order'
		];
		
		$inputs = $form->getElementsByTagName('input');
		foreach ($inputs as $input) {
			$name = $input->getAttribute('name') ?: '';
			if (in_array($name, $woocommerce_fields)) {
				return true;
			}
		}
		
		// Check if form is within WooCommerce checkout page
		$parent = $form->parentNode;
		while ($parent && $parent->nodeType === XML_ELEMENT_NODE) {
			$parent_id = strtolower($parent->getAttribute('id') ?: '');
			$parent_class = strtolower($parent->getAttribute('class') ?: '');
			
			if (strpos($parent_id, 'woocommerce') !== false || 
				strpos($parent_class, 'woocommerce') !== false ||
				strpos($parent_id, 'checkout') !== false ||
				strpos($parent_class, 'checkout') !== false) {
				return true;
			}
			
			$parent = $parent->parentNode;
		}
		
		return false;
	}

	/**
	 * Check if a form is a payment gateway iframe
	 *
	 * @param DOMElement $form The form element to check
	 * @return bool True if it's a payment gateway iframe
	 */
	private function is_payment_gateway_iframe($form) {
		// Check if form is within an iframe
		$parent = $form->parentNode;
		while ($parent && $parent->nodeType === XML_ELEMENT_NODE) {
			if (property_exists($parent, 'tagName') && $parent->tagName === 'iframe') {
				// Check if it's a payment gateway iframe
				$src = strtolower($parent->getAttribute('src') ?: '');
				$payment_gateway_indicators = [
					'stripe', 'paypal', 'square', 'adyen', 'braintree', 'klarna',
					'checkout', 'payment', 'gateway', 'processor', 'merchant',
					'secure', 'ssl', 'https', 'payment-form', 'checkout-form'
				];
				
				foreach ($payment_gateway_indicators as $indicator) {
					if (strpos($src, $indicator) !== false) {
						return true;
					}
				}
				
				// Check iframe title and name attributes
				$title = strtolower($parent->getAttribute('title') ?: '');
				$name = strtolower($parent->getAttribute('name') ?: '');
				
				foreach ($payment_gateway_indicators as $indicator) {
					if (strpos($title, $indicator) !== false || strpos($name, $indicator) !== false) {
						return true;
					}
				}
			}
			
			$parent = $parent->parentNode;
		}
		
		// Check form itself for payment gateway indicators
		$form_id = strtolower($form->getAttribute('id') ?: '');
		$form_class = strtolower($form->getAttribute('class') ?: '');
		$form_action = strtolower($form->getAttribute('action') ?: '');
		
		$payment_indicators = [
			'stripe', 'paypal', 'square', 'adyen', 'braintree', 'klarna',
			'payment', 'gateway', 'processor', 'merchant', 'secure', 'ssl'
		];
		
		foreach ($payment_indicators as $indicator) {
			if (strpos($form_id, $indicator) !== false || 
				strpos($form_class, $indicator) !== false ||
				strpos($form_action, $indicator) !== false) {
				return true;
			}
		}
		
		return false;
	}
}
