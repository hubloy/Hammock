<?php
namespace HubloyMembership\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\Rest;
use HubloyMembership\Core\Util;
use HubloyMembership\Helper\Pagination;

/**
 * Memberships rest route
 *
 * @since 1.0.0
 */
class Memberships extends Rest {

	const BASE_API_ROUTE = '/memberships/';

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
	 * @return Memberships
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
				'callback'            => array( $this, 'list_memberships' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'page'     => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'description'       => __( 'The current page', 'hubloy-membership' ),
					),
					'per_page' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'default'           => 10,
						'description'       => __( 'Items per page', 'hubloy-membership' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'count',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'count_memberships' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'list_simple',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_simple_memberships' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'member' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'default'           => 0,
						'description'       => __( 'The member id', 'hubloy-membership' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'get',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_membership' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'description'       => __( 'The membership id', 'hubloy-membership' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'save',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_membership' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'update/(?P<method>[\w-]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_membership' ),
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
	public function list_memberships( $request ) {
		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );

		$current_page = $page - 1;
		$service      = new \HubloyMembership\Services\Memberships();
		$total        = $service->count_memberships();
		$pages        = Pagination::generate_pages( $total, $per_page, $page );
		$items        = $service->list_html_memberships( $per_page, $current_page );
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
	 * Count memberships
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function count_memberships( $request ) {
		$service = new \HubloyMembership\Services\Memberships();
		$total   = $service->count_memberships();
		return array(
			'total' => $total,
		);
	}

	/**
	 * List simple memberships for drop down select
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_simple_memberships( $request ) {
		$service = new \HubloyMembership\Services\Memberships();
		$member  = $request->get_param( 'member' );
		return $service->list_simple_memberships( $member );
	}

	/**
	 * Get Membership by id
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_membership( $request ) {
		$id         = $request->get_param( 'id' );
		$membership = new \HubloyMembership\Model\Membership( $id );
		return $membership;
	}

	/**
	 * Save Membership
	 * This saves membership details
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function save_membership( $request ) {
		$name     = sanitize_text_field( $request['membership_name'] );
		$enabled  = isset( $request['membership_enabled'] ) ? intval( sanitize_text_field( $request['membership_enabled'] ) ) : 0;
		$type     = sanitize_text_field( $request['membership_type'] );
		$days     = false;
		$end      = false;
		$duration = false;
		$price    = sanitize_text_field( $request['membership_price'] );
		if ( $type === 'date-range' ) {
			$days = sanitize_text_field( $request['membership_days'] );
		} elseif ( $type === 'recurring' ) {
			$duration = sanitize_text_field( $request['recurring_duration'] );
		}

		$service = new \HubloyMembership\Services\Memberships();
		$id      = $service->save( $name, '', $enabled, $type, $price );
		if ( $id ) {
			if ( $days ) {
				$service->save_meta( $id, 'membership_days', $days );
			}

			if ( $duration ) {
				$service->update_duration( $id, $duration );
			}

			return array(
				'status'  => true,
				'message' => __( 'Membership Saved', 'hubloy-membership' ),
				'id'      => $id,
			);
		} else {
			return array(
				'status'  => false,
				'message' => __( 'Error saving membership', 'hubloy-membership' ),
			);
		}
	}

	/**
	 * Update membership
	 * This performs an update of the different sections of a membership edit page
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_membership( $request ) {
		$method     = $request['method'];
		$id         = (int) $request['id'];
		$membership = new \HubloyMembership\Model\Membership( $id );
		$service    = new \HubloyMembership\Services\Memberships();
		if ( $membership->id > 0 ) {
			switch ( $method ) {
				case 'general':
					$name        = sanitize_text_field( $request['membership_name'] );
					$details     = sanitize_text_field( $request['membership_details'] );
					$enabled     = isset( $request['membership_enabled'] ) ? intval( $request['membership_enabled'] ) : 0;
					$lmited      = isset( $request['limit_spaces'] ) ? intval( $request['limit_spaces'] ) : 0;
					$available   = sanitize_text_field( $request['total_available'] );
					$type        = sanitize_text_field( $request['membership_type'] );
					$invite_list = sanitize_text_field( $request['invite_only_list'] );
					$invite_only = isset( $request['invite_only'] ) ? intval( $request['invite_only'] ) : 0;
					$days        = false;
					$end         = false;
					$duration    = false;
					if ( $type === 'date-range' ) {
						$days = sanitize_text_field( $request['membership_days'] );
					} elseif ( $type === 'recurring' ) {
						$duration = sanitize_text_field( $request['recurring_duration'] );
					}
					$service->update_general( $id, $name, $details, $enabled, $type, $lmited, $available );
					if ( $days ) {
						$service->update_meta( $id, 'membership_days', $days );
					}
					if ( $duration ) {
						$service->update_duration( $id, $duration );
					}
					if ( ! empty( $invite_list ) ) {
						$invite_list = explode( ',', $invite_list );
					} else {
						$invite_list = array();
					}
					$service->update_meta( $id, 'invite_list', $invite_list );
					$service->update_meta( $id, 'invite_only', $invite_only );
					return $this->return_success( $id, $method );
				break;
				case 'price':
					$price          = sanitize_text_field( $request['membership_price'] );
					$signup_price   = sanitize_text_field( $request['signup_price'] );
					$trial_enabled  = isset( $request['trial_enabled'] ) ? intval( $request['trial_enabled'] ) : 0;
					$trial_price    = sanitize_text_field( $request['trial_price'] );
					$trial_period   = sanitize_text_field( $request['trial_period'] );
					$trial_duration = sanitize_text_field( $request['trial_duration'] );
					$service->update_price( $id, $price, $signup_price, $trial_enabled, $trial_price, $trial_period, $trial_duration );
					return $this->return_success( $id, $method );
				break;
				default:
					return array(
						'status'  => false,
						'message' => sprintf( __( 'Action %s not supported', 'hubloy-membership' ), $method ),
					);
				break;
			}
		} else {
			return array(
				'status'  => false,
				'message' => __( 'Membership not found', 'hubloy-membership' ),
			);
		}
	}

	/**
	 * Handles success messages on membership update
	 * This is a common function used for all success messages
	 *
	 * @param int    $id
	 * @param string $method
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function return_success( $id, $method ) {
		// We must load membership again with updated data
		$membership = new \HubloyMembership\Model\Membership( $id );
		return array(
			'status'     => true,
			'message'    => sprintf( __( '%s settings updated', 'hubloy-membership' ), ucfirst( $method ) ),
			'membership' => $membership,
		);
	}
}


