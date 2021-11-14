<?php
/**
 * Member Verify Account
 *
 * This template can be overridden by copying it to yourtheme/hammock/emails/member-verify-account.php.
 *
 * @package Hammock/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hammock_email_header', $heading, $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'hammock' ), esc_html( $object->user_login ) ); ?>
<p><?php printf( esc_html__( 'Thank you for creating an account on %s:', 'hammock' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ); ?></p>
<p><?php printf( esc_html__( 'Username: %s', 'hammock' ), esc_html( $object->user_login ) ); ?></p>
<p><?php esc_html_e( 'To proceed:', 'hammock' ); ?></p>
<p>
	<a class="link" href="
	<?php
	echo esc_url(
		add_query_arg(
			array(
				'verify' => $object->verify_key,
				'id'     => $object->user_id,
			),
			hammock_get_account_url()
		)
	);
	?>
	">
		<?php esc_html_e( 'Click here to verify your account', 'hammock' ); ?>
	</a>
</p>
<?php
do_action( 'hammock_email_footer', $email );
