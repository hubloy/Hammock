<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Transaction strings
 */

return array(
	'labels'    => array(
		'overdue' => __( 'Overdue', 'hubloy-membership' ),
	),
	'dashboard' => array(
		'add_new'        => array(
			'button' => __( 'Create Transaction', 'hubloy-membership' ),
		),
		'select_gateway' => __( 'Select Gateway', 'hubloy-membership' ),
		'table'          => array(
			'id'        => __( 'ID', 'hubloy-membership' ),
			'status'    => __( 'Status', 'hubloy-membership' ),
			'gateway'   => __( 'Gateway', 'hubloy-membership' ),
			'amount'    => __( 'Amount', 'hubloy-membership' ),
			'member'    => __( 'Member', 'hubloy-membership' ),
			'date'      => __( 'Date Created', 'hubloy-membership' ),
			'due'       => __( 'Due Date', 'hubloy-membership' ),
			'not_found' => __( 'No transactions found', 'hubloy-membership' ),
		),
	),
	'back'      => __( 'Back to transactions', 'hubloy-membership' ),
	'create'    => array(
		'title' => __( 'New Transaction', 'hubloy-membership' ),
		'form'  => array(
			'member'     => __( 'Member', 'hubloy-membership' ),
			'gateway'    => __( 'Gateway', 'hubloy-membership' ),
			'status'     => __( 'Status', 'hubloy-membership' ),
			'membership' => __( 'Membership', 'hubloy-membership' ),
			'date'       => __( 'Due Date', 'hubloy-membership' ),
		),
	),
	'update'    => array(
		'title' => __( 'Edit Transaction', 'hubloy-membership' ),
		'form'  => array(
			'member'     => __( 'Member', 'hubloy-membership' ),
			'gateway'    => __( 'Gateway', 'hubloy-membership' ),
			'status'     => __( 'Status', 'hubloy-membership' ),
			'amount'     => __( 'Amount', 'hubloy-membership' ),
			'membership' => __( 'Membership', 'hubloy-membership' ),
			'date'       => __( 'Due Date', 'hubloy-membership' ),
		),
	),
);
