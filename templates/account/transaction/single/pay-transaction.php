<?php
/**
 * Account transaction pay
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/transaction/single/pay-transaction.php.
 *
 * @package Hammock/Templates/Account/Transaction/Single/Pay
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hammock-account-transaction--pay-transaction hammock-account-transaction-<?php echo esc_attr( $invoice->id ); ?>">
	<form>
		<?php do_action( 'hammock_account_pay_single_transaction_before', $invoice ); ?>
		<table class="hammock-account-transaction--pay-transaction-details">
			<tr class="status">
				<td><?php esc_html_e( 'Invoice status:', 'hammock' ); ?></td>
				<td><?php echo esc_html( $invoice->get_status_name() ); ?></td>
			</tr>
			<tr class="total">
				<td><?php esc_html_e( 'Total:', 'hammock' ); ?></td>
				<td><?php echo wp_kses_post( $invoice->get_amount_formated() ); ?></td>
			</tr>
			<?php if ( $invoice->gateway ) : ?>
			<tr class="method">
				<td><?php esc_html_e( 'Payment method:', 'hammock' ); ?></td>
				<td><?php echo wp_kses_post( $invoice->gateway_name() ); ?></td>
			</tr>
			<?php endif; ?>
			<?php if ( $invoice->is_owner() ) : ?>
			<tr class="gateway">
				<td><?php esc_html_e( 'Payment gateway:', 'hammock' ); ?></td>
				<td>
					<?php
						foreach( hammock_list_active_gateways() as $gateway_id => $gateway_name ) {
							hammock_get_template(
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
		<?php do_action( 'hammock_account_pay_single_transaction_after', $invoice ); ?>
	</form>
</div>

