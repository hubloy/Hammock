<?php
/**
 * Auth Access
 * Handles login, registration and password reset
 *
 * This template can be overridden by copying it to yourtheme/hubloy-membership/account/auth-access.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy-membership_before_account_access' );
?>
<div class="hubloy-membership-account-access">
	<?php
		hubloy-membership_get_template( 'account/access/login-form.php' );
		hubloy-membership_get_template( 'account/access/reset-form.php' );
		hubloy-membership_get_template( 'account/access/register-form.php' );
	?>
</div>

<?php do_action( 'hubloy-membership_after_account_access' ); ?>
