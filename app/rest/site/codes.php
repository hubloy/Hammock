<?php
namespace HubloyMembership\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\Rest;
use HubloyMembership\Core\Util;
use HubloyMembership\Helper\Pagination;

/**
 * Codes rest controller
 * Handles all invoice and coupon codes
 *
 * @since 1.0.0
 */
class Codes extends Rest {

	const BASE_API_ROUTE = '/codes/';

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
	 *
	 * @return Codes
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
			self::BASE_API_ROUTE . 'list/(?P<type>[\w-]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'list_codes' ),
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
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'save/(?P<type>[\w-]+)',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_code' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'update/(?P<type>[\w-]+)',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'update_code' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'get/(?P<type>[\w-]+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_code' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'description'       => __( 'The code id', 'memberships-by-hubloy' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'dropdown/(?P<type>[\w-]+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'drop_down_list' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);
	}

	/**
	 * List codes by type
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_codes( $request ) {
		$type         = $request['type'];
		$page         = $request->get_param( 'page' );
		$per_page     = $request->get_param( 'per_page' );
		$current_page = $page - 1;
		$service      = new \HubloyMembership\Services\Codes( $type );
		$model        = $service->get_model();
		$total        = $model->count();
		$pages        = Pagination::generate_pages( $total, $per_page, $page );
		$items        = $model->list_all( $per_page, $current_page );
		$pager        = array(
			'total'   => $total,
			'pages'   => $pages,
			'current' => $page,
		);
		return array(
			'pager' => $pager,
			'items' => $items,
		);
	}

	/**
	 * Save code
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function save_code( $request ) {
		$type    = $request['type'];
		$service = new \HubloyMembership\Services\Codes( $type );
		return $service->save_code( $request );
	}

	/**
	 * Update code
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_code( $request ) {
		$type    = $request['type'];
		$service = new \HubloyMembership\Services\Codes( $type );
		return $service->update_code( $request );
	}

	/**
	 * Get Code by id
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_code( $request ) {
		$id      = $request->get_param( 'id' );
		$type    = $request['type'];
		$service = new \HubloyMembership\Services\Codes( $type );
		return $service->get_code( $id );
	}

	/**
	 * Drop down list of codes
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function drop_down_list( $request ) {
		$type    = $request['type'];
		$service = new \HubloyMembership\Services\Codes( $type );
		return $service->drop_down();
	}
}

