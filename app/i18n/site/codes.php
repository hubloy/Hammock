<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return array(
	'not_found'    => __( 'No codes found', 'memberships-by-hubloy' ),
	'select_email' => __( 'Email', 'memberships-by-hubloy' ),
	'add'          => array(
		'coupon' => __( 'New Coupon', 'memberships-by-hubloy' ),
		'invite' => __( 'New Invite Code', 'memberships-by-hubloy' ),
	),
	'edit'         => array(
		'coupon' => __( 'Edit Coupon', 'memberships-by-hubloy' ),
		'invite' => __( 'Edit Invite Code', 'memberships-by-hubloy' ),
	),
	'table'        => array(
		'id'     => __( 'ID', 'memberships-by-hubloy' ),
		'code'   => __( 'Code', 'memberships-by-hubloy' ),
		'status' => __( 'Status', 'memberships-by-hubloy' ),
		'amount' => __( 'Amount', 'memberships-by-hubloy' ),
		'author' => __( 'Author', 'memberships-by-hubloy' ),
		'date'   => __( 'Date Created', 'memberships-by-hubloy' ),
	),
	'coupons'      => array(
		'types' => array(
			'percentage' => __( 'Percentage Discount', 'memberships-by-hubloy' ),
			'fixed'      => __( 'Fixed Amount', 'memberships-by-hubloy' ),
		),
	),
	'create'       => array(
		'coupons' => array(
			'code'        => array(
				'title'       => __( 'Coupon Code', 'memberships-by-hubloy' ),
				'description' => __( 'The Coupon Code. If left blank this will be generated for you', 'memberships-by-hubloy' ),
			),
			'status'      => array(
				'title'       => __( 'Coupon Status', 'memberships-by-hubloy' ),
				'description' => __( 'The Coupon Status', 'memberships-by-hubloy' ),
			),
			'amount'      => array(
				'title'       => __( 'Coupon Amount', 'memberships-by-hubloy' ),
				'description' => __( 'The Coupon Amount', 'memberships-by-hubloy' ),
			),
			'amount_type' => array(
				'title'       => __( 'Discount Type', 'memberships-by-hubloy' ),
				'description' => __( 'The Coupon Discount Type ', 'memberships-by-hubloy' ),
			),
			'expire'      => array(
				'title'       => __( 'Coupon expiry date', 'memberships-by-hubloy' ),
				'description' => __( 'The last day the coupon is valid for', 'memberships-by-hubloy' ),
			),
			'restrict'    => array(
				'title'       => __( 'Email Restrict', 'memberships-by-hubloy' ),
				'description' => __( 'Restrict usage to specific emails. Add an email address then press enter', 'memberships-by-hubloy' ),
			),
			'usage'       => array(
				'title'       => __( 'Limit usage pre user', 'memberships-by-hubloy' ),
				'description' => __( 'How many times this coupon can be used per user email or user id for logged in users', 'memberships-by-hubloy' ),
			),
		),
		'invites' => array(
			'code'     => array(
				'title'       => __( 'Invitation Code', 'memberships-by-hubloy' ),
				'description' => __( 'The Invite Code. If left blank this will be generated for you', 'memberships-by-hubloy' ),
			),
			'status'   => array(
				'title'       => __( 'Invitation Code Status', 'memberships-by-hubloy' ),
				'description' => __( 'The Invitation Code Status', 'memberships-by-hubloy' ),
			),
			'expire'   => array(
				'title'       => __( 'Invitation Code expiry date', 'memberships-by-hubloy' ),
				'description' => __( 'The last day the invite code is valid for', 'memberships-by-hubloy' ),
			),
			'restrict' => array(
				'title'       => __( 'Email Restrict', 'memberships-by-hubloy' ),
				'description' => __( 'Restrict usage to specific emails. Add an email address then press enter', 'memberships-by-hubloy' ),
			),
		),
	),
);

