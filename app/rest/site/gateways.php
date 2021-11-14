<?php
namespace Hammock\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Rest;
use Hammock\Model\Settings;

/**
 * Gateways rest endpoint
 *
 * @since 1.0.0
 */
class Gateways extends Rest {

	const BASE_API_ROUTE = '/gateways/';

	/**
	 * Singletone instance of the rest route.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the rest route.
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
	 * Set up the api routes
	 *
	 * @param string $namespace - the parent namespace
	 *
	 * @since 1.0.0
	 */
	public function set_up_route( $namespace ) {

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'list',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_gateways' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'list_simple',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_simple_gateways' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'settings',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_settings' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'description'       => __( 'The gateway unique key', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'update',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_gateway' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);
	}

	/**
	 * List gateways
	 *
	 * @param WP_REST_Request $request - the rest request
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_gateways( $request ) {
		$gateways = \Hammock\Services\Gateways::load_gateways();
		return $gateways;
	}

	/**
	 * List simple gateways for drop down
	 *
	 * @param WP_REST_Request $request - the rest request
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_simple_gateways( $request ) {
		$gateways = \Hammock\Services\Gateways::list_simple_gateways();
		return $gateways;
	}

	/**
	 * Get gateway setting
	 *
	 * @param WP_REST_Request $request - the rest request
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_settings( $request ) {
		$name     = $request['id'];
		$gateways = \Hammock\Services\Gateways::load_gateways();
		if ( isset( $gateways[ $name ] ) ) {
			$settings = new Settings();
			$settings = $settings->get_gateway_setting( $name );
			$form     = apply_filters( 'hammock_gateway_' . $name . '_settings', __( 'Not implemented', 'hammock' ) );
			return array(
				'settings' => $settings,
				'form'     => $form,
			);
		} else {
			return new \WP_Error( 'rest_gateway_invalid', esc_html__( 'The gateway does not exist.', 'hammock' ), array( 'status' => 404 ) );
		}
	}

	/**
	 * Update gateway
	 *
	 * @param WP_REST_Request $request - the rest request
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function update_gateway( $request ) {
		$id       = sanitize_text_field( $request['id'] );
		$gateways = \Hammock\Services\Gateways::load_gateways();
		if ( isset( $gateways[ $id ] ) ) {
			$response = apply_filters( 'hammock_gateway_' . $id . '_update_settings', array(), $request );
			return array(
				'status'   => true,
				'message'  => __( 'Gateway updated', 'hammock' ),
				'settings' => $response,
			);
		} else {
			return new \WP_Error( 'rest_gateway_invalid', esc_html__( 'The gateway does not exist.', 'hammock' ), array( 'status' => 404 ) );
		}
	}
}


