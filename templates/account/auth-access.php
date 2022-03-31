<?php
/**
 * Auth Access
 * Handles login, registration and password reset
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/auth-access.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_before_account_access' );
?>
<div class="hubloy_membership-account-access">
	<?php
		hubloy_membership_get_template( 'account/access/login-form.php' );
		hubloy_membership_get_template( 'account/access/reset-form.php' );
		hubloy_membership_get_template( 'account/access/register-form.php' );
	?>
</div>

<?php do_action( 'hubloy_membership_after_account_access' ); ?>
