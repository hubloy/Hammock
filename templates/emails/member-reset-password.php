<?php
/**
 * Member Reset Password
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/emails/member-reset-password.php.
 *
 * @package HubloyMembership/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_email_header', $heading, $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'memberships-by-hubloy' ), esc_html( $object->user_login ) ); ?>
<p><?php printf( esc_html__( 'Someone has requested a new password for the following account on %s:', 'memberships-by-hubloy' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ); ?></p>
<p><?php printf( esc_html__( 'Username: %s', 'memberships-by-hubloy' ), esc_html( $object->user_login ) ); ?></p>
<p><?php esc_html_e( 'If you didn\'t make this request, just ignore this email. If you\'d like to proceed:', 'memberships-by-hubloy' ); ?></p>
<p>
	<a class="link" href="{reset_url}">
		<?php esc_html_e( 'Click here to reset your password', 'memberships-by-hubloy' ); ?>
	</a>
</p>
<?php
do_action( 'hubloy_membership_email_footer', $email );
