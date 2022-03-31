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
				'name'        => __( 'Invitation Codes', 'hubloy-membership' ),
				'description' => __( 'Users need an invitation code to subscribe to a membership.', 'hubloy-membership' ),
				'icon'        => 'dashicons dashicons-unlock',
				'url'         => admin_url( 'admin.php?page=hubloy_membership-invites' ),
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
				'name'    => __( 'Invitation Codes', 'hubloy-membership' ),
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
}

