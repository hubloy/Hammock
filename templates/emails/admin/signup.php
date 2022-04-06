<?php
/**
 * Admin member subscription signup
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/emails/admin/signup.php.
 *
 * @package HubloyMembership/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_email_header', $heading, $email );
?>
<p><?php esc_html_e( 'Hello,', 'hubloy_membership' ); ?>
<p><?php printf( esc_html__( '%1$s has signed up as a member on your website, %2$s', 'hubloy_membership' ), esc_html( $object->user_login ), esc_html( $blog_name ) ); ?></p>
<?php
do_action( 'hubloy_membership_email_footer', $email );
