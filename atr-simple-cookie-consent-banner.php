<?php
/**
 * Plugin Name: ATR Simple Cookie Consent Banner for Israeli web sites
 * Description: Cookie consent banner specifically designed for Israeli websites to comply with the 13th amendment of the Privacy Protection Law (תיקון 13 לחוק הגנת הפרטיות). Handles Essential, Analytics, and Marketing cookies with proper consent management. Suitable for all Israeli businesses and websites. Use at your own risk - no warranty or liability for damages.
 * Plugin URI:        https://atarimtr.co.il
 * Version:           2.0.0
 * Author:            Yehuda Tiram
 * Author URI:        https://atarimtr.co.il/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       atr-simple-cookie-consent-banner
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to:      6.4
 * Requires PHP:      7.4
 *
 * @package ATR_Simple_Cookie_Consent_Banner
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'ATR_SCB_VERSION', '2.0.0' );

/**
 * Plugin directory path.
 */
define( 'ATR_SCB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'ATR_SCB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_atr_simple_cookie_consent_banner() {
	// Activation code here if needed
}
register_activation_hook( __FILE__, 'activate_atr_simple_cookie_consent_banner' );

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_atr_simple_cookie_consent_banner() {
	// Deactivation code here if needed
}
register_deactivation_hook( __FILE__, 'deactivate_atr_simple_cookie_consent_banner' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
class ATR_Simple_Cookie_Consent_Banner {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ATR_Simple_Cookie_Consent_Banner_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ATR_SCB_VERSION' ) ) {
			$this->version = ATR_SCB_VERSION;
		} else {
			$this->version = '2.0.0';
		}
		$this->plugin_name = 'atr-simple-cookie-consent-banner';

		// Add settings link to plugins page - must be added directly, not through loader
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ) );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_woocommerce_hooks();
		$this->define_forms_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ATR_Simple_Cookie_Consent_Banner_Loader. Orchestrates the hooks of the plugin.
	 * - ATR_Simple_Cookie_Consent_Banner_i18n. Defines internationalization functionality.
	 * - ATR_Simple_Cookie_Consent_Banner_Admin. Defines all hooks for the admin area.
	 * - ATR_Simple_Cookie_Consent_Banner_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ATR_SCB_PLUGIN_DIR . 'includes/class-atr-simple-cookie-consent-banner-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once ATR_SCB_PLUGIN_DIR . 'includes/class-atr-simple-cookie-consent-banner-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once ATR_SCB_PLUGIN_DIR . 'includes/class-atr-simple-cookie-consent-banner-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once ATR_SCB_PLUGIN_DIR . 'includes/class-atr-simple-cookie-consent-banner-public.php';

		/**
		 * The class responsible for WooCommerce integration.
		 */
		require_once ATR_SCB_PLUGIN_DIR . 'includes/class-atr-simple-cookie-consent-banner-woocommerce.php';

		/**
		 * The class responsible for cookie consent management.
		 */
		require_once ATR_SCB_PLUGIN_DIR . 'includes/class-atr-simple-cookie-consent-banner-consent.php';

		/**
		 * The class responsible for global form integration.
		 */
		require_once ATR_SCB_PLUGIN_DIR . 'includes/class-atr-simple-cookie-consent-banner-forms.php';

		$this->loader = new ATR_Simple_Cookie_Consent_Banner_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ATR_Simple_Cookie_Consent_Banner_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ATR_Simple_Cookie_Consent_Banner_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new ATR_Simple_Cookie_Consent_Banner_Admin( $this->get_plugin_name(), $this->get_plugin_slug(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new ATR_Simple_Cookie_Consent_Banner_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'block_tracking_scripts', 1 );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'block_tracking_scripts', 1 );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'inject_banner_html' );

	}

	/**
	 * Register all of the hooks related to WooCommerce integration.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_woocommerce_hooks() {

		$plugin_woocommerce = new ATR_Simple_Cookie_Consent_Banner_WooCommerce();

		$this->loader->add_action( 'init', $plugin_woocommerce, 'init' );

	}

	/**
	 * Register all of the hooks related to global form integration.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_forms_hooks() {

		$plugin_forms = new ATR_Simple_Cookie_Consent_Banner_Forms( $this->get_plugin_name() );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The slug of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The slug of the plugin.
	 */
	public function get_plugin_slug() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ATR_Simple_Cookie_Consent_Banner_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Add settings link to plugins page.
	 *
	 * @since     1.0.0
	 * @param     array    $links    The existing links array.
	 * @return    array    $links    Modified array with settings link.
	 */
	public function add_plugin_action_links( $links ) {
		$testing_guide_link = '<a href="' . plugin_dir_url( __FILE__ ) . 'TESTING-GUIDE.md" target="_blank">' . __( 'Testing Guide', 'atr-simple-cookie-consent-banner' ) . '</a>';
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=atr-simple-cookie-consent-banner' ) . '">' . __( 'Settings', 'atr-simple-cookie-consent-banner' ) . '</a>';
		array_unshift( $links, $settings_link, $testing_guide_link );
		return $links;
	}

}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_atr_simple_cookie_consent_banner() {

	$plugin = new ATR_Simple_Cookie_Consent_Banner();
	$plugin->run();

}

// The hook priority of 0 ensures this runs early
add_action( 'plugins_loaded', 'run_atr_simple_cookie_consent_banner', 0 );
