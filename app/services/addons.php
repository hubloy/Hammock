<?php
namespace HubloyMembership\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Addons service
 *
 * @since 1.0.0
 */
class Addons {

	/**
	 * Load addons
	 * The return filter is completed in each addon class and initiated in the `hubloy-membership_init_addon` hook
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	static function load_addons() {
		$addons = apply_filters( 'hubloy-membership_register_addons', array() );
		ksort( $addons );
		return $addons;
	}


	/**
	 * Addons setting link
	 * This returns a list of nav items for all enabled addons/ Each filter is defined in the addon
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	static function addon_settings_links() {
		return apply_filters( 'hubloy-membership_register_addon_setting_link', array() );
	}
}

