<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Subsite settings strings
 */

return array(
	'nav'       => array(
		'mailchimp' => __( 'MailChimp', 'hubloy-membership' ),
	),
	'mailchimp' => array(
		'title'    => __( 'MailChimp integration', 'hubloy-membership' ),
		'desc'     => __( 'Mailchimp is the All-In-One integrated marketing platform for small businesses, to grow your business on your terms.', 'hubloy-membership' ),
		'info'     => sprintf( __( 'Visit %1$syour API dashboard%2$s to create an API Key.', 'hubloy-membership' ), '<a class="uk-text-primary" href="http://admin.mailchimp.com/account/api">', '</a>' ),
		'apikey'   => __( 'API Key', 'hubloy-membership' ),
		'validate' => __( 'Validate', 'hubloy-membership' ),
		'opt_in'   => array(
			'label'       => __( 'Automatically opt-in new users to the mailing list.', 'hubloy-membership' ),
			'description' => __( 'Users will not receive an email confirmation. You are responsible to inform your users.', 'hubloy-membership' ),
		),
		'lists'    => array(
			'registered'   => __( 'Registered users mailing list (not members)', 'hubloy-membership' ),
			'subscriber'   => __( 'Members mailing list', 'hubloy-membership' ),
			'unsubscriber' => __( 'Deactivated memberships mailing list', 'hubloy-membership' ),
		),
	),
);
