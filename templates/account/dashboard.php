<?php
/**
 * Account dashboard
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/dashboard.php.
 * 
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p>
<?php
	printf(
		__( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'hammock' ),
		'<strong>' . esc_html( $current_user->name ) . '</strong>',
		esc_url( hammock_logout_url() )
	);
?>
</p>