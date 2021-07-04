<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Subsite settings strings
 */

return array(
	'nav'	=> array(
		'mailchimp'	=> __( 'MailChimp', 'hammock' )
	),
	'mailchimp'	=> array(
		'enabled'	=> __( 'Enable MailChimp integration', 'hammock' ),
		'info'		=> sprintf( __( 'Visit %syour API dashboard%s to create an API Key.', 'hammock' ), '<a class="uk-text-primary" href="http://admin.mailchimp.com/account/api">', '</a>' ),
		'apikey'	=> __( 'API Key', 'hammock' ),
		'validate'	=> __( 'Validate', 'hammock' ),
		'opt_in'	=> array(
			'label'			=> __( 'Automatically opt-in new users to the mailing list.', 'hammock' ),
			'description' 	=> __( 'Users will not receive an email confirmation. You are responsible to inform your users.', 'hammock' ),
		),
		'lists'		=> array(
			'registered' 	=> __( 'Registered users mailing list (not members)', 'hammock' ),
			'subscriber' 	=> __( 'Members mailing list', 'hammock' ),
			'unsubscriber' 	=> __( 'Deactivated memberships mailing list', 'hammock' )
		)
	)
);