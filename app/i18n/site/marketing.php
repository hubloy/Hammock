<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Subsite settings strings
 */

return array(
	'nav'       => array(
		'mailchimp' => __( 'MailChimp', 'memberships-by-hubloy' ),
	),
	'mailchimp' => array(
		'title'    => __( 'MailChimp integration', 'memberships-by-hubloy' ),
		'desc'     => __( 'Mailchimp is the All-In-One integrated marketing platform for small businesses, to grow your business on your terms.', 'memberships-by-hubloy' ),
		'info'     => sprintf( __( 'Visit %1$syour API dashboard%2$s to create an API Key.', 'memberships-by-hubloy' ), '<a class="uk-text-primary" href="http://admin.mailchimp.com/account/api">', '</a>' ),
		'apikey'   => __( 'API Key', 'memberships-by-hubloy' ),
		'validate' => __( 'Validate', 'memberships-by-hubloy' ),
		'opt_in'   => array(
			'label'       => __( 'Automatically opt-in new users to the mailing list.', 'memberships-by-hubloy' ),
			'description' => __( 'Users will not receive an email confirmation. You are responsible to inform your users.', 'memberships-by-hubloy' ),
		),
		'lists'    => array(
			'registered'   => __( 'Registered users mailing list (not members)', 'memberships-by-hubloy' ),
			'subscriber'   => __( 'Members mailing list', 'memberships-by-hubloy' ),
			'unsubscriber' => __( 'Deactivated memberships mailing list', 'memberships-by-hubloy' ),
		),
	),
);
