<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return array(
	'not_found'    => __( 'No codes found', 'hubloy-membership' ),
	'select_email' => __( 'Email', 'hubloy-membership' ),
	'add'          => array(
		'coupon' => __( 'New Coupon', 'hubloy-membership' ),
		'invite' => __( 'New Invite Code', 'hubloy-membership' ),
	),
	'edit'         => array(
		'coupon' => __( 'Edit Coupon', 'hubloy-membership' ),
		'invite' => __( 'Edit Invite Code', 'hubloy-membership' ),
	),
	'table'        => array(
		'id'     => __( 'ID', 'hubloy-membership' ),
		'code'   => __( 'Code', 'hubloy-membership' ),
		'status' => __( 'Status', 'hubloy-membership' ),
		'amount' => __( 'Amount', 'hubloy-membership' ),
		'author' => __( 'Author', 'hubloy-membership' ),
		'date'   => __( 'Date Created', 'hubloy-membership' ),
	),
	'coupons'      => array(
		'types' => array(
			'percentage' => __( 'Percentage Discount', 'hubloy-membership' ),
			'fixed'      => __( 'Fixed Amount', 'hubloy-membership' ),
		),
	),
	'create'       => array(
		'coupons' => array(
			'code'        => array(
				'title'       => __( 'Coupon Code', 'hubloy-membership' ),
				'description' => __( 'The Coupon Code. If left blank this will be generated for you', 'hubloy-membership' ),
			),
			'status'      => array(
				'title'       => __( 'Coupon Status', 'hubloy-membership' ),
				'description' => __( 'The Coupon Status', 'hubloy-membership' ),
			),
			'amount'      => array(
				'title'       => __( 'Coupon Amount', 'hubloy-membership' ),
				'description' => __( 'The Coupon Amount', 'hubloy-membership' ),
			),
			'amount_type' => array(
				'title'       => __( 'Discount Type', 'hubloy-membership' ),
				'description' => __( 'The Coupon Discount Type ', 'hubloy-membership' ),
			),
			'expire'      => array(
				'title'       => __( 'Coupon expiry date', 'hubloy-membership' ),
				'description' => __( 'The last day the coupon is valid for', 'hubloy-membership' ),
			),
			'restrict'    => array(
				'title'       => __( 'Email Restrict', 'hubloy-membership' ),
				'description' => __( 'Restrict usage to specific emails. Add an email address then press enter', 'hubloy-membership' ),
			),
			'usage'       => array(
				'title'       => __( 'Limit usage pre user', 'hubloy-membership' ),
				'description' => __( 'How many times this coupon can be used per user email or user id for logged in users', 'hubloy-membership' ),
			),
		),
		'invites' => array(
			'code'     => array(
				'title'       => __( 'Invitation Code', 'hubloy-membership' ),
				'description' => __( 'The Invite Code. If left blank this will be generated for you', 'hubloy-membership' ),
			),
			'status'   => array(
				'title'       => __( 'Invitation Code Status', 'hubloy-membership' ),
				'description' => __( 'The Invitation Code Status', 'hubloy-membership' ),
			),
			'expire'   => array(
				'title'       => __( 'Invitation Code expiry date', 'hubloy-membership' ),
				'description' => __( 'The last day the invite code is valid for', 'hubloy-membership' ),
			),
			'restrict' => array(
				'title'       => __( 'Email Restrict', 'hubloy-membership' ),
				'description' => __( 'Restrict usage to specific emails. Add an email address then press enter', 'hubloy-membership' ),
			),
		),
	),
);

