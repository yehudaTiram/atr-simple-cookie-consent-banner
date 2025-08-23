<?php
/**
 * Cookie consent management functionality.
 *
 * @package ATR_Simple_Cookie_Consent_Banner
 * @since    1.0.0
 */

class ATR_Simple_Cookie_Consent_Banner_Consent {

	/**
	 * Check if user has given consent.
	 *
	 * @since    1.0.0
	 * @return   bool    Whether the user has given consent.
	 */
	public static function has_consent() {
		// Check for the consent flag cookie first (set by JavaScript)
		if ( isset( $_COOKIE['scb_consent_given'] ) && $_COOKIE['scb_consent_given'] === '1' ) {
			return true;
		}

		// Check cookies for detailed consent
		if ( isset( $_COOKIE['scb_consent'] ) ) {
			try {
				$consent = json_decode( stripslashes( $_COOKIE['scb_consent'] ), true );
				$has_consent = isset( $consent['essential'] ) && $consent['essential'] === true;
				return $has_consent;
			} catch ( Exception $e ) {
				return false;
			}
		}

		return false; // Default to false for server-side checks
	}

	/**
	 * Check if user has given consent for a specific cookie type.
	 *
	 * @since    1.0.0
	 * @param    string    $type    The cookie type to check (essential, analytics, marketing).
	 * @return   bool               Whether the user has given consent for the specific type.
	 */
	public static function has_consent_for_type( $type ) {
		if ( ! isset( $_COOKIE['scb_consent'] ) ) {
			return false;
		}

		try {
			$consent = json_decode( stripslashes( $_COOKIE['scb_consent'] ), true );
			
			// Essential cookies are always allowed if consent exists
			if ( $type === 'essential' ) {
				return isset( $consent['essential'] ) && $consent['essential'] === true;
			}
			
			// Check specific type consent
			return isset( $consent[ $type ] ) && $consent[ $type ] === true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Get user's consent preferences.
	 *
	 * @since    1.0.0
	 * @return   array|false    User's consent preferences or false if no consent.
	 */
	public static function get_consent_preferences() {
		if ( ! isset( $_COOKIE['scb_consent'] ) ) {
			return false;
		}

		try {
			$consent = json_decode( stripslashes( $_COOKIE['scb_consent'] ), true );
			return $consent;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Check if consent banner should be shown.
	 *
	 * @since    1.0.0
	 * @return   bool    Whether the consent banner should be displayed.
	 */
	public static function should_show_banner() {
		// Don't show if user has already given consent
		if ( self::has_consent() ) {
			return false;
		}

		// Don't show on privacy policy page
		$privacy_policy_url = get_privacy_policy_url();
		if ( $privacy_policy_url ) {
			$current_url = get_permalink();
			if ( $current_url === $privacy_policy_url ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get consent banner text based on current language.
	 *
	 * @since    1.0.0
	 * @return   string    The consent banner text.
	 */
	public static function get_banner_text() {
		// Default English text
		$text = __( 'We use cookies to ensure the website functions properly and improve user experience. You can choose which types of cookies to enable.', 'atr-simple-cookie-consent-banner' );
		
		// Check if we should use Hebrew
		$locale = get_locale();
		if ( strpos( $locale, 'he' ) === 0 ) {
			$text = __( 'משתמשים בעוגיות כדי להבטיח תפקוד האתר ולשפר את חוויית המשתמש. אפשר לבחור אילו סוגי עוגיות להפעיל.', 'atr-simple-cookie-consent-banner' );
		}
		
		return $text;
	}

}
