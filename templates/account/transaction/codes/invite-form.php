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
<tr class="invite">
	<?php if ( ! is_user_logged_in() ) : ?>
		<tr class="email">
			<td><?php esc_html_e( 'Email:', 'memberships-by-hubloy' ); ?></td>
			<td><input type="text" name="email_address" class="input-text" placeholder="<?php esc_attr_e( 'Email Address', 'memberships-by-hubloy' ); ?>" id="email_address" value="" /></td>
		</tr>
	<?php endif; ?>
	<td>
		<p><?php esc_html_e( 'You need an invitation to access this plan.', 'memberships-by-hubloy' ); ?></p>
		<input type="text" name="invite_code" class="input-text" placeholder="<?php esc_attr_e( 'Invite code', 'memberships-by-hubloy' ); ?>" id="invite_code" value="" />
	</td>
	<td>
		<button class="button" name="apply_invite" value="<?php esc_attr_e( 'Apply Code', 'memberships-by-hubloy' ); ?>"><?php esc_html_e( 'Apply Code', 'woocommerce' ); ?></button>
	</td>
</tr>