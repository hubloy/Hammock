<?php
namespace HubloyMembership\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;
use HubloyMembership\Services\Gateways;

/**
 * Gateway controller
 * Manages all gatewats
 *
 * @since 1.0.0
 */
class Gateway extends Controller {

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Gateway
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
	 * @return Gateway
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
		$this->load_gateways();
		$this->init_gateways();
	}

	/**
	 * Load gateways
	 *
	 * @since 1.0.0
	 */
	function load_gateways() {
		\HubloyMembership\Gateway\Manual\Manual::instance();
		\HubloyMembership\Gateway\Paypal\Paypal::instance();
		do_action( 'hubloy_membership_load_gateways' );
	}

	/**
	 * Init gateways
	 *
	 * @since 1.0.0
	 */
	function init_gateways() {
		do_action( 'hubloy_membership_init_gateway' );
	}
}


