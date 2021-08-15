<?php
namespace Hammock\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Rest;
use Hammock\Core\Util;

/**
 * Wizard rest route
 *
 * @since 1.0.0
 */
class Wizard extends Rest {

	const BASE_API_ROUTE = '/wizard/';

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
	 * @param String $namespace - the parent namespace
	 *
	 * @since 1.0.0
	 */
	public function set_up_route( $namespace ) {
		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'step',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_step' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);
	}


	/**
	 * Get the current wizard step
	 * Used if someone has saved a step and would like to go to the next step
	 * 
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_step( $request ) {
		$step = Util::get_option( 'hammock_wizard_step' );
		if ( !$step || !is_array( $step ) ) {
			$step = array(
				'value'	=> 10,
				'step'	=> 'options'
			);
		}
		return rest_ensure_response( $step );
	}
}