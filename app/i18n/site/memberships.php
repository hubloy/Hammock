<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Membership strings
 */

return array(
	'labels'    => array(
		'name'                 => __( 'Membership Name', 'memberships-by-hubloy' ),
		'details'              => __( 'Membership Description', 'memberships-by-hubloy' ),
		'status'               => __( 'Status', 'memberships-by-hubloy' ),
		'type'                 => __( 'Membership Type', 'memberships-by-hubloy' ),
		'price'                => __( 'Membership Price', 'memberships-by-hubloy' ),
		'signup_price'         => __( 'Sign-up Price', 'memberships-by-hubloy' ),
		'days'                 => __( 'Grant access for the following days', 'memberships-by-hubloy' ),
		'limit_access'         => __( 'Limit number of members', 'memberships-by-hubloy' ),
		'total_available'      => __( 'Total Available', 'memberships-by-hubloy' ),
		'total_available_desc' => __( 'Total spots available for this membership', 'memberships-by-hubloy' ),
		'recurring_duration'   => __( 'Recurring Frequency', 'memberships-by-hubloy' ),
		'trial'                => __( 'Trial', 'memberships-by-hubloy' ),
		'trial_price'          => __( 'Trial Price', 'memberships-by-hubloy' ),
		'trial_duration'       => __( 'Trial Duration', 'memberships-by-hubloy' ),
		'invite_only'          => __( 'Invite Only', 'memberships-by-hubloy' ),
		'invite_list'          => __( 'Restrict to these codes', 'memberships-by-hubloy' ),

	),

	'dashboard' => array(
		'add_new' => array(
			'button' => __( 'Create New Membership', 'memberships-by-hubloy' ),
			'modal'  => array(
				'title' => __( 'New Membership', 'memberships-by-hubloy' ),
			),
		),
		'table'   => array(
			'name'             => __( 'Membership Name', 'memberships-by-hubloy' ),
			'active'           => __( 'Active', 'memberships-by-hubloy' ),
			'type_description' => __( 'Type of Membership', 'memberships-by-hubloy' ),
			'members'          => __( 'Members', 'memberships-by-hubloy' ),
			'price'            => __( 'Price', 'memberships-by-hubloy' ),
			'shortcode'        => __( 'Shortcodes', 'memberships-by-hubloy' ),
			'not_found'        => __( 'No memberships found', 'memberships-by-hubloy' ),
		),
	),
	'edit'      => array(
		'title'     => __( 'Edit Membership', 'memberships-by-hubloy' ),
		'back'      => __( 'Back To Memberships', 'memberships-by-hubloy' ),
		'not_found' => __( 'Membership with that id not found', 'memberships-by-hubloy' ),
		'tabs'      => array(
			'general' => __( 'General', 'memberships-by-hubloy' ),
			'price'   => __( 'Price', 'memberships-by-hubloy' ),
			'rules'   => __( 'Rules', 'memberships-by-hubloy' ),
		),
		'invites'   => array(
			'select' => __( 'Select Invite codes', 'memberships-by-hubloy' ),
			'empty'  => __( 'No Invite codes found', 'memberships-by-hubloy' ),
		),
	),
);

