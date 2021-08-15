<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Dashboard strings
 */
return array(
	'members'	=> array(
		'title'	=> __( 'Members', 'hammock' ),
		'none'	=> __( 'No Members Found', 'hammock' ),
		'url'	=> admin_url( 'admin.php?page=hammock-members' )
	),
	'memberships'	=> array(
		'title'	=> __( 'Memberships', 'hammock' ),
		'none'	=> __( 'No Memberships Found', 'hammock' ),
		'url'	=> admin_url( 'admin.php?page=hammock-memberships' )
	),
	'management'	=> array(
		'title'	=> __( 'Site Management', 'hammock' ),
		'types'	=> apply_filters( 'hammock_dashboard_links',  array(
			'memberships' => array(
				'name'	=> __( 'View Memberships', 'hammock' ),
				'url'	=> admin_url( 'admin.php?page=hammock-memberships' )
			),
			'addons' => array(
				'name'	=> __( 'View Addons', 'hammock' ),
				'url'	=> admin_url( 'admin.php?page=hammock-addons' )
			)
		) )
	),
	'stats'	=> array(
		'title'		=> __( 'Stats Overview', 'hammock' ),
		'charts'	=> array(
			'days'			=> array(
				'mon' 	=> __( 'Mon', 'hammock' ),
				'tue' 	=> __( 'Tue', 'hammock' ),
				'wed'	=> __( 'Wed', 'hammock' ),
				'thur'	=> __( 'Thu', 'hammock' ),
				'fri'	=> __( 'Fri', 'hammock' ),
				'sat'	=> __( 'Sat', 'hammock' ),
				'sub'	=> __( 'Sun', 'hammock' )
			),
			'subscribers'	=> sprintf( __( '%s of Subscribers', 'hammock' ), '#' ),
			'transactions'	=> sprintf( __( '%s of Transactions', 'hammock' ), '#' )
		),
		'no_data' => array(
			'subscribers'	=> __( 'No Subscriber Data found for this week', 'hammock' ),
			'transactions'	=> __( 'No Transaction Data found for this week', 'hammock' )
		)
	),
	'wizard'	=> array(
		'title'			=> __( 'Initial Setup', 'hammock' ),
		'settings'		=> array(
			'currency' => array(
				'title'       => __( 'Membership Currency', 'hammock' ),
				'description' => __( 'This is the currency used to purchase memberships', 'hammock' ),
			)
		),
		'pages'			=> array(
			'title'             => __( 'Membership Pages', 'hammock' ),
			'membership_list'   => array(
				'title'       => __( 'Membership List', 'hammock' ),
				'description' => __( 'List of public memberships', 'hammock' ),
			),
			'protected_content' => array(
				'title'       => __( 'Protected Content', 'hammock' ),
				'description' => __( 'Displayed when a user cannot access the requested page', 'hammock' ),
			),
			'account_page'      => array(
				'title'       => __( 'Account', 'hammock' ),
				'description' => __( 'Shows details about the current user', 'hammock' ),
			),
		),
		'membership'	=> array(
			'labels'    => array(
				'name'                 => __( 'Membership Name', 'hammock' ),
				'status'               => __( 'Status', 'hammock' ),
				'type'                 => __( 'Membership Type', 'hammock' ),
				'price'                => __( 'Membership Price', 'hammock' ),
				'days'           	   => __( 'Grant access for the following days', 'hammock' ),
				'recurring_duration'   => __( 'Recurring Frequency', 'hammock' ),
			),
		)
	)
);