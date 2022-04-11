<?php
/**
 * Auth Access
 * Handles password reset
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/access/reset-form.php.
 *
 * @package HubloyMembership/Templates/Account/Access
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_before_account_reset_form' );

/**
 * Additional classes for the form
 *
 * @since 1.0.0
 *
 * @param string $extra_classes
 *
 * @return string
 */
$extra_classes = apply_filters( 'hubloy_membership_account_access_reset_form_extra_classes', 'hubloy_membership-hidden' );

?>

<div class="hubloy_membership-account-access-reset <?php echo $extra_classes; ?>">
	<h4><?php _e( 'Reset your account password', 'memberships-by-hubloy' ); ?></h4>
	<form name="lostpasswordform" class="hubloy_membership-ajax-form" id="lostpasswordform" method="post">
		<?php wp_nonce_field( 'hubloy_membership_account_reset_nonce' ); ?>
		<input type="hidden" name="action" value="hubloy_membership_reset" />
		<?php
			do_action( 'hubloy_membership_after_account_reset_form_after_form_open' );
		?>
		<p class="reset-username">
			<label for="user_login"><?php _e( 'Username or Email Address', 'memberships-by-hubloy' ); ?></label>
			<input type="text" name="user_login" id="user_login" class="input" size="20" autocapitalize="off" required />
		</p>
		<?php
			do_action( 'hubloy_membership_after_account_reset_form_before_submit_button' );
		?>
		<p class="submit">
			<button name="wp-submit" id="wp-submit" class="button button-primary button-large"><?php esc_attr_e( 'Get New Password', 'memberships-by-hubloy' ); ?></button>
		</p>
		<?php
			do_action( 'hubloy_membership_after_account_reset_form_before_form_close' );
		?>
	</form>
	<p id="nav">
		<a href="#" class="hubloy_membership-link-switch" data-target=".hubloy_membership-account-access-login" data-container=".hubloy_membership-account-access"><?php esc_html_e( 'Login' ); ?></a>&nbsp;&nbsp;<a href="#" class="hubloy_membership-link-switch" data-target=".hubloy_membership-account-access-register" data-container=".hubloy_membership-account-access"><?php _e( 'Register' ); ?></a>
	</p>
</div>

<?php do_action( 'hubloy_membership_after_account_reset_form' ); ?>
