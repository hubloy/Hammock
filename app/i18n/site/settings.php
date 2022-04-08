<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Subsite settings strings
 */

return array(
	'content_protection'   => array(
		'title'       => __( 'Content Protection', 'hubloy-membership' ),
		'description' => __( 'This setting toggles the content protection on this site', 'hubloy-membership' ),
	),
	'admin_toolbar'        => array(
		'title'       => __( 'Hide admin toolbar', 'hubloy-membership' ),
		'description' => __( 'Hide the admin toolbar for non administrator users', 'hubloy-membership' ),
	),
	'account_verification' => array(
		'title'       => __( 'Force account verification', 'hubloy-membership' ),
		'description' => __( 'This will force all registered accounts to first verify their emails before login', 'hubloy-membership' ),
	),
	'settings'             => array(
		'title'      => __( 'General Settings', 'hubloy-membership' ),
		'currency'   => array(
			'title'       => __( 'Membership Currency', 'hubloy-membership' ),
			'description' => __( 'This is the currency used to purchase memberships', 'hubloy-membership' ),
		),
		'invoice'    => array(
			'title'       => __( 'Invoice Prefix', 'hubloy-membership' ),
			'description' => __( 'This is the prefix used on all invoices generated. This will not update existing invoices', 'hubloy-membership' ),
		),
		'protection' => array(
			'title'       => __( 'Content Protection Mode', 'hubloy-membership' ),
			'description' => __( 'Specifies the way content is restricted: whether to show nothing, excerpts, or send to a protected content', 'hubloy-membership' ),
			'options'     => array(
				'hide'         => __( 'Hide completely', 'hubloy-membership' ),
				'hide_content' => __( 'Hide content only', 'hubloy-membership' ),
				'redirect'     => __( 'Redirect to page', 'hubloy-membership' ),
			),
		),
	),
	'pages'                => array(
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
	'data'                 => array(
		'title'               => __( 'Data Management', 'hubloy-membership' ),
		'delete_on_uninstall' => array(
			'title'       => __( 'Delete data on uninstall', 'hubloy-membership' ),
			'description' => __( 'This will delete all options and custom database tables once you deactivate and uninstall the plugin from your WordPress dashboard', 'hubloy-membership' ),
		),
	),
	'ipn' => __( 'IPN URL', 'hubloy-membership' ),
);

