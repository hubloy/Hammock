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
		'info'		=> sprintf( __( 'Visit <a class="uk-text-primary" href="%s" target="_blank">your API dashboard</a> to create an API Key.', 'hammock' ), "http://admin.mailchimp.com/account/api" ),
		'apikey'	=> __( 'API Key', 'hammock' ),
		'validate'	=> __( 'Validate', 'hammock' ),
	)
);