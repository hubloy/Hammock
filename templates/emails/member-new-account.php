<?php
/**
 * Member New Account
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/emails/member-new-account.php.
 *
 * @package HubloyMembership/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_email_header', $heading, $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'hubloy_membership' ), esc_html( $object->user_login ) ); ?></p>
<p><?php printf( esc_html__( 'Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to manage your subscription, change your password, and more at: %3$s', 'hubloy_membership' ), esc_html( $blog_name ), '<strong>' . esc_html( $object->user_login ) . '</strong>', make_clickable( esc_url( hubloy_membership_get_account_url() ) ) ); ?></p>
<?php
do_action( 'hubloy_membership_email_footer', $email );
