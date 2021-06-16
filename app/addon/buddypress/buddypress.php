<?php
namespace Hammock\Addon\Buddypress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Addon;
use Hammock\Core\Util;


class Buddypress extends Addon {

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
		$this->id = 'buddypress';
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
		if ( ! isset( $addons['buddypress'] ) ) {
			$active = $this->plugin_active();
			$addons['buddypress'] = array(
				'name'        => __( 'BuddyPress Integration', 'hammock' ),
				'description' => $active ? __( 'Integrate BuddyPress', 'hammock' ) : __( 'Install and active BuddyPress to use this addon', 'hammock' ),
				'icon'        => 'dashicons dashicons-buddicons-buddypress-logo',
				'configure'   => true,
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
		if ( ! isset( $links['buddypress'] ) ) {
			$links['buddypress'] = array(
				'name'    => __( 'BuddyPress', 'hammock' ),
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
	 * Check if buddypress is active
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function plugin_active() {
		return Util::plugin_active( 'buddypress/bp-loader.php' );
	}
}

