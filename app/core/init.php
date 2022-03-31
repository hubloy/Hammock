<?php
namespace HubloyMembership\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin init class
 */
class Init {

	/**
	 * Activation function
	 * Called in plugin activation
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		flush_rewrite_rules();
		\HubloyMembership\Helper\Logger::init_directory();
		Database::init();
	}

	/**
	 * Deactivation function
	 * Called on plugin deactivation
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		$settings = new \HubloyMembership\Model\Settings();
		if ( $settings->get_general_setting( 'delete_on_uninstall' ) == 1 ) {
			global $wpdb;
			$table_names = Database::table_names();
			foreach ( $table_names as $table ) {
				$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
			}
			Util::delete_option( 'hubloy-membership_settings' );
			Util::delete_option( 'hubloy-membership_installed' );
			Util::delete_option( 'hubloy-membership_wizard_step' );
		}
	}
}

