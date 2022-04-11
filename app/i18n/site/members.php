<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Members strings
 */
return array(
	'labels'    => array(
		'profile_url'   => __( 'Edit Profile', 'memberships-by-hubloy' ),
		'no_membership' => __( 'No Membership', 'memberships-by-hubloy' ),
		'email'         => __( 'Email', 'memberships-by-hubloy' ),
		'password'      => __( 'Password', 'memberships-by-hubloy' ),
		'firstname'     => __( 'Firstname', 'memberships-by-hubloy' ),
		'lastname'      => __( 'Lastname', 'memberships-by-hubloy' ),
		'start_date'    => __( 'Grant Access From', 'memberships-by-hubloy' ),
		'end_date'      => __( 'Grant Access To', 'memberships-by-hubloy' ),
		'trial'         => __( 'On trial', 'memberships-by-hubloy' ),
		'member_id'     => __( 'Member ID', 'memberships-by-hubloy' ),
	),
	'dashboard' => array(
		'add_new' => array(
			'button' => __( 'Add Member', 'memberships-by-hubloy' ),
			'modal'  => array(
				'title'       => __( 'Add Member', 'memberships-by-hubloy' ),
				'select'      => array(
					'new'      => __( 'New Member', 'memberships-by-hubloy' ),
					'existing' => __( 'Existing Member', 'memberships-by-hubloy' ),
				),
				'select_user' => __( 'Select user', 'memberships-by-hubloy' ),
			),
		),
		'table'   => array(
			'name'         => __( 'Name', 'memberships-by-hubloy' ),
			'email'        => __( 'Email', 'memberships-by-hubloy' ),
			'status'       => __( 'Status', 'memberships-by-hubloy' ),
			'plans'        => __( 'Total Plans', 'memberships-by-hubloy' ),
			'member_since' => __( 'Member Since', 'memberships-by-hubloy' ),
			'not_found'    => __( 'No members found', 'memberships-by-hubloy' ),
			'member_id'    => __( 'Member ID', 'memberships-by-hubloy' ),
		),
	),
	'edit'      => array(
		'title'        => __( 'Member Details', 'memberships-by-hubloy' ),
		'back'         => __( 'Back to members', 'memberships-by-hubloy' ),
		'not_found'    => __( 'Member not found', 'memberships-by-hubloy' ),
		'tabs'         => array(
			'subs'         => __( 'Subscriptions', 'memberships-by-hubloy' ),
			'activity'     => __( 'Activity', 'memberships-by-hubloy' ),
			'transactions' => __( 'Transactions', 'memberships-by-hubloy' ),
		),
		'details'      => array(
			'since'        => __( 'Member since', 'memberships-by-hubloy' ),
			'status'       => __( 'Status', 'memberships-by-hubloy' ),
			'delete'       => array(
				'title'  => __( 'Delete Member and Plans', 'memberships-by-hubloy' ),
				'prompt' => __( 'Are you sure you would like to delete the member and all plans?', 'memberships-by-hubloy' ),
			),
			'subscription' => array(
				'title'  => __( 'Subscription Plans', 'memberships-by-hubloy' ),
				'plans'  => __( 'plan(s)', 'memberships-by-hubloy' ),
				'list'   => array(
					'plan_id'     => __( 'Plan ID', 'memberships-by-hubloy' ),
					'type'        => __( 'Payment Type', 'memberships-by-hubloy' ),
					'sub_date'    => __( 'Subscription Start', 'memberships-by-hubloy' ),
					'expire_date' => __( 'Subscription End', 'memberships-by-hubloy' ),
					'status'      => __( 'Status', 'memberships-by-hubloy' ),
					'enabled'     => __( 'Enabled', 'memberships-by-hubloy' ),
					'date'        => __( 'Date Created', 'memberships-by-hubloy' ),
					'gateway'     => __( 'Payment Gateway', 'memberships-by-hubloy' ),
					'update'      => __( 'Update Details', 'memberships-by-hubloy' ),
					'delete'      => array(
						'one' => array(
							'title'  => __( 'Delete Plan only', 'memberships-by-hubloy' ),
							'prompt' => __( 'Are you sure you would like to delete the plan?', 'memberships-by-hubloy' ),
						),
					),
				),
				'create' => array(
					'title' => __( 'Add Subscription', 'memberships-by-hubloy' ),
					'modal' => array(
						'title'        => __( 'Assign Subscription', 'memberships-by-hubloy' ),
						'membership'   => __( 'Membership', 'memberships-by-hubloy' ),
						'enable_trial' => __( 'Enable trial for ', 'memberships-by-hubloy' ),
						'grant'        => array(
							'title'     => __( 'Grant access for', 'memberships-by-hubloy' ),
							'date'      => __( 'Between certain dates', 'memberships-by-hubloy' ),
							'permanent' => __( 'Permanent access', 'memberships-by-hubloy' ),
							'invoice'   => __( 'Generate an invoice', 'memberships-by-hubloy' ),
						),
					),
				),
			),
		),
		'transactions' => array(
			'id'        => __( 'ID', 'memberships-by-hubloy' ),
			'status'    => __( 'Status', 'memberships-by-hubloy' ),
			'gateway'   => __( 'Gateway', 'memberships-by-hubloy' ),
			'amount'    => __( 'Amount', 'memberships-by-hubloy' ),
			'date'      => __( 'Date', 'memberships-by-hubloy' ),
			'not_found' => __( 'No transactions found', 'memberships-by-hubloy' ),
		),
		'activities'   => array(
			'not_found' => __( 'No activity logs found', 'memberships-by-hubloy' ),
		),
	),
);
