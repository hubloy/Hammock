<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Subsite settings strings
 */

return array(
	'content_protection'   => array(
		'title'       => __( 'Content Protection', 'hammock' ),
		'description' => __( 'This setting toggles the content protection on this site', 'hammock' ),
	),
	'admin_toolbar'        => array(
		'title'       => __( 'Hide admin toolbar', 'hammock' ),
		'description' => __( 'Hide the admin toolbar for non administrator users', 'hammock' ),
	),
	'account_verification' => array(
		'title'       => __( 'Force account verification', 'hammock' ),
		'description' => __( 'This will force all registered accounts to first verify their emails before login', 'hammock' ),
	),
	'settings'             => array(
		'title'      => __( 'General Settings', 'hammock' ),
		'currency'   => array(
			'title'       => __( 'Membership Currency', 'hammock' ),
			'description' => __( 'This is the currency used to purchase memberships', 'hammock' ),
		),
		'invoice'    => array(
			'title'       => __( 'Invoice Prefix', 'hammock' ),
			'description' => __( 'This is the prefix used on all invoices generated. This will not update existing invoices', 'hammock' ),
		),
		'protection' => array(
			'title'       => __( 'Content Protection Mode', 'hammock' ),
			'description' => __( 'Specifies the way content is restricted: whether to show nothing, excerpts, or send to a protected content', 'hammock' ),
			'options'     => array(
				'hide'         => __( 'Hide completely', 'hammock' ),
				'hide_content' => __( 'Hide content only', 'hammock' ),
				'redirect'     => __( 'Redirect to page', 'hammock' ),
			),
		),
	),
	'pages'                => array(
		'title'             => __( 'Membership Pages', 'hammock' ),
		'membership_list'   => array(
			'title'       => __( 'Membership List', 'hammock' ),
			'description' => __( 'List of public memberships', 'hammock' ),
		),
		'protected_content' => array(
			'title'       => __( 'Protected Content', 'hammock' ),
			'description' => __( 'Displayed when a user cannot access the requested page', 'hammock' ),
		),
		'account_page'      => array(
			'title'       => __( 'Account', 'hammock' ),
			'description' => __( 'Shows details about the current user', 'hammock' ),
		),
	),
	'data'                 => array(
		'title'               => __( 'Data Management', 'hammock' ),
		'delete_on_uninstall' => array(
			'title'       => __( 'Delete data on uninstall', 'hammock' ),
			'description' => __( 'This will delete all options and custom database tables once you deactivate and uninstall the plugin from your WordPress dashboard', 'hammock' ),
		),
	),
);

