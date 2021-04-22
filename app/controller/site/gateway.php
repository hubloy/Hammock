<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Services\Gateways;

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
		\Hammock\Gateway\Stripe\Stripe::instance();
		\Hammock\Gateway\Manual\Manual::instance();

		do_action( 'hammock_load_gateways' );
	}

	/**
	 * Init gateways
	 *
	 * @since 1.0.0
	 */
	function init_gateways() {
		do_action( 'hammock_init_gateway' );
	}
}


