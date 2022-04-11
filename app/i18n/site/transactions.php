<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Transaction strings
 */

return array(
	'labels'    => array(
		'overdue' => __( 'Overdue', 'memberships-by-hubloy' ),
	),
	'dashboard' => array(
		'add_new'        => array(
			'button' => __( 'Create Transaction', 'memberships-by-hubloy' ),
		),
		'select_gateway' => __( 'Select Gateway', 'memberships-by-hubloy' ),
		'table'          => array(
			'id'        => __( 'ID', 'memberships-by-hubloy' ),
			'status'    => __( 'Status', 'memberships-by-hubloy' ),
			'gateway'   => __( 'Gateway', 'memberships-by-hubloy' ),
			'amount'    => __( 'Amount', 'memberships-by-hubloy' ),
			'member'    => __( 'Member', 'memberships-by-hubloy' ),
			'date'      => __( 'Date Created', 'memberships-by-hubloy' ),
			'due'       => __( 'Due Date', 'memberships-by-hubloy' ),
			'not_found' => __( 'No transactions found', 'memberships-by-hubloy' ),
		),
	),
	'back'      => __( 'Back to transactions', 'memberships-by-hubloy' ),
	'create'    => array(
		'title' => __( 'New Transaction', 'memberships-by-hubloy' ),
		'form'  => array(
			'member'     => __( 'Member', 'memberships-by-hubloy' ),
			'gateway'    => __( 'Gateway', 'memberships-by-hubloy' ),
			'status'     => __( 'Status', 'memberships-by-hubloy' ),
			'membership' => __( 'Membership', 'memberships-by-hubloy' ),
			'date'       => __( 'Due Date', 'memberships-by-hubloy' ),
		),
	),
	'update'    => array(
		'title' => __( 'Edit Transaction', 'memberships-by-hubloy' ),
		'form'  => array(
			'member'     => __( 'Member', 'memberships-by-hubloy' ),
			'gateway'    => __( 'Gateway', 'memberships-by-hubloy' ),
			'status'     => __( 'Status', 'memberships-by-hubloy' ),
			'amount'     => __( 'Amount', 'memberships-by-hubloy' ),
			'membership' => __( 'Membership', 'memberships-by-hubloy' ),
			'date'       => __( 'Due Date', 'memberships-by-hubloy' ),
		),
	),
);
