<?php
namespace HubloyMembership\Addon\Coupon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Addon;
use HubloyMembership\Helper\Template;

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
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function register( $addons ) {
		if ( ! isset( $addons['coupons'] ) ) {
			$addons['coupons'] = array(
				'name'        => __( 'Coupon', 'memberships-by-hubloy' ),
				'description' => __( 'Discount coupons.', 'memberships-by-hubloy' ),
				'icon'        => 'dashicons dashicons-tickets-alt',
				'url'         => admin_url( 'admin.php?page=memberships-by-hubloy-coupons' ),
			);
		}
		return $addons;
	}

	/**
	 * Settings link
	 *
	 * @param array $links
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function settings_link( $links ) {
		if ( ! isset( $links['coupons'] ) ) {
			$links['coupons'] = array(
				'name'    => __( 'Coupons', 'memberships-by-hubloy' ),
				'enabled' => $this->is_enabled(),
			);
		}
		return $links;
	}

	/**
	 * Initialize the addon action
	 *
	 * @since 1.1.0
	 */
	public function init_addon() {
		$this->add_action( 'hubloy_membership_account_fields_before_total', 'coupons_section' );
	}


	/**
	 * Add coupon form.
	 * 
	 * @param \HubloyMembership\Model\Codes\Invoice $invoice The invoice.
	 * 
	 * @since 1.1.0
	 * 
	 * @return string
	 */
	public function coupons_section( $invoice ) {
		Template::get_template(
			'account/transaction/codes/coupon-form.php',
			array(
				'invoice' => $invoice,
			)
		);
	}
}

