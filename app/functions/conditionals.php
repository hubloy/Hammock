<?php
/**
 * Conditionals
 * These functions can be used within themes or external resources
 *
 * @package HubloyMembership/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check if the current page is the account page
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy-membership_is_account_page() {
	$page_id = hubloy-membership_page_id( 'account_page' );
	return ( $page_id && is_page( $page_id ) );
}

/**
 * Is membership page
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy-membership_is_membership_page() {
	$page_id = hubloy-membership_page_id( 'membership_list' );
	return ( $page_id && is_page( $page_id ) );
}

/**
 * Is protected content page
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy-membership_is_protected_content_page() {
	$page_id = hubloy-membership_page_id( 'protected_content' );
	return ( $page_id && is_page( $page_id ) );
}

/**
 * Is registration page
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy-membership_is_registration_page() {
	$page_id = hubloy-membership_page_id( 'registration' );
	return ( $page_id && is_page( $page_id ) );
}

/**
 * Is thank you page
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy-membership_is_thank_you_page() {
	$page_id = hubloy-membership_page_id( 'thank_you_page' );
	return ( $page_id && is_page( $page_id ) );
}

