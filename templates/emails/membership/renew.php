<?php
/**
 * Membership Renewed
 *
 * This template can be overridden by copying it to yourtheme/hammock/emails/membership/renew.php.
 * 
 * @package Hammock/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hammock_email_header', $heading, $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'hammock' ), esc_html( $object->user_name ) ); ?>

<?php
do_action( 'hammock_email_footer', $email );