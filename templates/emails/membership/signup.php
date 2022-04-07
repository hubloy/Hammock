<?php
/**
 * Membership Signup
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/emails/membership/signup.php.
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
<p><?php printf( esc_html_e( 'You have just signed up to %1$s . Manage your account at : %2$s', 'hubloy_membership' ), esc_html( $object->membership ), make_clickable( esc_url( hubloy_membership_get_account_url() ) ) ); ?></p>
<?php
do_action( 'hubloy_membership_email_footer', $email );
