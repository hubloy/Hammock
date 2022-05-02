<?php
namespace HubloyMembership\Addon\Invitation;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Addon;

class Invitation extends Addon {

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
		$this->id = 'invitation';
		$this->add_filter( 'hubloy_membership_account_invite_enabled', 'invite_enabled' );
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
		if ( ! isset( $addons['invitation'] ) ) {
			$addons['invitation'] = array(
				'name'        => __( 'Invitation Codes', 'memberships-by-hubloy' ),
				'description' => __( 'Users need an invitation code to subscribe to a membership.', 'memberships-by-hubloy' ),
				'icon'        => 'dashicons dashicons-unlock',
				'url'         => admin_url( 'admin.php?page=memberships-by-hubloy-invites' ),
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
		if ( ! isset( $links['invitation'] ) ) {
			$links['invitation'] = array(
				'name'    => __( 'Invitation Codes', 'memberships-by-hubloy' ),
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
		$this->add_action( 'hubloy_membership_account_fields_before_total', 'invite_section' );
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
	public function invite_section( $invoice ) {
		$plan       = $invoice->get_plan();
		$membership = $plan->get_membership();
		if ( ! $membership->is_invite_only() ) {
			return;
		}
		if ( $membership->is_code_isted( $invoice->get_invite_code_id() ) ) {
			return;
		}
		Template::get_template(
			'account/transaction/codes/invite-form.php',
			array(
				'invoice' => $invoice,
			)
		);
	}

	/**
	 * Check if invites are enabled
	 * 
	 * @since 1.1.0
	 * 
	 * @return bool
	 */
	public function invite_enabled() {
		return $this->is_enabled();
	}
}

