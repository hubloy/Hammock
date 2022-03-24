<?php
namespace Hammock\Gateway\Paypal;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Gateway;

class PayPal extends Gateway {

	/**
	 * What type of transactions are supported
	 * This tells the frontend what to show depending on the plan purchased
	 *
	 * single - single payments, non-recurring
	 * recurring - subscription payments
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $supports = array(
		'single', 'recurring',
	);

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
		$settings                     		= $this->settings->get_gateway_setting( $this->id );
		$settings['enabled']          		= isset( $data[ $this->id ] ) ? true : false;
		$settings['mode']             		= sanitize_text_field( $data['paypal_mode'] );
		$settings['paypal_username']  		= sanitize_text_field( $data['paypal_username'] );
		$settings['paypal_password']  		= sanitize_text_field( $data['paypal_password'] );
		$settings['paypal_signature'] 		= sanitize_text_field( $data['paypal_signature'] );
		$settings['test_paypal_username']  	= sanitize_text_field( $data['test_paypal_username'] );
		$settings['test_paypal_password']  	= sanitize_text_field( $data['test_paypal_password'] );
		$settings['test_paypal_signature'] 	= sanitize_text_field( $data['test_paypal_signature'] );
		$this->settings->set_gateway_setting( $this->id, $settings );
		$this->settings->save();
		return $settings;
	}
}
