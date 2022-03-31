<?php
/**
 * Member Verify Account
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/emails/member-verify-account.php.
 *
 * @package HubloyMembership/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_email_header', $heading, $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'hubloy_membership' ), esc_html( $object->user_login ) ); ?>
<p><?php printf( esc_html__( 'Thank you for creating an account on %s:', 'hubloy_membership' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ); ?></p>
<p><?php printf( esc_html__( 'Username: %s', 'hubloy_membership' ), esc_html( $object->user_login ) ); ?></p>
<p><?php esc_html_e( 'To proceed:', 'hubloy_membership' ); ?></p>
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
		<?php esc_html_e( 'Click here to verify your account', 'hubloy_membership' ); ?>
	</a>
</p>
<?php
do_action( 'hubloy_membership_email_footer', $email );
