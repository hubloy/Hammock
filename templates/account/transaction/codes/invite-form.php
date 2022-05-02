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
	<div>
		<p><?php esc_html_e( 'You need an invitation to access this plan.', 'memberships-by-hubloy' ); ?></p>
		<p><input type="text" name="invite_code" class="input-text" placeholder="<?php esc_attr_e( 'Invite code', 'memberships-by-hubloy' ); ?>" id="invite_code" value="" /></p>
	</div>
	<div>
		<a class="button" name="apply_invite" data-invoice="<?php echo esc_attr( $invoice->id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'hubloy_membership_verify_code' ) ); ?>"><?php esc_html_e( 'Apply Code', 'woocommerce' ); ?></a>
	</div>
</div>