<?php
/**
 * Account functions
 * These functions can be used within themes or external resources
 *
 * @package HubloyMembership/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * This checks if the current plan id is active
 *
 * @param int $plan_id - the plan id
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy_membership_is_member_plan_active( $plan_id ) {
	$plan = new \HubloyMembership\Model\Plan( $plan_id );
	if ( $plan->id > 0 ) {
		return $plan->is_active();
	}
	return false;
}

/**
 * Account member navigation menu
 */
function hubloy_membership_account_member_navigation_labels() {
	$endpoints = \HubloyMembership\Services\Pages::account_page_endpoits();
	$labels    = array(
		'dashboard'     => __( 'Dashboard', 'memberships-by-hubloy' ),
		'transactions'  => __( 'Transactions', 'memberships-by-hubloy' ),
		'edit-account'  => __( 'Edit Account', 'memberships-by-hubloy' ),
		'subscriptions' => __( 'Subscriptions', 'memberships-by-hubloy' ),
		'member-logout' => __( 'Logout', 'memberships-by-hubloy' ),
	);

	foreach ( $endpoints as $endpoint_id => $endpoint ) {
		if ( empty( $endpoint ) ) {
			unset( $labels[ $endpoint_id ] );
		}
	}

	return apply_filters( 'hubloy_membership_account_member_navigation_labels', $labels, $endpoints );
}

/**
 * Add class to user account menus
 *
 * @param string $endpoint - the endpoint
 *
 * @since 1.0.0
 *
 * @return string
 */
function hubloy_membership_account_member_navigation_link_class( $endpoint ) {
	global $wp;

	$classes = array(
		'hubloy_membership-member-account-navigation-' . $endpoint,
	);

	// Set current item class.
	$current = isset( $wp->query_vars[ $endpoint ] );
	if ( 'dashboard' === $endpoint && ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) ) {
		$current = true;
	} elseif ( 'transactions' === $endpoint && isset( $wp->query_vars['view-transactions'] ) ) {
		$current = true;
	} elseif ( 'payment-methods' === $endpoint && isset( $wp->query_vars['add-payment-method'] ) ) {
		$current = true;
	}

	if ( $current ) {
		$classes[] = 'is-active';
	}

	$classes = apply_filters( 'hubloy_membership_account_member_navigation_link_class', $classes, $endpoint );

	return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}

/**
 * Logout url
 *
 * @param string $redirect - the url to redirect to. Defaults to the account page
 *
 * @since 1.0.0
 *
 * @return string
 */
function hubloy_membership_logout_url( $redirect = '' ) {
	$redirect = $redirect ? $redirect : apply_filters( 'hubloy_membership_logout_url', hubloy_membership_get_page_permalink( 'account_page' ) );
	return wp_logout_url( $redirect );
}

/**
 * Check if the current user can subscribe
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy_membership_current_user_can_subscribe() {
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		return hubloy_membership_user_can_subscribe( $user_id );
	}
	return false;
}

/**
 * Check if user can subscribe
 *
 * @param int $user_id - the user id
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy_membership_user_can_subscribe( $user_id ) {
	if ( user_can( $user_id, 'editor' ) || user_can( $user_id, 'administrator' ) ) {
		return apply_filters( 'hubloy_membership_admin_can_subscribe', false, $user_id );
	} else {
		return apply_filters( 'hubloy_membership_user_can_subscribe', true, $user_id );
	}
}


/**
 * Get the current member
 *
 * @see hubloy_membership_user_is_member
 *
 * @since 1.0.0
 *
 * @return bool|object
 */
function hubloy_membership_get_current_member() {
	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		return hubloy_membership_user_is_member( $user_id );
	}
	return false;
}

/**
 * Checks if the current user is a member.
 * This will just check if a user is saved in the member table
 * It will return a member object if the member exists
 *
 * @param int $user_id - the user id
 *
 * @since 1.0.0
 *
 * @return mixed
 */
function hubloy_membership_user_is_member( $user_id ) {
	$service = new \HubloyMembership\Services\Members();
	$member  = $service->get_member_by_user_id( $user_id );
	return $member;
}

/**
 * Check if member has plans
 *
 * @param int $user_id - the user id
 *
 * @since 1.0.0
 *
 * @return mixed
 */
function hubloy_membership_user_has_plans( $user_id ) {
	$member = hubloy_membership_user_is_member( $user_id );
	if ( $member ) {
		return count( $member->get_plan_ids() );
	}
	return false;
}

