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
	}

	/**
	 * List rules
	 * 
	 * @since 1.0.0
	 */
	public function list_rules() {
		return rest_ensure_response( $this->service->list_rule_types() );
	}
}