<?php
/**
 * Cache functions
 * Functions used to manage cache
 *
 * @package HubloyMembership/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Add to cache
 *
 * @param string $cache_key The cache key.
 * @param mixed  $results The results to cache
 * @param string $group The cache group
 *
 * @since 1.0.0
 */
function hubloy_membership_add_cache( $cache_key, $results, $group ) {
	\HubloyMembership\Helper\Cache::set_cache( $cache_key, $results, $group );
}

/**
 * Delete cache
 *
 * @param string $group The cache group
 * @param bool   $cache_key The cache key
 *
 * @since 1.0.0
 *
 * @return mixed
 */
function hubloy_membership_get_cache( $group, $cache_key ) {
	return \HubloyMembership\Helper\Cache::get_cache( $cache_key, $group );
}

/**
 * Delete cache
 *
 * @param string $group The cache group
 * @param bool   $cache_key Optional. The cache key
 *
 * @since 1.0.0
 */
function hubloy_membership_delete_cache( $group, $cache_key = false ) {
	if ( $cache_key ) {
		\HubloyMembership\Helper\Cache::delete_cache( $cache_key, $group );
	} else {
		\HubloyMembership\Helper\Cache::invalidate_cache_group( $group );
	}
}
