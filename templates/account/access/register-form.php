<?php
/**
 * Auth Access
 * Handles account creation
 *
 * This template can be overridden by copying it to yourtheme/hubloy-membership/account/access/register-form.php.
 *
 * @package HubloyMembership/Templates/Account/Access
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy-membership_before_account_register_form' );

/**
 * Additional classes for the form
 *
 * @since 1.0.0
 *
 * @var string $extra_classes
 *
 * @return string
 */
$extra_classes = apply_filters( 'hubloy-membership_account_access_register_form_extra_classes', 'hubloy-membership-hidden' );
?>

<div class="hubloy-membership-account-access-register <?php echo $extra_classes; ?>">
	<h4><?php _e( 'Register for an account', 'hubloy-membership' ); ?></h4>
	<form name="registerform" class="hubloy-membership-ajax-form" id="registerform" method="post">
		<?php wp_nonce_field( 'hubloy-membership_account_register_nonce' ); ?>
		<input type="hidden" name="action" value="hubloy-membership_register" />
		<?php
			do_action( 'hubloy-membership_after_account_register_form_after_form_open' );
		?>
		<p class="register-username">
			<label for="user_login"><?php _e( 'Username', 'hubloy-membership' ); ?></label>
			<input type="text" name="user_login" id="user_login" class="input" size="20" autocapitalize="off" required/>
		</p>
		<p class="register-email">
			<label for="user_email"><?php _e( 'Email Address', 'hubloy-membership' ); ?></label>
			<input type="email" name="user_email" id="user_email" class="input" autocapitalize="off" required/>
		</p>
		<p class="register-password">
			<label for="user_password"><?php _e( 'Password', 'hubloy-membership' ); ?></label>
			<input type="password" name="user_password" id="user_password" class="input" autocapitalize="off" required/>
		</p>
		<?php
			do_action( 'hubloy-membership_after_account_register_form_before_submit_button' );
		?>
		<p class="submit">
			<button name="wp-submit" id="wp-submit" class="button button-primary button-large"><?php esc_attr_e( 'Register', 'hubloy-membership' ); ?></button>
		</p>
		<?php
			do_action( 'hubloy-membership_after_account_register_form_before_form_close' );
		?>
	</form>
	<p id="nav">
		<a href="#" class="hubloy-membership-link-switch" data-target=".hubloy-membership-account-access-login" data-container=".hubloy-membership-account-access"><?php esc_html_e( 'Login' ); ?></a>
	</p>
</div>

<?php do_action( 'hubloy-membership_after_account_register_form' ); ?>
