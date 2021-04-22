<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Membership strings
 */

return array(
	'labels'    => array(
		'name'                 => __( 'Membership Name', 'hammock' ),
		'details'              => __( 'Membership Description', 'hammock' ),
		'status'               => __( 'Status', 'hammock' ),
		'type'                 => __( 'Membership Type', 'hammock' ),
		'price'                => __( 'Membership Price', 'hammock' ),
		'signup_price'         => __( 'Sign-up Price', 'hammock' ),
		'days'           	   => __( 'Grant access for the following days', 'hammock' ),
		'limit_access'         => __( 'Limit number of members', 'hammock' ),
		'total_available'      => __( 'Total Available', 'hammock' ),
		'total_available_desc' => __( 'Total spots available for this membership', 'hammock' ),
		'recurring_duration'   => __( 'Recurring Frequency', 'hammock' ),
		'trial'                => __( 'Trial', 'hammock' ),
		'trial_price'          => __( 'Trial Price', 'hammock' ),
		'trial_duration'       => __( 'Trial Duration', 'hammock' ),
		'invite_only'          => __( 'Invite Only', 'hammock' ),
		'invite_list'          => __( 'Restrict to these codes', 'hammock' ),
		
	),

	'dashboard' => array(
		'add_new' => array(
			'button' => __( 'Create New Membership', 'hammock' ),
			'modal'  => array(
				'title' => __( 'New Membership', 'hammock' ),
			),
		),
		'table'   => array(
			'name'             => __( 'Membership Name', 'hammock' ),
			'active'           => __( 'Active', 'hammock' ),
			'type_description' => __( 'Type of Membership', 'hammock' ),
			'members'          => __( 'Members', 'hammock' ),
			'price'            => __( 'Price', 'hammock' ),
			'shortcode'        => __( 'Shortcodes', 'hammock' ),
			'not_found'        => __( 'No memberships found', 'hammock' ),
		),
	),
	'edit'      => array(
		'title'     => __( 'Edit Membership', 'hammock' ),
		'back'      => __( 'Back To Memberships', 'hammock' ),
		'not_found' => __( 'Membership with that id not found', 'hammock' ),
		'tabs'      => array(
			'general' => __( 'General', 'hammock' ),
			'price'   => __( 'Price', 'hammock' ),
		),
		'invites'	=> array(
			'select'	=> __( 'Select Invite codes', 'hammock' ),
			'empty'		=> __( 'No Invite codes fount', 'hammock' ),
		)
	),
);

