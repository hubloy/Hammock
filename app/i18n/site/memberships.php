<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Membership strings
 */

return array(
	'labels'    => array(
		'name'                 => __( 'Membership Name', 'hubloy-membership' ),
		'details'              => __( 'Membership Description', 'hubloy-membership' ),
		'status'               => __( 'Status', 'hubloy-membership' ),
		'type'                 => __( 'Membership Type', 'hubloy-membership' ),
		'price'                => __( 'Membership Price', 'hubloy-membership' ),
		'signup_price'         => __( 'Sign-up Price', 'hubloy-membership' ),
		'days'                 => __( 'Grant access for the following days', 'hubloy-membership' ),
		'limit_access'         => __( 'Limit number of members', 'hubloy-membership' ),
		'total_available'      => __( 'Total Available', 'hubloy-membership' ),
		'total_available_desc' => __( 'Total spots available for this membership', 'hubloy-membership' ),
		'recurring_duration'   => __( 'Recurring Frequency', 'hubloy-membership' ),
		'trial'                => __( 'Trial', 'hubloy-membership' ),
		'trial_price'          => __( 'Trial Price', 'hubloy-membership' ),
		'trial_duration'       => __( 'Trial Duration', 'hubloy-membership' ),
		'invite_only'          => __( 'Invite Only', 'hubloy-membership' ),
		'invite_list'          => __( 'Restrict to these codes', 'hubloy-membership' ),

	),

	'dashboard' => array(
		'add_new' => array(
			'button' => __( 'Create New Membership', 'hubloy-membership' ),
			'modal'  => array(
				'title' => __( 'New Membership', 'hubloy-membership' ),
			),
		),
		'table'   => array(
			'name'             => __( 'Membership Name', 'hubloy-membership' ),
			'active'           => __( 'Active', 'hubloy-membership' ),
			'type_description' => __( 'Type of Membership', 'hubloy-membership' ),
			'members'          => __( 'Members', 'hubloy-membership' ),
			'price'            => __( 'Price', 'hubloy-membership' ),
			'shortcode'        => __( 'Shortcodes', 'hubloy-membership' ),
			'not_found'        => __( 'No memberships found', 'hubloy-membership' ),
		),
	),
	'edit'      => array(
		'title'     => __( 'Edit Membership', 'hubloy-membership' ),
		'back'      => __( 'Back To Memberships', 'hubloy-membership' ),
		'not_found' => __( 'Membership with that id not found', 'hubloy-membership' ),
		'tabs'      => array(
			'general' => __( 'General', 'hubloy-membership' ),
			'price'   => __( 'Price', 'hubloy-membership' ),
			'rules'   => __( 'Rules', 'hubloy-membership' ),
		),
		'invites'   => array(
			'select' => __( 'Select Invite codes', 'hubloy-membership' ),
			'empty'  => __( 'No Invite codes fount', 'hubloy-membership' ),
		),
	),
);

