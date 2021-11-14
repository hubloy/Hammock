<?php
/**
 * Auth Access
 * Handles password reset
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/access/reset-form.php.
 *
 * @package Hammock/Templates/Account/Access
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hammock_before_account_reset_form' );

/**
 * Additional classes for the form
 *
 * @since 1.0.0
 *
 * @param string $extra_classes
 *
 * @return string
 */
$extra_classes = apply_filters( 'hammock_account_access_reset_form_extra_classes', 'hammock-hidden' );

?>

<div class="hammock-account-access-reset <?php echo $extra_classes; ?>">
	<h4><?php _e( 'Reset your account password', 'hammock' ); ?></h4>
	<form name="lostpasswordform" class="hammock-ajax-form" id="lostpasswordform" method="post">
		<?php wp_nonce_field( 'hammock_account_reset_nonce' ); ?>
		<input type="hidden" name="action" value="hammock_reset" />
		<?php
			do_action( 'hammock_after_account_reset_form_after_form_open' );
		?>
		<p class="reset-username">
			<label for="user_login"><?php _e( 'Username or Email Address', 'hammock' ); ?></label>
			<input type="text" name="user_login" id="user_login" class="input" size="20" autocapitalize="off" required />
		</p>
		<?php
			do_action( 'hammock_after_account_reset_form_before_submit_button' );
		?>
		<p class="submit">
			<button name="wp-submit" id="wp-submit" class="button button-primary button-large"><?php esc_attr_e( 'Get New Password', 'hammock' ); ?></button>
		</p>
		<?php
			do_action( 'hammock_after_account_reset_form_before_form_close' );
		?>
	</form>
	<p id="nav">
		<a href="#" class="hammock-link-switch" data-target=".hammock-account-access-login" data-container=".hammock-account-access"><?php _e( 'Login' ); ?></a>&nbsp;&nbsp;<a href="#" class="hammock-link-switch" data-target=".hammock-account-access-register" data-container=".hammock-account-access"><?php _e( 'Register' ); ?></a>
	</p>
</div>

<?php do_action( 'hammock_after_account_reset_form' ); ?>
