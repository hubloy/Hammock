<?php
/**
 * Member Account Update
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/emails/member-account-update.php.
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
<p><?php esc_html_e( 'This is to confirm you made an update to your account', 'memberships-by-hubloy' ); ?></p>
<?php
do_action( 'hubloy_membership_email_footer', $email );
