<?php
namespace Hammock\Gateway\Manual;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Gateway;

/**
 * Manual gateway
 *
 * @since 1.0.0
 */
class Manual extends Gateway {


	/**
	 * What type of transactions are supported
	 * This tells the frontend wht to show depending on the plan purchased
	 *
	 * single - single payments, non-recurring
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $supports = array(
		'single',
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
		$this->id = 'manual';
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
		if ( ! isset( $gateways['manual'] ) ) {
			$gateways['manual'] = array(
				'name' => __( 'Manual Gateway', 'hammock' ),
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
		$view       = new \Hammock\View\Backend\Gateways\Manual();
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
		$settings                        = $this->settings->get_gateway_setting( $this->id );
		$settings['enabled']             = isset( $data[ $this->id ] ) ? true : false;
		$settings['manual_title']        = sanitize_text_field( $data['manual_title'] );
		$settings['manual_instructions'] = sanitize_text_field( $data['manual_instructions'] );
		$this->settings->set_gateway_setting( $this->id, $settings );
		$this->settings->save();
		return $settings;
	}
}

