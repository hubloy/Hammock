<?php
/**
 * Account dashboard
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/dashboard.php.
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
		esc_html__( 'Hello %1$s (not %1$s? %2$sLog out%3$s)', 'memberships-by-hubloy' ),
		
		'<strong>' . esc_html( $current_user->name ) . '</strong>',
		sprintf( '<a href="%s">', esc_url( hubloy_membership_logout_url() ) ),
		'</a>'
	);
	?>
</p>
