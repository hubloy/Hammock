<?php
namespace Hammock\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Rest;
use Hammock\Model\Settings;

/**
 * Addons rest endpoint
 *
 * @since 1.0.0
 */
class Addons extends Rest {

	const BASE_API_ROUTE = '/addons/';


	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the plugin.
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
				'callback'            => array( $this, 'list_addons' ),
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
					'name' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'description'       => __( 'The addon unique key', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'settings/update',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'update_settings' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'description'       => __( 'The addon unique key', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'toggle',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'toggle' ),
					'permission_callback' => array( $this, 'validate_request' ),
					'args'                => array(
						'name'    => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'type'              => 'string',
							'description'       => __( 'The addon unique key', 'hammock' ),
						),
						'enabled' => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
							'type'              => 'integer',
							'description'       => __( 'Enabled status either 1 or 0', 'hammock' ),
						),
					),
				),
			)
		);

		//Custom actions
		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'action',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'inner_action' ),
					'permission_callback' => array( $this, 'validate_request' ),
					'args'                => array(
						'id'    => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'type'              => 'string',
							'description'       => __( 'The addon unique key', 'hammock' ),
						),
						'action' => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'type'              => 'string',
							'description'       => __( 'The custom addon action to call', 'hammock' ),
						),
					),
				),
			)
		);
	}

	/**
	 * List addons
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_addons( $request ) {
		$addons = \Hammock\Services\Addons::load_addons();
		return $addons;
	}

	/**
	 * Get addon setting
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_settings( $request ) {
		$name   = $request['name'];
		$addons = \Hammock\Services\Addons::load_addons();
		if ( isset( $addons[ $name ] ) ) {
			$settings 	= new Settings();
			$settings 	= $settings->get_addon_setting( $name );
			$active 	= apply_filters( 'hammock_get_addon_' . $name . '_active', true );
			return array(
				'settings' 	=> $settings,
				'enabled'  	=> $settings['enabled'] && $active,
				'active'	=> $active
			);
		} else {
			return new \WP_Error( 'rest_addon_invalid', esc_html__( 'The addon does not exist.', 'hammock' ), array( 'status' => 404 ) );
		}
	}

	/**
	 * Update settings
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function update_settings( $request ) {
		$id = $request['id'];
		$response = apply_filters( 'hammock_addon_' . $id . '_update_settings', array( 'status' => false, 'message' => __( 'Addon not found', 'hammock' ) ), $request );
		return $response;
	}

	/**
	 * Toggle Enabled status
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function toggle( $request ) {
		$name    = $request['name'];
		$enabled = $request['enabled'];
		$addons  = \Hammock\Services\Addons::load_addons();
		if ( isset( $addons[ $name ] ) ) {
			$settings                  = new Settings();
			$addon_settings            = $settings->get_addon_setting( $name );
			$addon_settings['enabled'] = $enabled;
			$settings->set_addon_setting( $name, $addon_settings );
			$settings->save();
			return new \WP_REST_Response(
				array(
					'success' => true,
					'enabled' => $enabled,
				),
				200
			);
		} else {
			return new \WP_Error( 'rest_addon_invalid', esc_html__( 'The addon does not exist.', 'hammock' ), array( 'status' => 404 ) );
		}
	}

	/**
	 * Inner addon actions
	 * 
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function inner_action( $request ) {
		$id 		= $request['id'];
		$response 	= apply_filters( 'hammock_addon_' . $id . '_action', array( 'success' => true, 'message' => __( 'Action executed', 'hammock' ) ), $request );
		return $response;
	}
}

