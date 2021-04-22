<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return array(
	'not_found'		=> __( 'No codes found', 'hammock' ),
	'select_email'	=> __( 'Email', 'hammock' ),
	'add'		=> array(
		'coupon' => __( 'New Coupon', 'hammock' ),
		'invite' => __( 'New Invite Code', 'hammock' ),
	),
	'edit'		=> array(
		'coupon' => __( 'Editing Coupon', 'hammock' ),
		'invite' => __( 'Editing Invite Code', 'hammock' ),
	),
	'table'		=> array(
		'id'		=> __( 'ID', 'hammock' ),
		'code' 		=> __( 'Code', 'hammock' ),
		'status'	=> __( 'Status', 'hammock' ),
		'amount'	=> __( 'Amount', 'hammock' ),
		'author'	=> __( 'Author', 'hammock' ),
		'date'		=> __( 'Date Created', 'hammock' ),
	),
	'coupons' => array(
		'types'	=> array(
			'percentage' 	=> __( 'Percentage Discount', 'hammock' ),
			'fixed' 		=> __( 'Fixed Amount', 'hammock' )
		),
	),
	'create'	=> array(
		'coupons'	=> array(
			'code' 	=> array(
				'title' 		=> __( 'Coupon Code', 'hammock' ),
				'description'	=> __( 'The Coupon Code. If left blank this will be generated for you', 'hammock' )
			),
			'status' 	=> array(
				'title' 		=> __( 'Coupon Status', 'hammock' ),
				'description'	=> __( 'The Coupon Status', 'hammock' )
			),
			'amount' 	=> array(
				'title' 		=> __( 'Coupon Amount', 'hammock' ),
				'description'	=> __( 'The Coupon Amount', 'hammock' )
			),
			'amount_type' 	=> array(
				'title' 		=> __( 'Discount Type', 'hammock' ),
				'description'	=> __( 'The Coupon Discount Type ', 'hammock' )
			),
			'expire' 	=> array(
				'title' 		=> __( 'Coupon expiry date', 'hammock' ),
				'description'	=> __( 'The last day the coupon is valid for', 'hammock' )
			),
			'restrict' 	=> array(
				'title' 		=> __( 'Email Restrict', 'hammock' ),
				'description'	=> __( 'Restrict usage to specific emails. Add an email address then press enter', 'hammock' )
			),
			'usage' 	=> array(
				'title' 		=> __( 'Limit usage pre user', 'hammock' ),
				'description'	=> __( 'How many times this coupon can be used per user email or user id for logged in users', 'hammock' )
			)
		),
		'invites'	=> array(
			'code' 	=> array(
				'title' 		=> __( 'Invitation Code', 'hammock' ),
				'description'	=> __( 'The Invite Code. If left blank this will be generated for you', 'hammock' )
			),
			'status' 	=> array(
				'title' 		=> __( 'Invitation Code Status', 'hammock' ),
				'description'	=> __( 'The Invitation Code Status', 'hammock' )
			),
			'expire' 	=> array(
				'title' 		=> __( 'Invitation Code expiry date', 'hammock' ),
				'description'	=> __( 'The last day the invite code is valid for', 'hammock' )
			),
			'restrict' 	=> array(
				'title' 		=> __( 'Email Restrict', 'hammock' ),
				'description'	=> __( 'Restrict usage to specific emails. Add an email address then press enter', 'hammock' )
			)
		)
	)
);
?>