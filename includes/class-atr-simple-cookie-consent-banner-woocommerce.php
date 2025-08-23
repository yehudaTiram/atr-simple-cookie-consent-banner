<?php
/**
 * WooCommerce integration for the plugin.
 *
 * @package ATR_Simple_Cookie_Consent_Banner
 * @since    1.0.0
 */

class ATR_Simple_Cookie_Consent_Banner_WooCommerce {

	/**
	 * Initialize WooCommerce integration.
	 *
	 * @since    1.0.0
	 */
	public function init() {
		// Check if WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Add privacy policy checkbox to checkout
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'add_privacy_policy_checkbox' ), 20 );

		// Validate privacy policy acceptance
		add_action( 'woocommerce_checkout_process', array( $this, 'validate_privacy_policy_acceptance' ) );

		// Save privacy policy acceptance to order
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_privacy_policy_acceptance' ) );

		// Display privacy policy acceptance in admin
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_privacy_policy_acceptance' ) );
	}

	/**
	 * Add privacy policy checkbox to checkout.
	 *
	 * @since    1.0.0
	 */
	public function add_privacy_policy_checkbox() {
		$accept_text = __( 'I have read and agree to the', 'atr-simple-cookie-consent-banner' );
		$privacy_policy_text = __( 'Privacy Policy', 'atr-simple-cookie-consent-banner' );
		
		woocommerce_form_field( 'privacy_policy_accepted', array(
			'type'        => 'checkbox',
			'class'       => array( 'form-row privacy' ),
			'label_class' => array( 'woocommerce-form__label woocommerce-form__label-for-checkbox checkbox' ),
			'input_class' => array( 'woocommerce-form__input woocommerce-form__input-checkbox input-checkbox' ),
			'required'    => true,
			'label'       => sprintf(
				'%s <a href="%s" target="_blank">%s</a>',
				$accept_text,
				esc_url( get_privacy_policy_url() ),
				$privacy_policy_text
			),
		) );
	}

	/**
	 * Validate privacy policy acceptance.
	 *
	 * @since    1.0.0
	 */
	public function validate_privacy_policy_acceptance() {
		if ( empty( $_POST['privacy_policy_accepted'] ) ) {
			wc_add_notice( __( 'You must accept the privacy policy before placing the order.', 'atr-simple-cookie-consent-banner' ), 'error' );
		}
	}

	/**
	 * Save privacy policy acceptance to order.
	 *
	 * @since    1.0.0
	 * @param    int    $order_id    The order ID.
	 */
	public function save_privacy_policy_acceptance( $order_id ) {
		if ( ! empty( $_POST['privacy_policy_accepted'] ) ) {
			update_post_meta( $order_id, '_privacy_policy_accepted', 'yes' );
		}
	}

	/**
	 * Display privacy policy acceptance in admin order view.
	 *
	 * @since    1.0.0
	 * @param    WC_Order    $order    The order object.
	 */
	public function display_privacy_policy_acceptance( $order ) {
		$accepted = get_post_meta( $order->get_id(), '_privacy_policy_accepted', true );
		if ( $accepted === 'yes' ) {
			echo '<p><strong>' . __( 'Privacy Policy Acceptance:', 'atr-simple-cookie-consent-banner' ) . '</strong> ' . __( 'Yes', 'atr-simple-cookie-consent-banner' ) . '</p>';
		}
	}

}
