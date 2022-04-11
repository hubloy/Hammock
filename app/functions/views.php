<?php
/**
 * Views functions
 * Functions used in views
 * These functions can be used within themes or external resources
 *
 * @package HubloyMembership/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Subscription list table columns
 *
 * @since 1.0.0
 *
 * @return array
 */
function hubloy_membership_view_subscription_list_table_columns() {
	return apply_filters(
		'hubloy_membership_view_subscription_list_table_columns',
		array(
			'plan-name'    => __( 'Membership Name', 'memberships-by-hubloy' ),
			'plan-status'  => __( 'Status', 'memberships-by-hubloy' ),
			'plan-price'   => __( 'Price', 'memberships-by-hubloy' ),
			'plan-payment' => '',
		)
	);
}

/**
 * Transaction list table columns
 *
 * @since 1.0.0
 *
 * @return array
 */
function hubloy_membership_view_transaction_list_table_columns() {
	return apply_filters(
		'hubloy_membership_view_transaction_list_table_columns',
		array(
			'transaction-id'      => __( 'Transaction ID', 'memberships-by-hubloy' ),
			'transaction-status'  => __( 'Status', 'memberships-by-hubloy' ),
			'transaction-gateway' => __( 'Gateway', 'memberships-by-hubloy' ),
			'transaction-amount'  => __( 'Amount', 'memberships-by-hubloy' ),
			'transaction-date'    => __( 'Date', 'memberships-by-hubloy' ),
		)
	);
}
