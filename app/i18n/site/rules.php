<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Rules strings
 */
return array(
	'dashboard' => array(
		'add_new'  => array(
			'button' => __( 'Create Rule', 'memberships-by-hubloy' ),
			'modal'  => array(
				'title'      => __( 'New Rule', 'memberships-by-hubloy' ),
				'rule'       => __( 'Select Rule', 'memberships-by-hubloy' ),
				'item'       => __( 'Select Item to protect', 'memberships-by-hubloy' ),
				'membership' => __( 'Membership', 'memberships-by-hubloy' ),
			),
		),
		'table'    => array(
			'id'     => __( 'ID', 'memberships-by-hubloy' ),
			'desc'   => __( 'Description', 'memberships-by-hubloy' ),
			'status' => __( 'Status', 'memberships-by-hubloy' ),
			'type'   => __( 'Type', 'memberships-by-hubloy' ),
			'date'   => __( 'Date Created', 'memberships-by-hubloy' ),
			'delete' => array(
				'prompt' => __( 'Are you sure you want to delete this rule?', 'memberships-by-hubloy' ),
			),
		),
		'none'     => __( 'No protection rules found', 'memberships-by-hubloy' ),
		'disabled' => __( 'Content protection is currently disabled', 'memberships-by-hubloy' ),
	),
);
