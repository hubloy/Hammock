<?php
/**
 * Membership Payment Due
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/emails/membership/payment/due.php.
 *
 * @package HubloyMembership/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_email_header', $heading, $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'hubloy_membership' ), esc_html( $object->user_name ) ); ?>

<?php
do_action( 'hubloy_membership_email_footer', $email );
