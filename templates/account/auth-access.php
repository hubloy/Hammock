<?php
/**
 * Auth Access
 * Handles login, registration and password reset
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/auth-access.php.
 *
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hammock_before_account_access' );
?>
<div class="hammock-account-access">
	<?php
		hammock_get_template( 'account/access/login-form.php' );
		hammock_get_template( 'account/access/reset-form.php' );
		hammock_get_template( 'account/access/register-form.php' );
	?>
</div>

<?php do_action( 'hammock_after_account_access' ); ?>
