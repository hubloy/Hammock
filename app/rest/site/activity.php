<?php
namespace HubloyMembership\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\Rest;
use HubloyMembership\Helper\Pagination;

/**
 * Activity rest endpoint
 *
 * @since 1.0.0
 */
class Activity extends Rest {

	const BASE_API_ROUTE = '/activity/';

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
				'callback'            => array( $this, 'list_activities' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'page'     => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'description'       => __( 'The current page', 'memberships-by-hubloy' ),
					),
					'per_page' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'default'           => 10,
						'description'       => __( 'Items per page', 'memberships-by-hubloy' ),
					),
					'ref_id'   => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'string',
						'description'       => __( 'Reference id', 'memberships-by-hubloy' ),
					),
					'ref_type' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'description'       => __( 'Reference type', 'memberships-by-hubloy' ),
					),
				),
			)
		);
	}

	/**
	 * List activities
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_activities( $request ) {
		$page         = $request->get_param( 'page' );
		$per_page     = $request->get_param( 'per_page' );
		$ref_id       = $request->get_param( 'ref_id' );
		$ref_type     = $request->get_param( 'ref_type' );
		$current_page = $page - 1;
		$service      = new \HubloyMembership\Services\Activity();

		$total = $service->count_activities( $ref_id, $ref_type );
		$pages = Pagination::generate_pages( $total, $per_page, $page );
		$items = $service->list_activities( $ref_id, $ref_type, $per_page, $current_page );
		$pager = array(
			'total'   => $total,
			'pages'   => $pages,
			'current' => $page,
		);
		return rest_ensure_response(
			array(
				'pager' => $pager,
				'items' => $items,
			)
		);
	}
}

