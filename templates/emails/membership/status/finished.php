<?php
/**
 * Membership subscription complete
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/emails/membership/status/finished.php.
 *
 * @package HubloyMembership/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_email_header', $heading, $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'memberships-by-hubloy' ), esc_html( $object->user_name ) ); ?>
<p><?php printf( esc_html_e( 'Your subscription to %1$s has started. Manage your account at : %2$s', 'memberships-by-hubloy' ), esc_html( $object->membership->name ), make_clickable( esc_url( hubloy_membership_get_account_url() ) ) ); ?></p>
<?php
do_action( 'hubloy_membership_email_footer', $email );
