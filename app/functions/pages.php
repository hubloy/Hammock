<?php
/**
 * Page Settings
 * These functions can be used within themes or external resources
 *
 * @package HubloyMembership/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get page links within the account page
 *
 * @param string $endpoint - the endpoint
 * @param string $value - optional value
 *
 * @since 1.0.0
 *
 * @return string
 */
function hubloy-membership_get_account_page_links( $endpoint = 'base', $value = '' ) {
	$base_url = hubloy-membership_get_page_permalink( 'account_page' );
	if ( 'base' === $endpoint || 'dashboard' === $endpoint ) {
		return $base_url;
	} else {
		return hubloy-membership_get_page_endpoint_url( $base_url, $endpoint, $value );
	}
}

/**
 * Get invoice link
 * 
 * @param string $invoice_id The invoice id
 * 
 * @since 1.0.0
 * 
 * @return string
 */
function hubloy-membership_get_invoice_link( $invoice_id ) {
	return hubloy-membership_get_account_page_links( 'view-transaction', $invoice_id );
}

/**
 * Get the endpoint url
 *
 * @param string $path - the main parent path
 * @param string $endpoint - the endpoint
 * @param string $value - optional value
 *
 * @since 1.0.0
 *
 * @return string
 */
function hubloy-membership_get_page_endpoint_url( $path, $endpoint, $value = '' ) {
	$query_vars = hubloy-membership()->get_query()->get_query_vars();
	$endpoint   = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $path, '?' ) ) {
			$query_string = '?' . wp_parse_url( $path, PHP_URL_QUERY );
			$path         = current( explode( '?', $path ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $path );

		if ( $value ) {
			$url .= trailingslashit( $endpoint ) . user_trailingslashit( $value );
		} else {
			$url .= user_trailingslashit( $endpoint );
		}

		$url .= $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $path );
	}

	return apply_filters( 'hubloy-membership_get_page_endpoint_url', $url, $endpoint, $path, $value );
}

/**
 * Get Account URL
 *
 * @since 1.0.0
 *
 * @return string
 */
function hubloy-membership_get_page_permalink( $page_key ) {
	$page_id  = hubloy-membership_page_id( $page_key );
	$page_url = get_permalink( $page_id );
	return apply_filters( 'hubloy-membership_get_page_permalink', $page_url, $page_id );
}
