<?php
namespace Hammock\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Utility core class
 *
 * @since 1.0.0
 */
class Util {

	/**
	 * List of active plugins
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $active_plugins;

	/**
	 * Wrapper to get an option value (regards network-wide protection mode)
	 *
	 * @since  1.0.0
	 * @param  string $key Option Key
	 * @return mixed Option value
	 */
	public static function get_option( $key, $default = false ) {
		if ( is_multisite() ) {
			$settings = get_site_option( $key, $default );
		} else {
			$settings = get_option( $key, $default );
		}

		return $settings;
	}

	/**
	 * Wrapper to delete an option value (regards network-wide protection mode)
	 *
	 * @since  1.0.0
	 * @param  string $key Option Key
	 */
	public static function delete_option( $key ) {
		if ( is_multisite() ) {
			delete_site_option( $key );
		} else {
			delete_option( $key );
		}
	}

	/**
	 * Wrapper to update an option value (regards network-wide protection mode)
	 *
	 * @since  1.0.0
	 * @param  string $key Option Key
	 * @param  mixed  $value New option value
	 */
	public static function update_option( $key, $value ) {
		if ( is_multisite() ) {
			update_site_option( $key, $value, false );
		} else {
			update_option( $key, $value, false );
		}
	}

	/**
	 * Load plugins to check if plugins are enabled
	 *
	 * @since 1.0.0
	 */
	public static function init_plugins() {
		self::$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
	}

	/**
	 * Check if plugin is active
	 *
	 * @param string $plugin_slug - the plugin slug
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function plugin_active( $plugin_slug ) {
		if ( ! self::$active_plugins ) {
			self::init_plugins();
		}
		return in_array( $plugin_slug, self::$active_plugins ) || array_key_exists( $plugin_slug, self::$active_plugins );
	}
}

