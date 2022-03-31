<?php
/**
 * Auth Access
 * Handles account login
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/access/login-form.php.
 *
 * @package HubloyMembership/Templates/Account/Access
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_before_account_login_form' );


/**
 * Additional classes
 *
 * @since 1.0.0
 *
 * @param string $extra_classes
 *
 * @return string
 */
$extra_classes = apply_filters( 'hubloy_membership_account_access_login_form_extra_classes', 'login' );
?>

<div class="hubloy_membership-account-access-login <?php echo esc_attr( $extra_classes ); ?>">
	<h4><?php _e( 'Login to your account', 'hubloy_membership' ); ?></h4>
	<form name="loginform" class="hubloy_membership-ajax-form" id="loginform">
		<?php wp_nonce_field( 'hubloy_membership_account_login_nonce' ); ?>
		<input type="hidden" name="action" value="hubloy_membership_login" />
		<?php
			do_action( 'hubloy_membership_after_account_login_form_after_form_open' );
		?>
		<p class="login-username">
			<label for="user_login"><?php _e( 'Username or Email Address', 'hubloy_membership' ); ?></label>
			<input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" required />
		</p>
		<p class="login-password">
			<label for="user_pass"><?php _e( 'Password', 'hubloy_membership' ); ?></label>
			<input type="password" name="user_pass" id="user_pass" class="input password-input" value="" size="20" required />
		</p>
		<p class="forgetmenot">
			<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <label for="rememberme"><?php esc_html_e( 'Remember Me', 'hubloy_membership' ); ?></label>
		</p>
		<?php
			do_action( 'hubloy_membership_after_account_login_form_before_submit_button' );
		?>
		<p class="submit">
			<button name="wp-submit" id="wp-submit" class="button button-primary button-large"><?php esc_attr_e( 'Log In', 'hubloy_membership' ); ?></button>
		</p>
		<?php
			do_action( 'hubloy_membership_after_account_login_form_before_form_close' );
		?>
	</form>
	<p id="nav">
		<a href="#" class="hubloy_membership-link-switch" data-target=".hubloy_membership-account-access-reset" data-container=".hubloy_membership-account-access"><?php _e( 'Lost your password?' ); ?></a>&nbsp;&nbsp;<a href="#" class="hubloy_membership-link-switch" data-target=".hubloy_membership-account-access-register" data-container=".hubloy_membership-account-access"><?php esc_html_e( 'Register', 'hubloy_membership' ); ?></a>
	</p>
</div>

<?php do_action( 'hubloy_membership_after_account_login_form' ); ?>
