<?php
namespace Hammock\Gateway\Paypal;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Gateway;

class PayPal extends Gateway {

	/**
	 * Singletone instance of the addon.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the addon.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Gateway init
	 * Used to load any required classes
	 */
	public function init() {
		$this->id = 'paypal';
	}

	/**
	 * Register gateway
	 * Register a key value pair of gateways
	 *
	 * @param array $gateways - the current list of gateways
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register( $gateways ) {
		if ( ! isset( $gateways['paypal'] ) ) {
			$gateways['paypal'] = array(
				'name' => __( 'PayPal Standard Gateway', 'hammock' ),
				'logo' => HAMMOCK_ASSETS_URL . '/img/gateways/paypal.png',
			);
		}
		return $gateways;
	}

	/**
	 * Init gateway
	 * Initialize the gateway
	 *
	 * @since 1.0.0
	 */
	public function init_gateway() {

	}

	/**
	 * Gateway settings
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function settings( $data = '' ) {
		$view       = new \Hammock\View\Backend\Gateways\PayPal();
		$settings   = $this->settings->get_gateway_setting( $this->id );
		$view->data = array(
			'settings' => $settings,
		);
		return $view->render( true );
	}

	/**
	 * Update gateway settings
	 *
	 * @param array $response
	 * @param array $data - the post data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_settings( $response = array(), $data ) {
		$settings                     		 = $this->settings->get_gateway_setting( $this->id );
		$settings['enabled']          		 = isset( $data[ $this->id ] ) ? true : false;
		$settings['mode']             		 = sanitize_text_field( $data['paypal_mode'] );
		$settings['paypal_email']  			 = sanitize_text_field( $data['paypal_email'] );
		$settings['paypal_merchant_id']  	 = sanitize_text_field( $data['paypal_merchant_id'] );
		$settings['test_paypal_email']  	 = sanitize_text_field( $data['test_paypal_email'] );
		$settings['test_paypal_merchant_id'] = sanitize_text_field( $data['test_paypal_merchant_id'] );
		$this->settings->set_gateway_setting( $this->id, $settings );
		$this->settings->save();
		return $settings;
	}

	/**
	 * Check if currency is supported
	 * 
	 * @param string $currency The current site currency code.
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function is_currency_supported( $currency ) {
		return in_array(
            $currency,
			array(
				'AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK',
				'NZD', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD',
			)
        );
	}

	/**
	 * Handle the ipn callbacks
	 *
	 * @since 1.0.0
	 */
	public function ipn_notify() {

	}

	/**
	 * Render the payment form
	 *
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 *
	 * @return string
	 */
	public function render_payment_form( $invoice ) {
		return '';
	}

	/**
	 * Render the subscription payment update form
	 *
	 * @param \Hammock\Model\Plan $plan - the plan model
	 *
	 * @return string
	 */
	public function render_payment_update_form( $plan ) {
		return '';
	}

	/**
	 * Process Payment
	 *
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $invoice ) {
		$invoice->status = Transactions::STATUS_PAID;
		$invoice->save();
	}

	/**
	 * Process Refund
	 *
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 * @param \Hammock\Model\Plan    $plan - the plan model
	 * @param double                 $amount - the amount
	 *
	 * @since 1.0.0
	 */
	public function process_refund( $invoice, $plan, $amount ) {

	}


	/**
	 * Process Cancel
	 * Called when a plan is cancelled
	 *
	 * @param \Hammock\Model\Plan $plan - the plan model
	 *
	 * @since 1.0.0
	 */
	public function process_cancel( $plan ) {

	}

	/**
	 * Process Pause
	 * Called when a plan is paused
	 *
	 * @param \Hammock\Model\Plan $plan - the plan model
	 *
	 * @since 1.0.0
	 */
	public function process_pause( $plan ) {

	}

	/**
	 * Process Resume
	 * Called when a plan is resumed
	 *
	 * @param \Hammock\Model\Plan $plan - the plan model
	 *
	 * @since 1.0.0
	 */
	public function process_resume( $plan ) {

	}

	/**
	 * Handle payment return
	 * This is called after a payment gateway redirects
	 *
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 *
	 * @since 1.0.0
	 */
	public function handle_return( $invoice ) {

	}

	/**
	 * Handle member delete
	 *
	 * @param object $member - the current member
	 *
	 * @since 1.0.0
	 */
	public function handle_member_delete( $member ) {

	}
}

