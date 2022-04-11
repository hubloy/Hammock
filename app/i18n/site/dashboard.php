<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Dashboard strings
 */
return array(
	'members'     => array(
		'title' => __( 'Members', 'memberships-by-hubloy' ),
		'none'  => __( 'No Members Found', 'memberships-by-hubloy' ),
		'url'   => admin_url( 'admin.php?page=hubloy_membership-members' ),
	),
	'memberships' => array(
		'title' => __( 'Memberships', 'memberships-by-hubloy' ),
		'none'  => __( 'No Memberships Found', 'memberships-by-hubloy' ),
		'url'   => admin_url( 'admin.php?page=hubloy_membership-memberships' ),
	),
	'management'  => array(
		'title' => __( 'Site Management', 'memberships-by-hubloy' ),
		'types' => apply_filters(
			'hubloy_membership_dashboard_links',
			array(
				'memberships' => array(
					'name' => __( 'View Memberships', 'memberships-by-hubloy' ),
					'url'  => admin_url( 'admin.php?page=hubloy_membership-memberships' ),
				),
				'addons'      => array(
					'name' => __( 'View Addons', 'memberships-by-hubloy' ),
					'url'  => admin_url( 'admin.php?page=hubloy_membership-addons' ),
				),
			)
		),
	),
	'stats'       => array(
		'title'   => array(
			'subscribers'  => __( 'Subscriber Data', 'memberships-by-hubloy' ),
			'transactions' => __( 'Transaction Data', 'memberships-by-hubloy' ),
		),
		'charts'  => array(
			'days'         => array(
				'mon'  => __( 'Mon', 'memberships-by-hubloy' ),
				'tue'  => __( 'Tue', 'memberships-by-hubloy' ),
				'wed'  => __( 'Wed', 'memberships-by-hubloy' ),
				'thur' => __( 'Thu', 'memberships-by-hubloy' ),
				'fri'  => __( 'Fri', 'memberships-by-hubloy' ),
				'sat'  => __( 'Sat', 'memberships-by-hubloy' ),
				'sub'  => __( 'Sun', 'memberships-by-hubloy' ),
			),
			'subscribers'  => sprintf( __( '%s of Subscribers', 'memberships-by-hubloy' ), '#' ),
			'transactions' => sprintf( __( '%s of Transactions', 'memberships-by-hubloy' ), '#' ),
		),
		'no_data' => array(
			'subscribers'  => __( 'No data found for this week', 'memberships-by-hubloy' ),
			'transactions' => __( 'No data found for this week', 'memberships-by-hubloy' ),
		),
	),
	'wizard'      => array(
		'title'      => __( 'Initial Setup', 'memberships-by-hubloy' ),
		'settings'   => array(
			'title'    => __( 'General Settings', 'memberships-by-hubloy' ),
			'currency' => array(
				'title'       => __( 'Membership Currency', 'memberships-by-hubloy' ),
				'description' => __( 'This is the currency used to purchase memberships', 'memberships-by-hubloy' ),
			),
		),
		'pages'      => array(
			'title'             => __( 'Membership Pages', 'memberships-by-hubloy' ),
			'membership_list'   => array(
				'title'       => __( 'Membership List', 'memberships-by-hubloy' ),
				'description' => __( 'List of public memberships', 'memberships-by-hubloy' ),
			),
			'protected_content' => array(
				'title'       => __( 'Protected Content', 'memberships-by-hubloy' ),
				'description' => __( 'Displayed when a user cannot access the requested page', 'memberships-by-hubloy' ),
			),
			'account_page'      => array(
				'title'       => __( 'Account', 'memberships-by-hubloy' ),
				'description' => __( 'Shows details about the current user', 'memberships-by-hubloy' ),
			),
		),
		'membership' => array(
			'title'    => __( 'Create your first membership', 'memberships-by-hubloy' ),
			'labels'   => array(
				'name'               => __( 'Membership Name', 'memberships-by-hubloy' ),
				'status'             => __( 'Status', 'memberships-by-hubloy' ),
				'type'               => __( 'Membership Type', 'memberships-by-hubloy' ),
				'price'              => __( 'Membership Price', 'memberships-by-hubloy' ),
				'days'               => __( 'Grant access for the following days', 'memberships-by-hubloy' ),
				'recurring_duration' => __( 'Recurring Frequency', 'memberships-by-hubloy' ),
			),
			'type'     => \HubloyMembership\Services\Memberships::payment_types(),
			'duration' => \HubloyMembership\Services\Memberships::payment_durations(),
		),
	),
);
