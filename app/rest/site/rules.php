<?php
namespace Hammock\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Rest;

/**
 * Rules rest route
 *
 * @since 1.0.0
 */
class Rules extends Rest {

	const BASE_API_ROUTE = '/rules/';

	/**
	 * Singletone instance of the rest route.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * The rules service.
	 * 
	 * @var object
	 * 
	 * @since 1.0.0
	 */
	private $service = null;

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
	 * Initialize
	 */
	protected function init() {
		$this->service = new \Hammock\Services\Rules();
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
				'callback'            => array( $this, 'list_rules' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'get/(?P<type>[\w-]+)/(?P<method>[\w-]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_rule_data' ),
					'permission_callback' => array( $this, 'validate_request' ),
					'args'                => array(
						'page'     => array(
							'required'          => true,
							'sanitize_callback' => 'absint',
							'type'              => 'integer',
							'description'       => __( 'The current page', 'hammock' ),
						),
					),
				),
			)
		);
	}

	/**
	 * List rules
	 * 
	 * @since 1.0.0
	 */
	public function list_rules() {
		return rest_ensure_response( $this->service->list_rule_types() );
	}


	/**
	 * Get the rule data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_rule_data( $request ) {
		$type   = $request['type'];
		$method = $request['method'];
		return rest_ensure_response( $this->service->get_rule_type_data( array(
			'type'   => $type,
			'data'   => $method,
			'offset' => $request->get_param( 'page' )
		) ) );
	}
}