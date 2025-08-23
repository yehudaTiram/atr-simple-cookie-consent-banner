<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package ATR_Simple_Cookie_Consent_Banner
 * @since      2.0.0
 * @author     Yehuda Tiram
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      2.0.0
 * @package    ATR_Simple_Cookie_Consent_Banner
 * @subpackage ATR_Simple_Cookie_Consent_Banner/admin
 */
class ATR_Simple_Cookie_Consent_Banner_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since      2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The slug of this plugin.
	 *
	 * @since      2.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The version of this plugin.
	 *
	 * @since      2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $plugin_slug       The slug of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_slug, $version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_slug = $plugin_slug;
		$this->version = $version;

		// Initialize settings
		$this->init_settings();

	}

	/**
	 * Initialize the settings class.
	 *
	 * @since    2.0.0
	 */
	private function init_settings() {
		// Include the settings class
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-atr-simple-cookie-consent-banner-settings.php';
		
		// Initialize settings
		new ATR_Simple_Cookie_Consent_Banner_Settings( $this->plugin_name, $this->plugin_slug, $this->version );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ATR_Simple_Cookie_Consent_Banner_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ATR_Simple_Cookie_Consent_Banner_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/atr-simple-cookie-consent-banner-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ATR_Simple_Cookie_Consent_Banner_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ATR_Simple_Cookie_Consent_Banner_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/atr-simple-cookie-consent-banner-admin.js', array( 'jquery' ), $this->version, false );

	}

}
