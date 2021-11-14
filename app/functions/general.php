<?php
/**
 * General functions
 * These functions can be used within themes or external resources
 *
 * @package Hammock/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get template
 * This will search the current theme for the template files before checking the default files of the plugin
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 *
 * @see \Hammock\Helper\Template::get_template
 *
 * @since 1.0.0
 */
function hammock_get_template( $template_name, $args = array() ) {
	\Hammock\Helper\Template::get_template( $template_name, $args );
}

/**
 * List active gateways
 *
 * @since 1.0.0
 *
 * @return array
 */
function hammock_list_active_gateways() {
	return \Hammock\Services\Gateways::list_active_gateways();
}

/**
 * Get current page
 *
 * @since 1.0.0
 *
 * @return int
 */
function hammock_get_current_page() {
	$paged = 0;
	if ( get_query_var( 'page' ) ) {
		$paged = intval( get_query_var( 'page' ) ) - 1;
	}
	return $paged;
}

/**
 * get current status
 *
 * @since 1.0.0
 *
 * @return string
 */
function hammock_get_current_status() {
	$status = 'all';
	if ( get_query_var( 'status' ) ) {
		$status = sanitize_text_field( get_query_var( 'status' ) );
	} elseif ( isset( $_REQUEST['status'] ) ) {
		$status = sanitize_text_field( $_REQUEST['status'] );
	}
	return $paged;
}

