<?php
namespace Hammock\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Rest;
use Hammock\Core\Util;
use Hammock\Helper\Pagination;

/**
 * Members rest route
 *
 * @since 1.0.0
 */
class Members extends Rest {

	const BASE_API_ROUTE = '/members/';

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
	 * @return Members
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
			self::BASE_API_ROUTE . 'list',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_members' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'page'     => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'description'       => __( 'The current page', 'hammock' ),
					),
					'per_page' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'default'           => 10,
						'description'       => __( 'Items per page', 'hammock' ),
					),
					'membership' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'default'           => 0,
						'description'       => __( 'The membership id', 'hammock' ),
					),
					'status' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'default'           => null,
						'description'       => __( 'The subscription status', 'hammock' ),
					),
					'gateway' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'default'           => null,
						'description'       => __( 'The gateway id', 'hammock' ),
					),
					'start_date' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'default'           => null,
						'description'       => __( 'The subscription start date', 'hammock' ),
					),
					'end_date' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'default'           => null,
						'description'       => __( 'The subscription end date', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'count',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'count_members' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'get',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_member' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'description'       => __( 'The member id', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'get/plans',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_member_plans' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'description'       => __( 'The member id', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'list/status',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_status' ),
				'permission_callback' => array( $this, 'validate_request' )
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'non_members',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'non_members' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'search' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'description'       => __( 'The search param', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'existing_members',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'existing_members' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'search' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'description'       => __( 'The search param', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'save/new',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_new_member' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'save/existing',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_existing_member' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'plan/create',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'assign_member_plan' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'plan/update',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_member_plan' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'plan/remove',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'remove_member_plan' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'delete',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'remove_member' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);
	}

	/**
	 * List memberships
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_members( $request ) {
		$page     	= $request->get_param( 'page' );
		$per_page 	= $request->get_param( 'per_page' );

		$args		= array();

		$membership = $request->get_param( 'membership' );
		$status 	= $request->get_param( 'status' );
		$gateway 	= $request->get_param( 'gateway' );
		$start_date = $request->get_param( 'start_date' );
		$end_date 	= $request->get_param( 'end_date' );

		if ( intval( $membership ) > 0 ) {
			$args['membership'] = $membership;
		}
		if ( $status ) {
			$args['status'] = $status;
		}
		if ( $gateway ) {
			$args['gateway'] = $gateway;
		}
		if ( $start_date ) {
			$args['start_date'] = $start_date;
		}
		if ( $end_date ) {
			$args['end_date'] = $end_date;
		}

		$current_page = $page - 1;
		$service      = new \Hammock\Services\Members();
		$total        = $service->count_members( $args );
		$pages        = Pagination::generate_pages( $total, $per_page, $page );
		$items        = $service->list_html_members( $per_page, $current_page, $args );
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
	 * Count member
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function count_members( $request ) {
		$service	= new \Hammock\Services\Members();
		$total		= $service->count_members( array() );
		return array(
			'total' => $total
		);
	}

	/**
	 * Get member by id
	 * 
	 * @since 1.0.0
	 * 
	 * @return object
	 */
	public function get_member( $request ) {
		$id 		= $request->get_param( 'id' );
		$service	= new \Hammock\Services\Members();
		return $service->get_member_by_id( $id );
	}

	/**
	 * Get Member plans
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_member_plans( $request ) {
		$member_id 	= $request->get_param( 'id' );
		$service	= new \Hammock\Services\Members();
		$total		= $service->count_member_plans( $member_id );
		$items		= $service->list_member_plans( $member_id );
		return array(
			'total' => $total,
			'items'	=> $items
		);
	}

	/**
	 * List member plan status
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function list_status( $request ) {
		return \Hammock\Services\Members::get_status_types( true );
	}

	/**
	 * List non members
	 * 
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function non_members( $request ) {
		$search = $request->get_param( 'search' );
		if ( !empty( $search ) && strlen( $search ) >= 2 ) {
			$service	= new \Hammock\Services\Members();
			$members 	= $service->search_members( $search );
			return rest_ensure_response( $members );
		} else {
			return array();
		}
	}

	/**
	 * List existing members
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function existing_members( $request ) {
		$search = $request->get_param( 'search' );
		if ( !empty( $search ) && strlen( $search ) >= 2 ) {
			$service	= new \Hammock\Services\Members();
			$members 	= $service->search_members( $search, true );
			return rest_ensure_response( $members );
		} else {
			return array();
		}
	}

	/**
	 * Save new member
	 * This creates a new member and saves in the database
	 * 
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function save_new_member( $request ) {
		$email 		= sanitize_email( $request['email'] );
		if ( !empty( $email ) ) {
			$password 	= isset( $request['password'] ) ? sanitize_text_field( $request['password'] ) : false;
			$firstname 	= isset( $request['firstname'] ) ? sanitize_text_field( $request['firstname'] ) : '';
			$lastname 	= isset( $request['lastname'] ) ? sanitize_text_field( $request['lastname'] ) : '';
			$service	= new \Hammock\Services\Members();
			return $service->save_new_user( $email, $firstname, $lastname, '', $password );
		} else {
			return array(
				'status'   => false,
				'message'  => __( 'Invalid email', 'hammock' )
			);
		}
	}

	/**
	 * Save existing member
	 * Save an existing user id as a member
	 * 
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function save_existing_member( $request ) {
		$user_id = absint( sanitize_text_field( $request['user_id'] ) );
		if ( $user_id > 0 ) {
			$service	= new \Hammock\Services\Members();
			return $service->save_member( $user_id );
		} else {
			return array(
				'status'   => false,
				'message'  => __( 'Invalid user id', 'hammock' )
			);
		}
	}

	/**
	 * Assign plan to member
	 * 
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function assign_member_plan( $request ) {
		$member 	= absint( sanitize_text_field( $request['member'] ) );
		$membership = absint( sanitize_text_field( $request['membership'] ) );
		if ( $membership > 0 ) {
			$enable_trial 	= isset( $request['enable_trial'] );
			$access			= isset( $request['access'] ) ? sanitize_text_field( $request['access'] ) : '';
			$start_date		= isset( $request['membership_start'] ) ? sanitize_text_field( $request['membership_start'] ) : '';
			$end_date		= isset( $request['membership_end'] ) ? sanitize_text_field( $request['membership_end'] ) : '';
			$service		= new \Hammock\Services\Members();
			$response 		= $service->admin_set_plan( $membership, $member, $access, $start_date, $end_date, $enable_trial );
			return $response;
		} else {
			return array(
				'status'   => false,
				'message'  => __( 'Invalid membership id', 'hammock' )
			);
		}
	}

	/**
	 * Update plan
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function update_member_plan( $request ) {
		$plan_id 		= absint( sanitize_text_field( $request['plan'] ) );
		$status			= sanitize_text_field( $request['status'] );
		$start_date		= sanitize_text_field( $request['membership_start'] );
		$end_date		= sanitize_text_field( $request['membership_end'] );
		$enabled 		= isset( $request['enabled'] );
		$service		= new \Hammock\Services\Members();
		$response 		= $service->admin_update_plan( $plan_id, $status, $start_date, $end_date, $enabled );
		
		return $response;
	}

	/**
	 * Remove member plan
	 * This removes the plan and checks if the user will also be deleted
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function remove_member_plan( $request ) {
		$plan_id 		= absint( sanitize_text_field( $request['plan'] ) );
		$delete_user 	= isset( $request['delete_user'] );
		$service		= new \Hammock\Services\Members();
		$response		= $service->admin_remove_plan( $plan_id, $delete_user );
		return $response;
	}

	/**
	 * Remove member
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function remove_member( $request ) {
		$member_id 	= absint( sanitize_text_field( $request['member'] ) );
		$service	= new \Hammock\Services\Members();
		$response	= $service->remove_member( $member_id );
		return $response;
	}
}

?>