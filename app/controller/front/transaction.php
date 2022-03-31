<?php
namespace HubloyMembership\Controller\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;
use HubloyMembership\Services\Transactions;

/**
 * Transaction controller
 * Handles all transactions
 *
 * @since 1.0.0
 */
class Transaction extends Controller {

	/**
	 * Transaction service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $transaction_service = null;

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Controller
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Controller
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize controller
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->transaction_service = new Transactions();
		$this->add_action( 'hubloy-membership_api_ipn_notify', 'ipn_notify' );
		$this->add_action( 'hubloy-membership_api_handle_return', 'handle_return' );
	}

	/**
	 * Handle IPN requests
	 * This is used for gateways
	 * There has to be a request parameter of the gateway id
	 *
	 * @since 1.0.0
	 */
	public function ipn_notify() {
		if ( isset( $_REQUEST['gateway'] ) ) {
			$gateway = sanitize_text_field( $_REQUEST['gateway'] );

			$this->transaction_service->process_ipn_notification( $gateway );
		}
		wp_die();
	}


	/**
	 * Handle Payment returns
	 * Handles payment returns. Incase its from a redirect or something else
	 *
	 * @since 1.0.0
	 */
	public function handle_return() {
		$this->transaction_service->process_payment_return();
	}
}

