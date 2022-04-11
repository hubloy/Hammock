<?php
/**
 * Account transaction pay
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/transaction/single/pay-transaction.php.
 *
 * @package HubloyMembership/Templates/Account/Transaction/Single/Pay
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hubloy_membership-account-transaction--pay-transaction hubloy_membership-account-transaction-<?php echo esc_attr( $invoice->id ); ?>">
	<form method="POST" class="hubloy_membership-checkout-form">
		<?php wp_nonce_field( 'hubloy_membership_purchase_subscription' ); ?>
		<input type="hidden" name="action" value="hubloy_membership_purchase_subscription" />
		<input type="hidden" name="invoice" value="<?php echo esc_attr( $invoice->id ); ?>" />
		<?php do_action( 'hubloy_membership_account_pay_single_transaction_before', $invoice ); ?>
		<table class="hubloy_membership-account-transaction--pay-transaction-details">
			<tr class="details">
				<td><?php esc_html_e( 'Details:', 'memberships-by-hubloy' ); ?></td>
				<td>
					<?php
						hubloy_membership_get_template(
							'account/plan/single/plan-price.php',
							array(
								'plan' => $invoice->get_plan(),
							)
						);
					?>
				</td>
			</tr>
			<tr class="total">
				<td><?php esc_html_e( 'Total:', 'memberships-by-hubloy' ); ?></td>
				<td><?php echo wp_kses_post( $invoice->get_amount_formated() ); ?></td>
			</tr>
			<?php if ( $invoice->is_owner() ) : ?>
			<tr class="gateway">
				<td><?php esc_html_e( 'Payment gateway:', 'memberships-by-hubloy' ); ?></td>
				<td>
					<?php
						foreach( hubloy_membership_list_active_gateways() as $gateway_id => $gateway_name ) {
							hubloy_membership_get_template(
								'account/transaction/single/payment-method.php',
								array(
									'invoice'      => $invoice,
									'gateway_id'   => $gateway_id,
									'gateway_name' => $gateway_name,
								)
							);
						}
					?>
				</td>
			</tr>
			<?php endif; ?>
		</table>
		<?php do_action( 'hubloy_membership_account_pay_single_transaction_after', $invoice ); ?>
	</form>
</div>

