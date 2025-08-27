<?php

/**
 * Admin functionality for the plugin settings.
 *
 * This class handles the admin logic for the ATR Simple Cookie Consent Banner plugin settings.
 *
 * @link       https://atarimtr.co.il
 * @since      2.0.0
 * @author     Yehuda Tiram <yehuda@atarimtr.co.il>
 * @package    ATR_Simple_Cookie_Consent_Banner
 * @subpackage ATR_Simple_Cookie_Consent_Banner/admin
 */

/**
 * The admin-facing functionality of the plugin settings.
 *
 * @since      2.0.0
 * @package    ATR_Simple_Cookie_Consent_Banner
 * @subpackage ATR_Simple_Cookie_Consent_Banner/admin
 */
class ATR_Simple_Cookie_Consent_Banner_Settings
{

    /**
     * The ID of this plugin.
     *
     * @since      2.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The slug of this plugin.
     *
     * @since      2.0.0
     * @access   private
     * @var      string    $plugin_slug    The current version of this plugin.
     */
    private $plugin_slug;

    /**
     * Plugin settings configuration.
     *
     * @var array
     */
    private $settings;

    /**
     * Plugin options.
     *
     * @var array
     */
    private $options;

    /**
     * Fired during plugins_loaded (very very early),
     * so don't miss-use this, only actions and filters,
     * current ones speak for themselves.
     */
    public function __construct($plugin_name, $plugin_slug, $version)
    {
        $this->plugin_slug = $plugin_slug;
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Initialise settings
        add_action('admin_init', array($this, 'init'));

        // Add settings and docs pages to menu
        add_action('admin_menu', array($this, 'add_submenu_item'));

        // Add settings link to plugins page
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_slug), array($this, 'add_settings_link'));
        
        // Add admin notices hook
        add_action('admin_notices', array($this, 'display_admin_notices'));
    }

    /**
     * Initialize settings
     * @return void
     */
    public function init()
    {
        $this->settings = $this->settings_fields();
        $this->options = $this->get_options();
        $this->register_settings();
    }

    /**
     * Display admin notices
     * @return void
     */
    public function display_admin_notices()
    {
        // Get any stored notices
        $notices = get_transient('atr_scb_admin_notices');
        
        if ($notices && is_array($notices)) {
            foreach ($notices as $notice) {
                $type = isset($notice['type']) ? $notice['type'] : 'info';
                $message = isset($notice['message']) ? $notice['message'] : '';
                $dismissible = isset($notice['dismissible']) ? $notice['dismissible'] : true;
                
                if (!empty($message)) {
                    $dismissible_class = $dismissible ? 'is-dismissible' : '';
                    printf(
                        '<div class="notice notice-%s %s" id="atr-scb-notice-%s"><p>%s</p></div>',
                        esc_attr($type),
                        esc_attr($dismissible_class),
                        esc_attr(uniqid()),
                        wp_kses_post($message)
                    );
                }
            }
            
            // Clear the notices after displaying
            delete_transient('atr_scb_admin_notices');
        }
    }

    /**
     * Add an admin notice
     * @param string $message The notice message
     * @param string $type The notice type (info, success, warning, error)
     * @param bool $dismissible Whether the notice is dismissible
     * @return void
     */
    public function add_admin_notice($message, $type = 'info', $dismissible = true)
    {
        $notices = get_transient('atr_scb_admin_notices');
        if (!is_array($notices)) {
            $notices = array();
        }
        
        $notices[] = array(
            'message' => $message,
            'type' => $type,
            'dismissible' => $dismissible
        );
        
        set_transient('atr_scb_admin_notices', $notices, 60); // Store for 1 minute
    }

    /**
     * Static method to add an admin notice from anywhere in the plugin
     * @param string $message The notice message
     * @param string $type The notice type (info, success, warning, error)
     * @param bool $dismissible Whether the notice is dismissible
     * @return void
     */
    public static function add_notice($message, $type = 'info', $dismissible = true)
    {
        $notices = get_transient('atr_scb_admin_notices');
        if (!is_array($notices)) {
            $notices = array();
        }
        
        $notices[] = array(
            'message' => $message,
            'type' => $type,
            'dismissible' => $dismissible
        );
        
        set_transient('atr_scb_admin_notices', $notices, 60); // Store for 1 minute
    }

    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_submenu_item()
    {
        // Check if the ATR Core plugin is active by checking for its constant.
        if (defined('ATR_PLUGINS_MENU_SLUG') && constant('ATR_PLUGINS_MENU_SLUG')) {
            // Add as submenu under ATR Core plugin menu
            add_submenu_page(
                (string) constant('ATR_PLUGINS_MENU_SLUG'), // The slug of the parent menu, provided by the core plugin.
                __('ATR Cookie Consent Banner Options', 'atr-simple-cookie-consent-banner'),
                __('ATR Cookie Consent Banner', 'atr-simple-cookie-consent-banner'),
                'manage_options',
                $this->plugin_name,
                array($this, 'settings_page')
            );
            add_submenu_page(
                ATR_PLUGINS_MENU_SLUG,
                __('ATR Cookie Consent Banner Docs', 'atr-simple-cookie-consent-banner'),
                __('Docs (ATR Cookie Banner)', 'atr-simple-cookie-consent-banner'),
                'manage_options',
                $this->plugin_name . '_docs',
                array($this, 'docs_page')
            );
            add_submenu_page(
                ATR_PLUGINS_MENU_SLUG,
                __('Cookie Consent Banner Testing Guide', 'atr-simple-cookie-consent-banner'),
                __('Testing Guide', 'atr-simple-cookie-consent-banner'),
                'manage_options',
                $this->plugin_name . '_testing',
                array($this, 'testing_guide_page')
            );
        } else {
            // Create standalone menu when ATR Core plugin is not active
            add_menu_page(
                __('ATR Cookie Consent Banner', 'atr-simple-cookie-consent-banner'),
                __('ATR Cookie Consent Banner', 'atr-simple-cookie-consent-banner'),
                'manage_options',
                $this->plugin_name,
                array($this, 'settings_page'),
                'dashicons-shield-alt',
                30
            );
            
            // Add submenu items under the standalone menu
            add_submenu_page(
                $this->plugin_name,
                __('Cookie Consent Banner Options', 'atr-simple-cookie-consent-banner'),
                __('Settings', 'atr-simple-cookie-consent-banner'),
                'manage_options',
                $this->plugin_name,
                array($this, 'settings_page')
            );
            add_submenu_page(
                $this->plugin_name,
                __('Cookie Consent Banner Docs', 'atr-simple-cookie-consent-banner'),
                __('Documentation', 'atr-simple-cookie-consent-banner'),
                'manage_options',
                $this->plugin_name . '_docs',
                array($this, 'docs_page')
            );
            add_submenu_page(
                $this->plugin_name,
                __('Cookie Consent Banner Testing Guide', 'atr-simple-cookie-consent-banner'),
                __('Testing Guide', 'atr-simple-cookie-consent-banner'),
                'manage_options',
                $this->plugin_name . '_testing',
                array($this, 'testing_guide_page')
            );
        }
    }

    /**
     * Add settings link to plugin list table
     * @param  array $links Existing links
     * @return array 		Modified links
     */
    public function add_settings_link($links)
    {
        $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=' . $this->plugin_name)) . '">' . __('Settings', 'atr-simple-cookie-consent-banner') . '</a>';
        // Link to internal Docs admin page that renders Markdown
        $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=' . $this->plugin_name . '_docs')) . '">' . __('Docs', 'atr-simple-cookie-consent-banner') . '</a>';
        // Link to internal Testing Guide admin page
        $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=' . $this->plugin_name . '_testing')) . '">' . __('Testing Guide', 'atr-simple-cookie-consent-banner') . '</a>';
        $links[] = '<a href="http://atarimtr.com" target="_blank">More plugins by Yehuda Tiram (English)</a>';
        $links[] = '<a href="http://atarimtr.co.il" target="_blank">More plugins by Yehuda Tiram (Hebrew)</a>';
        return $links;
    }

    /**
     * Render simple Markdown to HTML for docs view (basic headings, code blocks, lists)
     */
    private function render_markdown_basic($markdown)
    {
        // Code fences
        $html = preg_replace('/```([\s\S]*?)```/m', '<pre><code>$1</code></pre>', $markdown);
        // Inline code
        $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
        // Headings ###, ##, #
        $html = preg_replace('/^###\s*(.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^##\s*(.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^#\s*(.+)$/m', '<h1>$1</h1>', $html);
        // Bold and italics
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        // Lists
        $lines = explode("\n", $html);
        $out = '';
        $in_ul = false;
        foreach ($lines as $line) {
            if (preg_match('/^\s*[-*]\s+(.+)/', $line, $m)) {
                if (! $in_ul) { $out .= '<ul>'; $in_ul = true; }
                // Allow basic inline HTML like <code>, <strong>, <em>
                $out .= '<li>' . wp_kses_post($m[1]) . '</li>';
            } else {
                if ($in_ul) { $out .= '</ul>'; $in_ul = false; }
                $out .= '<p>' . wp_kses_post($line) . '</p>';
            }
        }
        if ($in_ul) { $out .= '</ul>'; }
        return $out;
    }

    /**
     * Docs page renderer: reads local Markdown docs and displays nicely
     */
    public function docs_page()
    {
        $plugin_root = plugin_dir_path(dirname(__FILE__));
        $readme = $plugin_root . 'README.md';
        $privacy = $plugin_root . 'privacy-policy.md';
        $md1 = file_exists($readme) ? file_get_contents($readme) : __('README not found.', 'atr-simple-cookie-consent-banner');
        $md2 = file_exists($privacy) ? file_get_contents($privacy) : '';
        $html1 = $this->render_markdown_basic($md1);
        $html2 = $md2 ? $this->render_markdown_basic($md2) : '';
?>
        <style>
            /* Full-width docs layout */
            .atr-scb-docs .card { max-width: none; width: 100%; box-sizing: border-box; }
            .atr-scb-docs pre { background: #f6f7f7; padding: 12px; overflow: auto; }
            .atr-scb-docs code { font-family: Menlo, Monaco, Consolas, monospace; }
            .atr-scb-docs .card p { margin: 0 0 12px; }
        </style>
        <div class="wrap atr-scb-docs">
            <h1><?php _e('Cookie Consent Banner — Documentation', 'atr-simple-cookie-consent-banner'); ?></h1>
            <div class="card">
                <?php echo $html1; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
            <?php if ($html2) : ?>
                <h2 style="margin-top:24px;"><?php _e('Privacy Policy', 'atr-simple-cookie-consent-banner'); ?></h2>
                <div class="card">
                    <?php echo $html2; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php endif; ?>
        </div>
<?php
    }

    /**
     * Testing Guide page renderer: reads local testing guide and displays nicely
     */
    public function testing_guide_page()
    {
        $plugin_root = plugin_dir_path(dirname(__FILE__));
        $testing_guide = $plugin_root . 'TESTING-GUIDE.md';
        $md_content = file_exists($testing_guide) ? file_get_contents($testing_guide) : __('Testing Guide not found.', 'atr-simple-cookie-consent-banner');
        $html_content = $this->render_markdown_basic($md_content);
?>
        <style>
            /* Full-width testing guide layout */
            .atr-scb-testing .card { max-width: none; width: 100%; box-sizing: border-box; }
            .atr-scb-testing pre { background: #f6f7f7; padding: 12px; overflow: auto; }
            .atr-scb-testing code { font-family: Menlo, Monaco, Consolas, monospace; }
            .atr-scb-testing .card p { margin: 0 0 12px; }
            .atr-scb-testing .card h2 { margin-top: 24px; }
            .atr-scb-testing .card h3 { margin-top: 20px; }
        </style>
        <div class="wrap atr-scb-testing">
            <h1><?php _e('Cookie Consent Banner — Testing Guide', 'atr-simple-cookie-consent-banner'); ?></h1>
            <div class="card">
                <?php echo $html_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        </div>
<?php
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    private function settings_fields()
    {
        $settings['general'] = array(
            'title'                    => __('General Settings', 'atr-simple-cookie-consent-banner'),
            'description'            => __('Configure the basic behavior of the cookie consent banner.', 'atr-simple-cookie-consent-banner'),
            'fields'                => array(
                array(
                    'id' => 'enable_banner',
                    'label' => __('Enable Cookie Banner', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Show the cookie consent banner to visitors.', 'atr-simple-cookie-consent-banner'),
                    'type' => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'id' => 'banner_position',
                    'label' => __('Banner Position', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Choose where the banner appears on the page.', 'atr-simple-cookie-consent-banner'),
                    'type' => 'select',
                    'options' => array(
                        'bottom' => __('Bottom', 'atr-simple-cookie-consent-banner'),
                        'top' => __('Top', 'atr-simple-cookie-consent-banner'),
                        'overlay' => __('Overlay (Center)', 'atr-simple-cookie-consent-banner'),
                    ),
                    'default' => 'bottom',
                ),
                array(
                    'id' => 'auto_hide_delay',
                    'label' => __('Auto-hide Delay (seconds)', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Automatically hide the banner after this many seconds (0 = no auto-hide).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'number',
                    'default' => '0',
                    'placeholder' => '0',
                ),
            )
        );

        $settings['cookies'] = array(
            'title'                    => __('Cookie Categories', 'atr-simple-cookie-consent-banner'),
            'description'            => __('Configure which cookie categories to show and their default states.', 'atr-simple-cookie-consent-banner'),
            'fields'                => array(
                array(
                    'id' => 'show_analytics_cookies',
                    'label' => __('Show Analytics Cookies', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Display the analytics cookies option in the banner.', 'atr-simple-cookie-consent-banner'),
                    'type' => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'id' => 'show_marketing_cookies',
                    'label' => __('Show Marketing Cookies', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Display the marketing cookies option in the banner.', 'atr-simple-cookie-consent-banner'),
                    'type' => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'id' => 'analytics_cookies_default',
                    'label' => __('Analytics Cookies Default', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Default state for analytics cookies (checked = enabled by default).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'id' => 'marketing_cookies_default',
                    'label' => __('Marketing Cookies Default', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Default state for marketing cookies (checked = enabled by default).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
            )
        );

        $settings['styling'] = array(
            'title'                    => __('Styling & Appearance', 'atr-simple-cookie-consent-banner'),
            'description'            => __('Customize the appearance of the cookie consent banner.', 'atr-simple-cookie-consent-banner'),
            'fields'                => array(
                array(
                    'id' => 'primary_color',
                    'label' => __('Primary Color', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Main color for buttons and highlights (hex code).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'text',
                    'default' => '#0073aa',
                    'placeholder' => '#0073aa',
                ),
                array(
                    'id' => 'text_color',
                    'label' => __('Text Color', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Color for text content (hex code).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'text',
                    'default' => '#333333',
                    'placeholder' => '#333333',
                ),
                array(
                    'id' => 'background_color',
                    'label' => __('Background Color', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Background color for the banner (hex code).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'text',
                    'default' => '#ffffff',
                    'placeholder' => '#ffffff',
                ),
                array(
                    'id' => 'custom_css',
                    'label' => __('Custom CSS', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Additional CSS rules to customize the banner appearance.', 'atr-simple-cookie-consent-banner'),
                    'type' => 'textarea',
                    'default' => '',
                    'placeholder' => '/* Add your custom CSS here */',
                ),
            )
        );

        $settings['advanced'] = array(
            'title'                    => __('Advanced Options', 'atr-simple-cookie-consent-banner'),
            'description'            => __('Advanced configuration options for developers.', 'atr-simple-cookie-consent-banner'),
            'fields'                => array(
                array(
                    'id' => 'enable_debug',
                    'label' => __('Enable Debug Mode', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Show debug information in browser console (for development only).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'id' => 'cookie_expiry_days',
                    'label' => __('Cookie Expiry (days)', 'atr-simple-cookie-consent-banner'),
                    'description' => __('How long to remember user consent (in days).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'number',
                    'default' => '365',
                    'placeholder' => '365',
                ),
                array(
                    'id' => 'woocommerce_integration',
                    'label' => __('WooCommerce Integration', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Add privacy policy checkbox to WooCommerce checkout (requires WooCommerce).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'id' => 'global_form_integration',
                    'label' => __('Global Form Integration', 'atr-simple-cookie-consent-banner'),
                    'description' => __('Add privacy policy checkbox to all forms on the website (contact forms, comment forms, etc.).', 'atr-simple-cookie-consent-banner'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
            )
        );

        $settings = apply_filters('atr_scb_settings_fields', $settings);

        return $settings;
    }

    /**
     * Options getter
     * @return array Options, either saved or default ones.
     */
    public function get_options()
    {
        $options = get_option($this->plugin_name);

        if (! $options && is_array($this->settings)) {
            $options = [];

            foreach ($this->settings as $section => $data) {
                foreach ($data['fields'] as $field) {
                    // only apply a default if the field actually defines one
                    if (array_key_exists('default', $field)) {
                        $options[$field['id']] = $field['default'];
                    }
                }
            }

            add_option($this->plugin_name, $options);
        } elseif ($options && is_array($this->settings)) {
            $changed = false;

            foreach ($this->settings as $section => $data) {
                foreach ($data['fields'] as $field) {
                    if (! array_key_exists($field['id'], $options)) {
                        if (array_key_exists('default', $field)) {
                            $options[$field['id']] = $field['default'];
                            $changed = true;
                        }
                        // else: no default to apply, leave it out
                    }
                }
            }

            if ($changed) {
                update_option($this->plugin_name, $options);
            }
        }

        return $options;
    }

    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings()
    {
        if (is_array($this->settings)) {

            register_setting($this->plugin_slug, $this->plugin_slug, array($this, 'validate_fields'));

            foreach ($this->settings as $section => $data) {

                // Add section to page
                add_settings_section($section, $data['title'], array($this, 'settings_section'), $this->plugin_slug);

                foreach ($data['fields'] as $field) {

                    // Add field to page
                    add_settings_field($field['id'], $field['label'], array($this, 'display_field'), $this->plugin_slug, $section, array('field' => $field));
                }
            }
        }
    }

    public function settings_section($section)
    {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
        echo wp_kses($html, $this->get_allowed_html());
    }

    /**
     * Generate HTML for displaying fields
     * @param  array $args Field data
     * @return void
     */
    public function display_field($args)
    {

        $field = $args['field'];

        $html = '';

        $option_name = $this->plugin_slug . "[" . $field['id'] . "]";

        $data = (isset($this->options[$field['id']])) ? $this->options[$field['id']] : '';

        switch ($field['type']) {

            case 'text':
            case 'password':
            case 'number':
                $html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . esc_attr($data) . '"/>' . "\n";
                break;
            case 'text_secret':
                $html .= '<input type="password" id="' . esc_attr($field['id']) . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="" autocomplete="new-password"/>' . "\n";
                break;

            case 'textarea':
                $html .= '<textarea id="' . esc_attr($field['id']) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '">' . $data . '</textarea><br/>' . "\n";
                break;

            case 'checkbox':
                $checked = '';
                if ($data && 'on' == $data) {
                    $checked = 'checked="checked"';
                }
                // Add a hidden field to ensure a value is sent even when unchecked
                $html .= '<input type="hidden" name="' . esc_attr($option_name) . '" value="off" />';
                $html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" ' . $checked . '/>' . "\n";
                break;

            case 'checkbox_multi':
                foreach ($field['options'] as $k => $v) {
                    $checked = false;
                    if (is_array($data) && in_array($k, $data)) {
                        $checked = true;
                    }
                    $html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label></br> ';
                }
                break;

            case 'radio':
                foreach ($field['options'] as $k => $v) {
                    $checked = false;
                    if ($k == $data) {
                        $checked = true;
                    }
                    $html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label> ';
                }
                break;

            case 'select':
                $html .= '<select name="' . esc_attr($option_name) . '" id="' . esc_attr($field['id']) . '">';
                foreach ($field['options'] as $k => $v) {
                    $selected = false;
                    if ($k == $data) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
                }
                $html .= '</select> ';
                break;

            case 'select_multi':
                $html .= '<select name="' . esc_attr($option_name) . '[]" id="' . esc_attr($field['id']) . '" multiple="multiple">';
                foreach ($field['options'] as $k => $v) {
                    $selected = false;
                    if (in_array($k, $data)) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '" />' . $v . '</label> ';
                }
                $html .= '</select> ';
                break;
        }

        switch ($field['type']) {

            case 'checkbox_multi':
            case 'radio':
            case 'select_multi':
                $html .= '<br/><span class="description">' . $field['description'] . '</span>';
                break;

            default:
                $html .= '<label for="' . esc_attr($field['id']) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
                break;
        }

        echo wp_kses($html, $this->get_allowed_html());
    }

    /**
     * Validate individual settings field
     * @param  array $data Inputted value
     * @return array       Validated value
     */
    public function validate_fields($data)
    {
        // Validate color fields
        if (!empty($data['primary_color'])) {
            if (!preg_match('/^#[a-fA-F0-9]{6}$/', $data['primary_color'])) {
                add_settings_error(
                    $this->plugin_slug,
                    'invalid-primary-color',
                    __('Primary color must be a valid hex color code (e.g., #0073aa).', 'atr-simple-cookie-consent-banner'),
                    'error'
                );
                return false;
            }
        }

        if (!empty($data['text_color'])) {
            if (!preg_match('/^#[a-fA-F0-9]{6}$/', $data['text_color'])) {
                add_settings_error(
                    $this->plugin_slug,
                    'invalid-text-color',
                    __('Text color must be a valid hex color code (e.g., #333333).', 'atr-simple-cookie-consent-banner'),
                    'error'
                );
                return false;
            }
        }

        if (!empty($data['background_color'])) {
            if (!preg_match('/^#[a-fA-F0-9]{6}$/', $data['background_color'])) {
                add_settings_error(
                    $this->plugin_slug,
                    'invalid-background-color',
                    __('Background color must be a valid hex color code (e.g., #ffffff).', 'atr-simple-cookie-consent-banner'),
                    'error'
                );
                return false;
            }
        }

        // Validate numeric fields
        if (isset($data['auto_hide_delay'])) {
            $data['auto_hide_delay'] = absint($data['auto_hide_delay']);
        }

        if (isset($data['cookie_expiry_days'])) {
            $data['cookie_expiry_days'] = absint($data['cookie_expiry_days']);
            if ($data['cookie_expiry_days'] < 1) {
                $data['cookie_expiry_days'] = 1;
            }
        }

        return $data;
    }

    /**
     * Load settings page content
     * @return void
     */
    public function settings_page()
    {
        // Build page HTML output
        // If you don't need tabbed navigation just strip out everything between the <!-- Tab navigation --> tags.
?>
        <div class="wrap" id="<?php echo $this->plugin_slug; ?>">
            <h2><?php _e('Cookie Consent Banner Settings', 'atr-simple-cookie-consent-banner'); ?></h2>
            <p><?php _e('Configure the cookie consent banner behavior and appearance.', 'atr-simple-cookie-consent-banner'); ?></p>
            
            <?php
            // Example notice - you can remove this or modify it
            $this->add_admin_notice(
                sprintf(
                    __('Welcome to the Cookie Consent Banner settings! This plugin helps you comply with Israeli privacy laws. <strong>Important:</strong> Please check our website regularly for plugin updates at <a href="%s" target="_blank">%s</a> to ensure you have the latest version with security patches and new features.', 'atr-simple-cookie-consent-banner'),
                    'https://atarimtr.co.il/מדריך-התאמת-אתר-וורדפרס-לתיקון-13-לחוק-ה/',
                    'atarimtr.co.il'
                ),
                'info',
                true
            );
            
            // Debug: Check if the method is working
            echo '<!-- DEBUG: Notice method called -->';
            
            // Alternative: Display notice directly if the system isn't working
            echo '<div class="notice notice-info is-dismissible"><p>';
            printf(
                __('Welcome to the Cookie Consent Banner settings! This plugin helps you comply with Israeli privacy laws.<br><strong>Current Version:</strong> %s.<br><strong>Important:</strong> Please check our website regularly for plugin updates at <a href="%s" target="_blank">%s</a> to ensure you have the latest version with security patches and new features.', 'atr-simple-cookie-consent-banner'),
                ATR_SCB_VERSION,
                'https://atarimtr.co.il/מדריך-התאמת-אתר-וורדפרס-לתיקון-13-לחוק-ה/',
                'atarimtr.co.il'
            );
            echo '</p></div>';
            ?>

            <!-- Tab navigation starts -->
            <h2 class="nav-tab-wrapper settings-tabs hide-if-no-js">
                <?php
                foreach ($this->settings as $section => $data) {
                    echo '<a href="#' . $section . '" class="nav-tab">' . $data['title'] . '</a>';
                }
                ?>
            </h2>
            <?php $this->do_script_for_tabbed_nav(); ?>
            <!-- Tab navigation ends -->

            <form action="options.php" method="POST">
                <?php settings_fields($this->plugin_slug); ?>
                <div class="settings-container">
                    <?php do_settings_sections($this->plugin_slug); ?>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php
    }

    /**
     * Print jQuery script for tabbed navigation
     * @return void
     */
    private function do_script_for_tabbed_nav()
    {
        // Very simple jQuery logic for the tabbed navigation.
        // Delete this function if you don't need it.
        // If you have other JS assets you may merge this there.
    ?>
        <script>
            jQuery(document).ready(function($) {
                var headings = jQuery('.settings-container > h2, .settings-container > h3');
                var paragraphs = jQuery('.settings-container > p');
                var tables = jQuery('.settings-container > table');
                var triggers = jQuery('.settings-tabs a');

                triggers.each(function(i) {
                    triggers.eq(i).on('click', function(e) {
                        e.preventDefault();
                        triggers.removeClass('nav-tab-active');
                        headings.hide();
                        paragraphs.hide();
                        tables.hide();

                        triggers.eq(i).addClass('nav-tab-active');
                        headings.eq(i).show();
                        paragraphs.eq(i).show();
                        tables.eq(i).show();
                    });
                })

                triggers.eq(0).click();
            });
        </script>
<?php
    }

    private function get_allowed_html()
    {
        return array(
            'div' => array(
                'class' => array(),
                'id' => array(),
            ),
            'input' => array(
                'id' => array(),
                'class' => array(),
                'type' => array(),
                'name' => array(),
                'value' => array(),
                'checked' => array(),
                'placeholder' => array(),
                'multiple' => array(),
            ),
            'select' => array(
                'id' => array(),
                'class' => array(),
                'name' => array(),
                'multiple' => array(),
            ),
            'option' => array(
                'value' => array(),
                'selected' => array(),
            ),
            'textarea' => array(
                'id' => array(),
                'class' => array(),
                'name' => array(),
                'rows' => array(),
                'cols' => array(),
                'placeholder' => array(),
            ),
            'label' => array(
                'for' => array(),
                'class' => array(),
            ),
            'span' => array(
                'class' => array(),
                'id' => array(),
            ),
            'p' => array(
                'class' => array(),
            ),
            'br' => array(),
            'h2' => array(
                'class' => array(),
                'style' => array(),
            ),
            'h3' => array(
                'class' => array(),
            ),
            'ul' => array(
                'class' => array(),
            ),
            'li' => array(
                'class' => array(),
            ),
            'ol' => array(
                'class' => array(),
            ),
            'a' => array(
                'href' => array(),
                'class' => array(),
                'id' => array(),
                'target' => array(),
            ),
            'button' => [
                'type'    => [],
                'class'   => [],
                'id'      => [],
                'onclick' => [],
                'name'    => [],
                'value'   => [],
            ],
        );
    }
}
