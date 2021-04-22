<?php
namespace Hammock\Addon\Bbpress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Addon;
use Hammock\Core\Util;


class Bbpress extends Addon {

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
		$this->id = 'bbpress';
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
		if ( ! isset( $addons['bbpress'] ) ) {
			$active = $this->plugin_active();
			$addons['bbpress'] = array(
				'name'        => __( 'bbPress Integration', 'hammock' ),
				'description' => $active ? __( 'bbPress rules integration.', 'hammock' ) : __( 'Insatall and active bbPress to use this addon', 'hammock' ),
				'icon'        => 'dashicons dashicons-buddicons-bbpress-logo',
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
		if ( ! isset( $links['bbpress'] ) ) {
			$links['bbpress'] = array(
				'name'    => __( 'bbPress', 'hammock' ),
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
	 * Check if bbpress is active
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function plugin_active() {
		return Util::plugin_active( 'bbpress/bbpress.php' );
	}
}

