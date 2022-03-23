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
			self::BASE_API_ROUTE . 'get/(?P<type>[\w-]+)',
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
							'default'           => 0,
							'description'       => __( 'The current page', 'hammock' ),
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'memberships',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_memberships' ),
					'permission_callback' => array( $this, 'validate_request' ),
					'args'                => array(
						'id'     => array(
							'required'          => false,
							'sanitize_callback' => 'absint',
							'type'              => 'integer',
							'default'           => 0,
							'description'       => __( 'The rule id', 'hammock' ),
						),
					),
				),
			)
		);


		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'items/(?P<type>[\w-]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'validate_request' ),
					'args'                => array(
						'id'     => array(
							'required'          => false,
							'sanitize_callback' => 'absint',
							'type'              => 'integer',
							'description'       => __( 'The rule id', 'hammock' ),
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'save',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_rule' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'delete',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'delete_rule' ),
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


	/**
	 * Get the rule data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_rule_data( $request ) {
		$type  = $request['type'];
		return rest_ensure_response( $this->service->get_rule_type_data( array(
			'type'   => $type,
			'paged'  => $request->get_param( 'page' )
		) ) );
	}

	/**
	 * Get memberships
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_memberships( $request ) {
		$id  = $request->get_param( 'id' );
		return rest_ensure_response( $this->service->get_rule_membership_select( $id ) );
	}

	/**
	 * Get items select
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_items( $request ) {
		$id   = $request->get_param( 'id' );
		$type = $request['type'];
		return rest_ensure_response( $this->service->get_rule_items_select( $id, $type ) );
	}

	/**
	 * Save a new rule
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function save_rule( $request ) {
		$type     		= sanitize_text_field( $request['type'] );
		$item     		= ( int ) sanitize_text_field( $request['item'] );
		$memberships 	= is_array( $request['memberships'] ) ? array_map( 'absint', $request['memberships'] ) : array( absint( $request['memberships'] ) );
		$enabled  		= isset( $request['enabled'] ) ? intval( sanitize_text_field( $request['enabled'] ) ) : 0;
		$status         = $enabled ? \Hammock\Services\Rules::STATUS_ENABLED : \Hammock\Services\Rules::STATUS_DISABLED;
		$data           = array(
			'type'        => $type,
			'id'          => $item,
			'status'      => $status,
			'memberships' => $memberships,
		);
		$response       = $this->service->save_rule( $data );
		if ( is_wp_error( $response ) ) {
			return rest_ensure_response(
				array(
					'status'  => false,
					'message' => $result->get_error_message(),
				)
			);
		}
		return rest_ensure_response( $response );
	}

	/**
	 * Delete a rule
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function delete_rule( $request ) {
		$rule = ( int ) sanitize_text_field( $request['rule'] );
		return rest_ensure_response( $this->service->delete_rule( $rule ) );
	}
}