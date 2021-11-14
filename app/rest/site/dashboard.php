<?php
namespace Hammock\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Rest;
use Hammock\Core\Util;
use Hammock\Services\Members;
use Hammock\Services\Memberships;
use Hammock\Services\Transactions;

/**
 * Dashboard rest controller
 * Handles dashboard content
 *
 * @since 1.0.0
 */
class Dashboard extends Rest {

	const BASE_API_ROUTE = '/dashboard/';

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
			self::BASE_API_ROUTE . 'members',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'members' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'memberships',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'memberships' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'subscribers',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'subscribers' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'transactions',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'transactions' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);
	}

	/**
	 * List Recent Members
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function members( $request ) {
		$service = new Members();
		$items   = $service->list_html_members( 4, 0 );
		return $items;
	}

	/**
	 * List Recent Memberships
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function memberships( $request ) {
		$service = new Memberships();
		$items   = $service->list_html_memberships( 4, 0 );
		return $items;
	}

	/**
	 * Get graph subscription data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function subscribers( $request ) {
		$service = new Members();
		$items   = $service->get_weekly_member_stats();
		return $items;
	}

	/**
	 * Get graph transaction data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function transactions( $request ) {
		$service = new Transactions();
		$items   = $service->get_weekly_transaction_stats();
		return $items;
	}
}
