<?php
/**
 * Account dashboard
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/dashboard.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p>
<?php
	printf(
		esc_html__( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'hubloy_membership' ),
		'<strong>' . esc_html( $current_user->name ) . '</strong>',
		esc_url( hubloy_membership_logout_url() )
	);
	?>
</p>
