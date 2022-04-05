<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Rules strings
 */
return array(
	'dashboard' => array(
		'add_new' => array(
			'button' => __( 'Create Rule', 'hubloy-membership' ),
			'modal'  => array(
				'title'      => __( 'New Rule', 'hubloy-membership' ),
				'rule'       => __( 'Select Rule', 'hubloy-membership' ),
				'item'       => __( 'Select Item to protect', 'hubloy-membership' ),
				'membership' => __( 'Membership', 'hubloy-membership' ),
			),
		),
		'table'   => array(
			'id'     => __( 'ID', 'hubloy-membership' ),
			'desc'   => __( 'Description', 'hubloy-membership' ),
			'status' => __( 'Status', 'hubloy-membership' ),
			'type'   => __( 'Type', 'hubloy-membership' ),
			'date'   => __( 'Date Created', 'hubloy-membership' ),
			'delete' => array(
				'prompt' => __( 'Are you sure you want to delete this rule?', 'hubloy-membership' ),
			),
		),
		'none'     => __( 'No protection rules found', 'hubloy-membership' ),
		'disabled' => __( 'Content protection is currently disabled', 'hubloy-membership' ),
	),
);
