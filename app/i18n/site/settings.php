<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Subsite settings strings
 */

return array(
	'content_protection'   => array(
		'title'       => __( 'Content Protection', 'memberships-by-hubloy' ),
		'description' => __( 'This setting toggles the content protection on this site', 'memberships-by-hubloy' ),
	),
	'admin_toolbar'        => array(
		'title'       => __( 'Hide admin toolbar', 'memberships-by-hubloy' ),
		'description' => __( 'Hide the admin toolbar for non administrator users', 'memberships-by-hubloy' ),
	),
	'account_verification' => array(
		'title'       => __( 'Force account verification', 'memberships-by-hubloy' ),
		'description' => __( 'This will force all registered accounts to first verify their emails before login', 'memberships-by-hubloy' ),
	),
	'settings'             => array(
		'title'      => __( 'General Settings', 'memberships-by-hubloy' ),
		'currency'   => array(
			'title'       => __( 'Membership Currency', 'memberships-by-hubloy' ),
			'description' => __( 'This is the currency used to purchase memberships', 'memberships-by-hubloy' ),
		),
		'invoice'    => array(
			'title'       => __( 'Invoice Prefix', 'memberships-by-hubloy' ),
			'description' => __( 'This is the prefix used on all invoices generated. This will not update existing invoices', 'memberships-by-hubloy' ),
		),
		'protection' => array(
			'title'       => __( 'Content Protection Mode', 'memberships-by-hubloy' ),
			'description' => __( 'Specifies the way content is restricted: whether to show nothing, excerpts, or send to a protected content', 'memberships-by-hubloy' ),
			'options'     => array(
				'hide'         => __( 'Hide completely', 'memberships-by-hubloy' ),
				'hide_content' => __( 'Hide content only', 'memberships-by-hubloy' ),
				'redirect'     => __( 'Redirect to page', 'memberships-by-hubloy' ),
			),
		),
	),
	'pages'                => array(
		'title'             => __( 'Membership Pages', 'memberships-by-hubloy' ),
		'membership_list'   => array(
			'title'       => __( 'Membership List', 'memberships-by-hubloy' ),
			'description' => __( 'List of public memberships', 'memberships-by-hubloy' ),
		),
		'protected_content' => array(
			'title'       => __( 'Protected Content', 'memberships-by-hubloy' ),
			'description' => __( 'Displayed when a user cannot access the requested page', 'memberships-by-hubloy' ),
		),
		'account_page'      => array(
			'title'       => __( 'Account', 'memberships-by-hubloy' ),
			'description' => __( 'Shows details about the current user', 'memberships-by-hubloy' ),
		),
	),
	'data'                 => array(
		'title'               => __( 'Data Management', 'memberships-by-hubloy' ),
		'delete_on_uninstall' => array(
			'title'       => __( 'Delete data on uninstall', 'memberships-by-hubloy' ),
			'description' => __( 'This will delete all options and custom database tables once you deactivate and uninstall the plugin from your WordPress dashboard', 'memberships-by-hubloy' ),
		),
	),
	'ipn'                  => __( 'IPN URL', 'memberships-by-hubloy' ),
);

