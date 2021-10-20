<?php
namespace Hammock\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Cache helper
 * Handles plugin-wide cache
 * 
 * @since 1.0.0
 */
class Cache {

	/**
	 * Get prefix for use with wp_cache_set. Allows all cache in a group to be invalidated at once.
	 *
	 * @param  string $group Group of cache to get.
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	private static function get_cache_prefix( $group ) {
		// Get cache key - uses cache key wc_orders_cache_prefix to invalidate when needed.
		$prefix = wp_cache_get( 'hammock_' . $group . '_cache_prefix', $group );

		if ( false === $prefix ) {
			$prefix = microtime();
			wp_cache_set( 'hammock_' . $group . '_cache_prefix', $prefix, $group );
		}

		return 'hammock_cache_' . $prefix . '_';
	}

	/**
	 * Invalidate cache group.
	 *
	 * @param string $group Group of cache to clear.
	 * 
	 * @since 1.0.0
	 */
	public static function invalidate_cache_group( $group ) {
		wp_cache_set( 'hammock_' . $group . '_cache_prefix', microtime(), $group );
	}

	/**
	 * Set cache
	 * 
	 * @param string $cache_key The cache key.
	 * @param mixed $results The results to cache
	 * @param string $group The cache group
	 * 
	 * @since 1.0.0
	 */
	public static function set_cache( $cache_key, $results, $group ) {
		$prefix = self::get_cache_prefix( $group );
		wp_cache_set( $cache_key, $results, $prefix );
	}

	/**
	 * Get cache
	 * 
	 * @param string $cache_key The cache key.
	 * @param string $group The cache group
	 * 
	 * @since 1.0.0
	 * 
	 * @return mixed
	 */
	public static function get_cache( $cache_key, $group ) {
		$prefix = self::get_cache_prefix( $group );
		return wp_cache_get( $cache_key, $prefix );
	}

	/**
	 * Delete cache
	 * 
	 * @param string $cache_key The cache key.
	 * @param string $group The cache group
	 * 
	 * @since 1.0.0
	 */
	public static function delete_cache( $cache_key, $group ) {
		$prefix = self::get_cache_prefix( $group );
		wp_cache_delete( $cache_key, $prefix );
	}
}
