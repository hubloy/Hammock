<?php
/**
 * Invite form.
 * Shown to user before selecting a membership.
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/transaction/codes/invite-form.php.
 *
 * @package HubloyMembership/Templates/Account/Transaction/Codes/InviteForm
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="invite">
	<?php if ( ! is_user_logged_in() ) : ?>
		<div class="email">
			<p><?php esc_html_e( 'Email:', 'memberships-by-hubloy' ); ?></p>
			<p><input type="text" name="email_address" class="input-text" placeholder="<?php esc_attr_e( 'Email Address', 'memberships-by-hubloy' ); ?>" id="email_address" value="" /></p>
		</div>
	<?php endif; ?>
	<div>
		<p><?php esc_html_e( 'You need an invitation to access this plan.', 'memberships-by-hubloy' ); ?></p>
		<p><input type="text" name="invite_code" class="input-text" placeholder="<?php esc_attr_e( 'Invite code', 'memberships-by-hubloy' ); ?>" id="invite_code" value="" /></p>
	</div>
	<div>
		<button class="button" name="apply_invite" value="<?php esc_attr_e( 'Apply Code', 'memberships-by-hubloy' ); ?>"><?php esc_html_e( 'Apply Code', 'woocommerce' ); ?></button>
	</div>
</div>