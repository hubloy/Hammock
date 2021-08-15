<?php
namespace Hammock\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Rest;
use Hammock\Core\Util;
use Hammock\Helper\Pagination;

/**
 * Transactions rest route
 *
 * @since 1.0.0
 */
class Transactions extends Rest {

	const BASE_API_ROUTE = '/transactions/';

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
	 * @return Transactions
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
				'callback'            => array( $this, 'list_transactions' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'page'      => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'description'       => __( 'The current page', 'hammock' ),
					),
					'per_page'  => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'default'           => 10,
						'description'       => __( 'Items per page', 'hammock' ),
					),
					'gateway'   => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'default'           => '',
						'description'       => __( 'Gateway id', 'hammock' ),
					),
					'status'    => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'default'           => '',
						'description'       => __( 'Transaction Status', 'hammock' ),
					),
					'member_id' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'default'           => false,
						'description'       => __( 'The member id', 'hammock' ),
					),
					'amount'    => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
						'type'              => 'integer',
						'default'           => false,
						'description'       => __( 'The amount', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'list/status',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_transaction_status' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);


		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'get',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_invoice' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'save',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_new_transaction' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'update',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_transaction' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);
	}

	/**
	 * List transactions
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_transactions( $request ) {
		$page         = $request->get_param( 'page' );
		$per_page     = $request->get_param( 'per_page' );
		$current_page = $page - 1;
		$service      = new \Hammock\Services\Transactions();
		$params       = array(
			'gateway'   => $request->get_param( 'gateway' ),
			'status'    => $request->get_param( 'status' ),
			'member_id' => $request->get_param( 'member_id' ),
			'amount'    => $request->get_param( 'amount' ),
		);
		$total        = $service->count_transaction( $params );
		$pages        = Pagination::generate_pages( $total, $per_page, $page );
		$items        = $service->list_transactions( $per_page, $current_page, $params );
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
	 * List transaction status
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_transaction_status( $request ) {
		$status = \Hammock\Services\Transactions::transaction_status();
		array_unshift( $status, array( 0 => __( 'Select Status', 'hammock' ) ) );
		return $status;
	}

	/**
	 * Get invoice
	 * 
	 * @since 1.0.0
	 * 
	 * @return object
	 */
	public function get_invoice( $request ) {
		$id 		= sanitize_text_field( $request['id'] );
		$invoice 	= \Hammock\Services\Transactions::get_invoice( $id );
		return (object) $invoice->to_html();
	}

	/**
	 * Save new transaction
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function save_new_transaction( $request ) {
		$user_id 	= sanitize_text_field( $request['user_id'] );
		$membership = sanitize_text_field( $request['membership'] );
		$status 	= sanitize_text_field( $request['status'] );
		$gateway 	= sanitize_text_field( $request['gateway'] );
		$due_date 	= sanitize_text_field( $request['due_date'] );
		$service	= new \Hammock\Services\Transactions();
		return $service->create_transaction( $user_id, $membership, $status, $gateway, $due_date );
	}

	/**
	 * Update transaction
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function update_transaction( $request ) {
		$id 		= sanitize_text_field( $request['id'] );
		$status 	= sanitize_text_field( $request['status'] );
		$due_date 	= sanitize_text_field( $request['due_date'] );
		$amount		= sanitize_text_field( $request['amount'] );
		$service	= new \Hammock\Services\Transactions();
		$success 	= $service->update_transaction( $id, '', $status, $amount, $due_date );
		return array(
			'status' 	=> $success,
			'message'	=> $success ? __( 'Transaction updated', 'hammock' ) : __( 'Error updating transaction', 'hammock' )
		);
	}
}


