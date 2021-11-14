<?php
/**
 * Auth Access
 * Handles account login
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/access/login-form.php.
 *
 * @package Hammock/Templates/Account/Access
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hammock_before_account_login_form' );


/**
 * Additional classes
 *
 * @since 1.0.0
 *
 * @param string $extra_classes
 *
 * @return string
 */
$extra_classes = apply_filters( 'hammock_account_access_login_form_extra_classes', '' );
?>

<div class="hammock-account-access-login <?php echo $extra_classes; ?>">
	<h4><?php _e( 'Login to your account', 'hammock' ); ?></h4>
	<form name="loginform" class="hammock-ajax-form" id="loginform">
		<?php wp_nonce_field( 'hammock_account_login_nonce' ); ?>
		<input type="hidden" name="action" value="hammock_login" />
		<?php
			do_action( 'hammock_after_account_login_form_after_form_open' );
		?>
		<p class="login-username">
			<label for="user_login"><?php _e( 'Username or Email Address', 'hammock' ); ?></label>
			<input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" required />
		</p>
		<p class="login-password">
			<label for="user_pass"><?php _e( 'Password', 'hammock' ); ?></label>
			<input type="password" name="user_pass" id="user_pass" class="input password-input" value="" size="20" required />
		</p>
		<p class="forgetmenot">
			<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <label for="rememberme"><?php esc_html_e( 'Remember Me', 'hammock' ); ?></label>
		</p>
		<?php
			do_action( 'hammock_after_account_login_form_before_submit_button' );
		?>
		<p class="submit">
			<button name="wp-submit" id="wp-submit" class="button button-primary button-large"><?php esc_attr_e( 'Log In', 'hammock' ); ?></button>
		</p>
		<?php
			do_action( 'hammock_after_account_login_form_before_form_close' );
		?>
	</form>
	<p id="nav">
		<a href="#" class="hammock-link-switch" data-target=".hammock-account-access-reset" data-container=".hammock-account-access"><?php _e( 'Lost your password?' ); ?></a>&nbsp;&nbsp;<a href="#" class="hammock-link-switch" data-target=".hammock-account-access-register" data-container=".hammock-account-access"><?php _e( 'Register' ); ?></a>
	</p>
</div>

<?php do_action( 'hammock_after_account_login_form' ); ?>
