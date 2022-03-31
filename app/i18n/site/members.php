<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Members strings
 */
return array(
	'labels'    => array(
		'profile_url'   => __( 'Edit Profile', 'hubloy-membership' ),
		'no_membership' => __( 'No Membership', 'hubloy-membership' ),
		'email'         => __( 'Email', 'hubloy-membership' ),
		'password'      => __( 'Password', 'hubloy-membership' ),
		'firstname'     => __( 'Firstname', 'hubloy-membership' ),
		'lastname'      => __( 'Lastname', 'hubloy-membership' ),
		'start_date'    => __( 'Grant Access From', 'hubloy-membership' ),
		'end_date'      => __( 'Grant Access To', 'hubloy-membership' ),
		'trial'         => __( 'On trial', 'hubloy-membership' ),
		'member_id'     => __( 'Member ID', 'hubloy-membership' ),
	),
	'dashboard' => array(
		'add_new' => array(
			'button' => __( 'Add Member', 'hubloy-membership' ),
			'modal'  => array(
				'title'       => __( 'Add Member', 'hubloy-membership' ),
				'select'      => array(
					'new'      => __( 'New Member', 'hubloy-membership' ),
					'existing' => __( 'Existing Member', 'hubloy-membership' ),
				),
				'select_user' => __( 'Select user', 'hubloy-membership' ),
			),
		),
		'table'   => array(
			'name'         => __( 'Name', 'hubloy-membership' ),
			'email'        => __( 'Email', 'hubloy-membership' ),
			'status'       => __( 'Status', 'hubloy-membership' ),
			'plans'        => __( 'Total Plans', 'hubloy-membership' ),
			'member_since' => __( 'Member Since', 'hubloy-membership' ),
			'not_found'    => __( 'No members found', 'hubloy-membership' ),
			'member_id'    => __( 'Member ID', 'hubloy-membership' ),
		),
	),
	'edit'      => array(
		'title'        => __( 'Member Details', 'hubloy-membership' ),
		'back'         => __( 'Back to members', 'hubloy-membership' ),
		'not_found'    => __( 'Member not found', 'hubloy-membership' ),
		'tabs'         => array(
			'subs'         => __( 'Subscriptions', 'hubloy-membership' ),
			'activity'     => __( 'Activity', 'hubloy-membership' ),
			'transactions' => __( 'Transactions', 'hubloy-membership' ),
		),
		'details'      => array(
			'since'        => __( 'Member since', 'hubloy-membership' ),
			'status'       => __( 'Status', 'hubloy-membership' ),
			'delete'       => array(
				'title'  => __( 'Delete Member and Plans', 'hubloy-membership' ),
				'prompt' => __( 'Are you sure you would like to delete the member and all plans?', 'hubloy-membership' ),
			),
			'subscription' => array(
				'title'  => __( 'Subscription Plans', 'hubloy-membership' ),
				'plans'  => __( 'plan(s)', 'hubloy-membership' ),
				'list'   => array(
					'plan_id'     => __( 'Plan ID', 'hubloy-membership' ),
					'type'        => __( 'Payment Type', 'hubloy-membership' ),
					'sub_date'    => __( 'Subscription Start', 'hubloy-membership' ),
					'expire_date' => __( 'Subscription End', 'hubloy-membership' ),
					'status'      => __( 'Status', 'hubloy-membership' ),
					'enabled'     => __( 'Enabled', 'hubloy-membership' ),
					'date'        => __( 'Date Created', 'hubloy-membership' ),
					'gateway'     => __( 'Payment Gateway', 'hubloy-membership' ),
					'update'      => __( 'Update Details', 'hubloy-membership' ),
					'delete'      => array(
						'one' => array(
							'title'  => __( 'Delete Plan only', 'hubloy-membership' ),
							'prompt' => __( 'Are you sure you would like to delete the plan?', 'hubloy-membership' ),
						),
					),
				),
				'create' => array(
					'title' => __( 'Add Subscription', 'hubloy-membership' ),
					'modal' => array(
						'title'        => __( 'Assign Subscription', 'hubloy-membership' ),
						'membership'   => __( 'Membership', 'hubloy-membership' ),
						'enable_trial' => __( 'Enable trial for ', 'hubloy-membership' ),
						'grant'        => array(
							'title'     => __( 'Grant access for', 'hubloy-membership' ),
							'date'      => __( 'Between certain dates', 'hubloy-membership' ),
							'permanent' => __( 'Permanent access', 'hubloy-membership' ),
							'invoice'   => __( 'Generate an invoice', 'hubloy-membership' ),
						),
					),
				),
			),
		),
		'transactions' => array(
			'id'        => __( 'ID', 'hubloy-membership' ),
			'status'    => __( 'Status', 'hubloy-membership' ),
			'gateway'   => __( 'Gateway', 'hubloy-membership' ),
			'amount'    => __( 'Amount', 'hubloy-membership' ),
			'date'      => __( 'Date', 'hubloy-membership' ),
			'not_found' => __( 'No transactions found', 'hubloy-membership' ),
		),
		'activities'   => array(
			'not_found' => __( 'No activity logs found', 'hubloy-membership' ),
		),
	),
);
