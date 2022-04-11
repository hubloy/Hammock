<?php
/**
 * Member Verify Account
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/emails/member-verify-account.php.
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
<p><?php printf( esc_html__( 'Thank you for creating an account on %s:', 'memberships-by-hubloy' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ); ?></p>
<p><?php printf( esc_html__( 'Username: %s', 'memberships-by-hubloy' ), esc_html( $object->user_login ) ); ?></p>
<p><?php esc_html_e( 'To proceed:', 'memberships-by-hubloy' ); ?></p>
<p>
	<a class="link" href="
	<?php
	echo esc_url(
		add_query_arg(
			array(
				'verify' => $object->verify_key,
				'id'     => $object->user_id,
			),
			hubloy_membership_get_account_url()
		)
	);
	?>
	">
		<?php esc_html_e( 'Click here to verify your account', 'memberships-by-hubloy' ); ?>
	</a>
</p>
<?php
do_action( 'hubloy_membership_email_footer', $email );
