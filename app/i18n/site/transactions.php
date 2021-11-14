<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Transaction strings
 */

return array(
	'labels'    => array(
		'overdue' => __( 'Overdue', 'hammock' ),
	),
	'dashboard' => array(
		'add_new'        => array(
			'button' => __( 'Create Transaction', 'hammock' ),
		),
		'select_gateway' => __( 'Select Gateway', 'hammock' ),
		'table'          => array(
			'id'        => __( 'ID', 'hammock' ),
			'status'    => __( 'Status', 'hammock' ),
			'gateway'   => __( 'Gateway', 'hammock' ),
			'amount'    => __( 'Amount', 'hammock' ),
			'member'    => __( 'Member', 'hammock' ),
			'date'      => __( 'Date Created', 'hammock' ),
			'due'       => __( 'Due Date', 'hammock' ),
			'not_found' => __( 'No transactions found', 'hammock' ),
		),
	),
	'back'      => __( 'Back to transactions', 'hammock' ),
	'create'    => array(
		'title' => __( 'New Transaction', 'hammock' ),
		'form'  => array(
			'member'     => __( 'Member', 'hammock' ),
			'gateway'    => __( 'Gateway', 'hammock' ),
			'status'     => __( 'Status', 'hammock' ),
			'membership' => __( 'Membership', 'hammock' ),
			'date'       => __( 'Due Date', 'hammock' ),
		),
	),
	'update'    => array(
		'title' => __( 'Edit Transaction', 'hammock' ),
		'form'  => array(
			'member'     => __( 'Member', 'hammock' ),
			'gateway'    => __( 'Gateway', 'hammock' ),
			'status'     => __( 'Status', 'hammock' ),
			'amount'     => __( 'Amount', 'hammock' ),
			'membership' => __( 'Membership', 'hammock' ),
			'date'       => __( 'Due Date', 'hammock' ),
		),
	),
);
