<?php
namespace HubloyMembership\Addon\Coupon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Addon;

class Coupon extends Addon {

	/**
	 * Singletone instance of the addon.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the addon.
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
	 * Addon init
	 */
	public function init() {
		$this->id = 'coupons';
	}

	/**
	 * Register addon
	 * Register a key value pair of addons
	 *
	 * @param array $addons - the current list of addons
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register( $addons ) {
		if ( ! isset( $addons['coupons'] ) ) {
			$addons['coupons'] = array(
				'name'        => __( 'Coupon', 'hubloy-membership' ),
				'description' => __( 'Discount coupons.', 'hubloy-membership' ),
				'icon'        => 'dashicons dashicons-tickets-alt',
				'url'         => admin_url( 'admin.php?page=hubloy-membership-coupons' ),
			);
		}
		return $addons;
	}

	/**
	 * Settings link
	 *
	 * @param array $links
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function settings_link( $links ) {
		if ( ! isset( $links['coupons'] ) ) {
			$links['coupons'] = array(
				'name'    => __( 'Coupons', 'hubloy-membership' ),
				'enabled' => $this->is_enabled(),
			);
		}
		return $links;
	}

	/**
	 * Initialize the addon action
	 *
	 * @since 1.0.0
	 */
	public function init_addon() {

	}


	/**
	 * The addon settings page
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function settings_page() {
		return 'Hello';
	}
}

