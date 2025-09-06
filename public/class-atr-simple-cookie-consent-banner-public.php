<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package ATR_Simple_Cookie_Consent_Banner
 * @since    1.0.0
 */

class ATR_Simple_Cookie_Consent_Banner_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url(__FILE__) . 'css/atr-scb.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url(__FILE__) . 'css/atr-scb.js', array(), $this->version, true );

		// Check if we're on the privacy policy page
		$is_privacy_page = false;
		$privacy_policy_url = get_privacy_policy_url();
		
		// More robust privacy page detection
		if ( $privacy_policy_url ) {
			$current_url = get_permalink();
			$privacy_url_parts = parse_url( $privacy_policy_url );
			$current_url_parts = parse_url( $current_url );
			
			// Check if current page matches privacy policy URL
			if ( $current_url === $privacy_policy_url || 
				( isset( $privacy_url_parts['path'] ) && isset( $current_url_parts['path'] ) && 
				 $privacy_url_parts['path'] === $current_url_parts['path'] ) ) {
				$is_privacy_page = true;
			}
		}

		// Pass settings to JavaScript
		wp_localize_script( $this->plugin_name, 'scbSettings', array(
			'cookieName' => 'scb_consent',
			'expiryDays' => 365,
			'siteName' => get_bloginfo( 'name' ),
			'isPrivacyPage' => $is_privacy_page,
			'privacyPolicyUrl' => $privacy_policy_url,
			'privacyNoteText' => __( '💡 You can read this page while deciding about cookies', 'atr-simple-cookie-consent-banner' ),
		) );

	}

	/**
	 * Block tracking scripts before consent is given.
	 *
	 * @since    1.0.0
	 */
	public function block_tracking_scripts() {

		// Check if user has given consent - if yes, don't block anything
		if ( $this->has_consent() ) {
			return;
		}

		// Block Google Analytics
		$this->block_google_analytics();
		
		// Block Facebook Pixel
		$this->block_facebook_pixel();
		
		// Block other common tracking scripts
		$this->block_common_tracking();
	}

	/**
	 * Check if user has given consent for cookies.
	 *
	 * @since    1.0.0
	 * @return bool
	 */
	private function has_consent() {
		
		if ( isset( $_COOKIE['scb_consent'] ) ) {
			$consent_data = json_decode( stripslashes( $_COOKIE['scb_consent'] ), true );
			
			// Check if consent has required fields and essential is true
			$has_consent = isset( $consent_data['essential'] ) && $consent_data['essential'] === true;
			return $has_consent;
		}
		
		return false;
	}

	/**
	 * Block Google Analytics scripts.
	 *
	 * @since    1.0.0
	 */
	private function block_google_analytics() {
		// Block gtag
		wp_enqueue_script( 'scb-block-gtag', '', array(), null, false );
		wp_add_inline_script( 'scb-block-gtag', '
			window.gtag = function() { return; };
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push = function() { return; };
		' );

		// Block Google Analytics
		wp_enqueue_script( 'scb-block-ga', '', array(), null, false );
		wp_add_inline_script( 'scb-block-ga', '
			window.ga = function() { return; };
			window._gaq = window._gaq || [];
			window._gaq.push = function() { return; };
		' );
	}

	/**
	 * Block Facebook Pixel scripts.
	 *
	 * @since    1.0.0
	 */
	private function block_facebook_pixel() {
		wp_enqueue_script( 'scb-block-fbq', '', array(), null, false );
		wp_add_inline_script( 'scb-block-fbq', '
			window.fbq = function() { return; };
			window._fbq = window._fbq || [];
			window._fbq.push = function() { return; };
		' );
	}

	/**
	 * Block other common tracking scripts.
	 *
	 * @since    1.0.0
	 */
	private function block_common_tracking() {
		wp_enqueue_script( 'scb-block-tracking', '', array(), null, false );
		wp_add_inline_script( 'scb-block-tracking', '
			window.track = function() { return; };
			window.tracking = function() { return; };
			window.analytics = function() { return; };
		' );
	}

	/**
	 * Inject the cookie consent banner HTML.
	 *
	 * @since    1.0.0
	 */
	public function inject_banner_html() {
		
		// Don't show banner if user already has consent
		if ( $this->has_consent() ) {
			return;
		}
		
		?>
		<div id="scb-overlay" class="scb-overlay"></div>
		<div id="scb-banner" class="scb-banner">
			<div class="scb-modal">
				<div class="scb-header">
					<button type="button" class="scb-close" onclick="scbCloseModal()">&times;</button>
				</div>
				<div class="scb-content">
					<div class="scb-text">
						<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong>
						<?php echo esc_html( __( 'We use cookies to ensure the website functions properly and improve user experience. You can choose which types of cookies to enable.', 'atr-simple-cookie-consent-banner' ) ); ?>
					</div>
					<div class="scb-actions">
						<button id="scb-btn-accept-all" class="scb-btn scb-btn-primary" type="button">
							<span class="scb-btn-text"><?php echo esc_html( __( 'Accept All', 'atr-simple-cookie-consent-banner' ) ); ?></span>
							<span class="scb-btn-loading" style="display: none;"><?php echo esc_html( __( 'Loading...', 'atr-simple-cookie-consent-banner' ) ); ?></span>
						</button>
						<button id="scb-btn-reject" class="scb-btn scb-btn-secondary" type="button">
							<?php echo esc_html( __( 'Reject Non-Essential', 'atr-simple-cookie-consent-banner' ) ); ?>
						</button>
						<button id="scb-btn-custom" class="scb-btn scb-btn-link" type="button">
							<?php echo esc_html( __( 'Preferences', 'atr-simple-cookie-consent-banner' ) ); ?>
						</button>
					</div>
					<div id="scb-settings" class="scb-settings">
						<form id="scb-form">
							<fieldset>
								<legend><?php echo esc_html( __( 'Cookie Selection', 'atr-simple-cookie-consent-banner' ) ); ?></legend>
								<label><input type="checkbox" name="essential" checked disabled> <?php echo esc_html( __( 'Essential (Required)', 'atr-simple-cookie-consent-banner' ) ); ?></label><br>
								<label><input type="checkbox" name="analytics" value="analytics"> <?php echo esc_html( __( 'Analytics (Google Analytics)', 'atr-simple-cookie-consent-banner' ) ); ?></label><br>
								<label><input type="checkbox" name="marketing" value="marketing"> <?php echo esc_html( __( 'Marketing/Advertising (Facebook/Ads)', 'atr-simple-cookie-consent-banner' ) ); ?></label><br>
								<div class="scb-actions">
									<button id="scb-btn-save" class="scb-btn scb-btn-primary" type="submit">
										<?php echo esc_html( __( 'Save Choices', 'atr-simple-cookie-consent-banner' ) ); ?>
									</button>
									<button id="scb-btn-cancel" class="scb-btn scb-btn-secondary" type="button">
										<?php echo esc_html( __( 'Cancel', 'atr-simple-cookie-consent-banner' ) ); ?>
									</button>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="scb-footer">
						<div class="scb-more" style="display: flex;justify-content: space-between;direction: ltr;">
							<a href="<?php echo esc_url( get_privacy_policy_url() ?: '#' ); ?>" target="_blank" rel="noopener" role="link"><?php echo esc_html( __( 'Privacy Policy', 'atr-simple-cookie-consent-banner' ) ); ?></a>
							<a href="https://atarimtr.co.il/" target="_blank" rel="noopener" role="link">
								<svg inkscape:version="1.2.1 (9c6d41e410, 2022-07-14)" version="1.1" id="svg2" viewBox="0 0 24 24" height="16" width="16" sodipodi:docname="atr-guten-icon.svg" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/">
									<sodipodi:namedview inkscape:window-maximized="1" inkscape:window-y="-8" inkscape:window-x="-8" inkscape:window-height="1009" inkscape:window-width="1920" inkscape:guide-bbox="true" showguides="false" showgrid="false" inkscape:current-layer="layer3" inkscape:document-units="px" inkscape:cy="12" inkscape:cx="12" inkscape:zoom="33.583333" inkscape:pageshadow="2" inkscape:pageopacity="0.0" borderopacity="1.0" bordercolor="#666666" pagecolor="#ffffff" id="base" units="px" inkscape:showpageshadow="2" inkscape:pagecheckerboard="0" inkscape:deskcolor="#d1d1d1"></sodipodi:namedview>
									<defs id="defs4"></defs>
									<g style="display:inline" inkscape:label="Layer 3" id="layer3" inkscape:groupmode="layer" transform="translate(0,-1028.3622)">
										<path sodipodi:nodetypes="ccccscc" inkscape:connector-curvature="0" id="path4182" d="m 12.076801,1046.5293 -5.1846318,3.1169 3.6096058,1.9426 c 0,0 0.572927,0.233 1.489745,0.6308 1.041163,-0.5804 4.979356,-2.5632 4.979356,-2.5632 -1.889288,-1.1423 -4.894068,-3.1271 -4.894068,-3.1271 z" style="display:inline;fill:#26a2b4;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path4186" d="m 1.7276013,1046.5237 5.150301,-2.8783 5.1643637,2.9306 -5.1370677,3.067 -4.3969393,-2.4535 c -0.5179755,-0.2845 -0.7939729,-0.4057 -0.7806577,-0.6658 z" style="display:inline;fill:#4fab2e;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4190" d="m 12.042266,1046.576 -5.1847193,-2.9627 -0.00947,-5.6896 5.1690883,2.8632 z" style="display:inline;fill:#d8d9de;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path4192" d="m 17.056384,1037.8742 v 5.7497 l 5.063042,2.8378 c 0,0 0.09576,-0.3658 0.07506,-1.5277 v -4.1967 z" style="display:inline;fill:#d8d9de;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:0.998851px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4196" d="m 17.056384,1043.6651 -4.985509,2.9452 4.910058,3.0574 5.16782,-3.3012 z" style="display:inline;fill:#012788;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4198" d="m 22.224248,1040.7869 0.0027,-4.1764 c -0.0027,-1.2226 -0.15367,-1.3928 -0.15367,-1.3928 l -5.016854,2.7161 z" style="display:inline;fill:#e0247d;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:0"></path>
										<path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4202" d="m 17.056384,1031.9634 -4.993594,3.225 5.016407,2.7568 4.994081,-2.7275 z" style="display:inline;fill:#bd0ad4;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path4210" d="m 12.090525,1029.5845 c 0,0 -0.549471,0.038 -1.250067,0.4265 -1.505646,0.9077 -3.9714327,2.3243 -3.9714327,2.3243 l 5.2137227,2.8591 4.973636,-3.231 z" style="display:inline;fill:#e96715;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path4214" d="m 6.8779023,1032.3353 c 0,0 -2.7172973,1.4804 -4.1721331,2.2468 -0.6391915,0.3653 -0.763411,0.7989 -0.763411,0.7989 l 4.9012607,2.5495 5.1952551,-2.7097 z" style="display:inline;fill:#f5b940;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4256" d="m 1.714286,1040.7869 5.1724933,-2.8531 -4.9621748,-2.5439 c 0,0 -0.2103185,0.3073 -0.2103185,1.5978 0,1.2815 0,3.7992 0,3.7992 z" style="display:inline;fill:#f8ee31;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="ccccc" inkscape:connector-curvature="0" id="path4258" d="m 12.077531,1035.1884 -5.2102955,2.7454 5.1625545,2.8531 5.026594,-2.8531 z" style="display:inline;fill:#e4412e;fill-opacity:1;fill-rule:evenodd;stroke:none;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"></path>
										<path sodipodi:nodetypes="cccccc" inkscape:connector-curvature="0" id="path532" d="m 6.8727612,1037.904 v 5.7497 l -5.0833936,2.8378 c 0,0 -0.096146,-0.3658 -0.075362,-1.5277 v -4.1967 z" style="display:inline;fill:#ffffff;fill-opacity:1;fill-rule:evenodd;stroke:#a6a6a6;stroke-width:0.101;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;stroke-dasharray:none;stroke-dashoffset:0"></path>
									</g>
									<metadata id="metadata5298">
										<rdf:rdf>
											<cc:work rdf:about="AtarimTR" atarimtr="">
												<dc:title atarimtr="">AtarimTR</dc:title>
											</cc:work>
										</rdf:rdf>
									</metadata>
								</svg>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
