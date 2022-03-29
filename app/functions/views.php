<?php
/**
 * Views functions
 * Functions used in views
 * These functions can be used within themes or external resources
 *
 * @package Hammock/Functions
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
function hammock_view_subscription_list_table_columns() {
	return apply_filters(
		'hammock_view_subscription_list_table_columns',
		array(
			'plan-name'    => __( 'Membership Name', 'hammock' ),
			'plan-status'  => __( 'Status', 'hammock' ),
			'plan-price'   => __( 'Price', 'hammock' ),
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
function hammock_view_transaction_list_table_columns() {
	return apply_filters(
		'hammock_view_transaction_list_table_columns',
		array(
			'transaction-id'      => __( 'Transaction ID', 'hammock' ),
			'transaction-status'  => __( 'Status', 'hammock' ),
			'transaction-gateway' => __( 'Gateway', 'hammock' ),
			'transaction-amount'  => __( 'Amount', 'hammock' ),
			'transaction-date'    => __( 'Date', 'hammock' ),
		)
	);
}
