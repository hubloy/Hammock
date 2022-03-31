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
			'plan-name'    => __( 'Membership Name', 'hubloy-membership' ),
			'plan-status'  => __( 'Status', 'hubloy-membership' ),
			'plan-price'   => __( 'Price', 'hubloy-membership' ),
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
			'transaction-id'      => __( 'Transaction ID', 'hubloy-membership' ),
			'transaction-status'  => __( 'Status', 'hubloy-membership' ),
			'transaction-gateway' => __( 'Gateway', 'hubloy-membership' ),
			'transaction-amount'  => __( 'Amount', 'hubloy-membership' ),
			'transaction-date'    => __( 'Date', 'hubloy-membership' ),
		)
	);
}
