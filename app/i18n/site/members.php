<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Members strings
 */
return array(
	'labels'    => array(
		'profile_url'	=> __( 'Edit Profile', 'hammock' ),
		'no_membership'	=> __( 'No Membership', 'hammock' ),
		'email'			=> __( 'Email', 'hammock' ),
		'password'		=> __( 'Password', 'hammock' ),
		'firstname'		=> __( 'Firstname', 'hammock' ),
		'lastname'		=> __( 'Lastname', 'hammock' ),
		'start_date'	=> __( 'Grant Access From', 'hammock' ),
		'end_date'		=> __( 'Grant Access To', 'hammock' ),
		'trial'			=> __( 'On trial', 'hammock' ),
		'member_id'		=> __( 'Member ID', 'hammock' ),
	),
	'dashboard' => array(
		'add_new' => array(
			'button' => __( 'New Member', 'hammock' ),
			'modal'  => array(
				'title' 	=> __( 'Add Member', 'hammock' ),
				'select'	=> array(
					'new' 		=> __( 'New Member', 'hammock' ),
					'existing'	=> __( 'Existing Member', 'hammock' )
				),
				'select_user'	=>  __( 'Select user', 'hammock' ),
			),
		),
		'table'   => array(
			'name'			=> __( 'Name', 'hammock' ),
			'email'			=> __( 'Email', 'hammock' ),
			'status'		=> __( 'Status', 'hammock' ),
			'plans'			=> __( 'Total Plans', 'hammock' ),
			'member_since'	=> __( 'Member Since', 'hammock' ),
			'not_found'		=> __( 'No members found', 'hammock' ),
			'member_id'		=> __( 'Member ID', 'hammock' ),
		),
	),
	'edit' => array(
		'title'     => __( 'Member Details', 'hammock' ),
		'back'		=> __( 'Back to members', 'hammock' ),
		'not_found'	=> __( 'Member not found', 'hammock' ),
		'tabs'      => array(
			'details' 		=> __( 'Details', 'hammock' ),
			'activity'  	=> __( 'Activity', 'hammock' ),
			'transactions'  => __( 'Transactions', 'hammock' ),
		),
		'details'	=> array(
			'since'			=> __( 'Member since', 'hammock' ),
			'status'		=> __( 'Status', 'hammock' ),
			'delete'		=> array (
				'title' => __( 'Delete Member and Plans', 'hammock' ),
				'prompt'=> __( 'Are you sure you would like to delete the member and all plans?', 'hammock' ),
			),
			'subscription'	=> array(
				'title'		=> __( 'Subscription Plans', 'hammock' ),
				'plans'		=> __( 'plan(s)', 'hammock' ),
				'list'		=> array(
					'plan_id'		=> __( 'Plan ID', 'hammock' ),
					'type'			=> __( 'Payment Type', 'hammock' ),
					'sub_date'		=> __( 'Subscription Start', 'hammock' ),
					'expire_date'	=> __( 'Subscription End', 'hammock' ),
					'status'		=> __( 'Status', 'hammock' ),
					'enabled'		=> __( 'Enabled', 'hammock' ),
					'date'			=> __( 'Date Created', 'hammock' ),
					'gateway'		=> __( 'Payment Gateway', 'hammock' ),
					'update'		=> __( 'Update Details', 'hammock' ),
					'delete'		=> array(
						'one'	=> array(
							'title'	=> __( 'Delete Plan only', 'hammock' ),
							'prompt'=> __( 'Are you sure you would like to delete the plan?', 'hammock' ),
						)
					)
				),
				'create'	=> array(
					'title'	=> __( 'Add Subscription', 'hammock' ),
					'modal'	=> array(
						'title'			=> __( 'Assign Subscription', 'hammock' ),
						'membership'	=> __( 'Membership', 'hammock' ),
						'enable_trial'	=> __( 'Enable trial for ', 'hammock' ),
						'grant'			=> array(
							'title'	=> __( 'Grant access for', 'hammock' ),
							'date'		=> __( 'Between certain dates', 'hammock' ),
							'permanent'	=> __( 'Permanent access', 'hammock' ),
							'invoice'	=> __( 'Generate an invoice', 'hammock' ),
						),
					)
				)
			),
		),
		'transactions'	=> array(
			'id'        => __( 'ID', 'hammock' ),
			'status'    => __( 'Status', 'hammock' ),
			'gateway'   => __( 'Gateway', 'hammock' ),
			'amount'    => __( 'Amount', 'hammock' ),
			'date'      => __( 'Date', 'hammock' ),
			'not_found' => __( 'No transactions found', 'hammock' ),
		),
		'activities'	=> array(
			'not_found' => __( 'No activity logs found', 'hammock' ),
		)
	)	
);