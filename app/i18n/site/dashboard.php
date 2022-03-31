<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Dashboard strings
 */
return array(
	'members'     => array(
		'title' => __( 'Members', 'hubloy-membership' ),
		'none'  => __( 'No Members Found', 'hubloy-membership' ),
		'url'   => admin_url( 'admin.php?page=hubloy-membership-members' ),
	),
	'memberships' => array(
		'title' => __( 'Memberships', 'hubloy-membership' ),
		'none'  => __( 'No Memberships Found', 'hubloy-membership' ),
		'url'   => admin_url( 'admin.php?page=hubloy-membership-memberships' ),
	),
	'management'  => array(
		'title' => __( 'Site Management', 'hubloy-membership' ),
		'types' => apply_filters(
			'hubloy-membership_dashboard_links',
			array(
				'memberships' => array(
					'name' => __( 'View Memberships', 'hubloy-membership' ),
					'url'  => admin_url( 'admin.php?page=hubloy-membership-memberships' ),
				),
				'addons'      => array(
					'name' => __( 'View Addons', 'hubloy-membership' ),
					'url'  => admin_url( 'admin.php?page=hubloy-membership-addons' ),
				),
			)
		),
	),
	'stats'       => array(
		'title'   => array(
			'subscribers'  => __( 'Subscriber Data', 'hubloy-membership' ),
			'transactions' => __( 'Transaction Data', 'hubloy-membership' ),
		),
		'charts'  => array(
			'days'         => array(
				'mon'  => __( 'Mon', 'hubloy-membership' ),
				'tue'  => __( 'Tue', 'hubloy-membership' ),
				'wed'  => __( 'Wed', 'hubloy-membership' ),
				'thur' => __( 'Thu', 'hubloy-membership' ),
				'fri'  => __( 'Fri', 'hubloy-membership' ),
				'sat'  => __( 'Sat', 'hubloy-membership' ),
				'sub'  => __( 'Sun', 'hubloy-membership' ),
			),
			'subscribers'  => sprintf( __( '%s of Subscribers', 'hubloy-membership' ), '#' ),
			'transactions' => sprintf( __( '%s of Transactions', 'hubloy-membership' ), '#' ),
		),
		'no_data' => array(
			'subscribers'  => __( 'No data found for this week', 'hubloy-membership' ),
			'transactions' => __( 'No data found for this week', 'hubloy-membership' ),
		),
	),
	'wizard'      => array(
		'title'      => __( 'Initial Setup', 'hubloy-membership' ),
		'settings'   => array(
			'title'    => __( 'General Settings', 'hubloy-membership' ),
			'currency' => array(
				'title'       => __( 'Membership Currency', 'hubloy-membership' ),
				'description' => __( 'This is the currency used to purchase memberships', 'hubloy-membership' ),
			),
		),
		'pages'      => array(
			'title'             => __( 'Membership Pages', 'hubloy-membership' ),
			'membership_list'   => array(
				'title'       => __( 'Membership List', 'hubloy-membership' ),
				'description' => __( 'List of public memberships', 'hubloy-membership' ),
			),
			'protected_content' => array(
				'title'       => __( 'Protected Content', 'hubloy-membership' ),
				'description' => __( 'Displayed when a user cannot access the requested page', 'hubloy-membership' ),
			),
			'account_page'      => array(
				'title'       => __( 'Account', 'hubloy-membership' ),
				'description' => __( 'Shows details about the current user', 'hubloy-membership' ),
			),
		),
		'membership' => array(
			'title'    => __( 'Create your first membership', 'hubloy-membership' ),
			'labels'   => array(
				'name'               => __( 'Membership Name', 'hubloy-membership' ),
				'status'             => __( 'Status', 'hubloy-membership' ),
				'type'               => __( 'Membership Type', 'hubloy-membership' ),
				'price'              => __( 'Membership Price', 'hubloy-membership' ),
				'days'               => __( 'Grant access for the following days', 'hubloy-membership' ),
				'recurring_duration' => __( 'Recurring Frequency', 'hubloy-membership' ),
			),
			'type'     => \HubloyMembership\Services\Memberships::payment_types(),
			'duration' => \HubloyMembership\Services\Memberships::payment_durations(),
		),
	),
);
