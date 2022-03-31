<?php
/**
 * Auth Access
 * Handles password reset
 *
 * This template can be overridden by copying it to yourtheme/hubloy-membership/account/access/reset-form.php.
 *
 * @package HubloyMembership/Templates/Account/Access
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy-membership_before_account_reset_form' );

/**
 * Additional classes for the form
 *
 * @since 1.0.0
 *
 * @param string $extra_classes
 *
 * @return string
 */
$extra_classes = apply_filters( 'hubloy-membership_account_access_reset_form_extra_classes', 'hubloy-membership-hidden' );

?>

<div class="hubloy-membership-account-access-reset <?php echo $extra_classes; ?>">
	<h4><?php _e( 'Reset your account password', 'hubloy-membership' ); ?></h4>
	<form name="lostpasswordform" class="hubloy-membership-ajax-form" id="lostpasswordform" method="post">
		<?php wp_nonce_field( 'hubloy-membership_account_reset_nonce' ); ?>
		<input type="hidden" name="action" value="hubloy-membership_reset" />
		<?php
			do_action( 'hubloy-membership_after_account_reset_form_after_form_open' );
		?>
		<p class="reset-username">
			<label for="user_login"><?php _e( 'Username or Email Address', 'hubloy-membership' ); ?></label>
			<input type="text" name="user_login" id="user_login" class="input" size="20" autocapitalize="off" required />
		</p>
		<?php
			do_action( 'hubloy-membership_after_account_reset_form_before_submit_button' );
		?>
		<p class="submit">
			<button name="wp-submit" id="wp-submit" class="button button-primary button-large"><?php esc_attr_e( 'Get New Password', 'hubloy-membership' ); ?></button>
		</p>
		<?php
			do_action( 'hubloy-membership_after_account_reset_form_before_form_close' );
		?>
	</form>
	<p id="nav">
		<a href="#" class="hubloy-membership-link-switch" data-target=".hubloy-membership-account-access-login" data-container=".hubloy-membership-account-access"><?php esc_html_e( 'Login' ); ?></a>&nbsp;&nbsp;<a href="#" class="hubloy-membership-link-switch" data-target=".hubloy-membership-account-access-register" data-container=".hubloy-membership-account-access"><?php _e( 'Register' ); ?></a>
	</p>
</div>

<?php do_action( 'hubloy-membership_after_account_reset_form' ); ?>
