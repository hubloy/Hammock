<?php
/**
 * Account invoice
 * This manages the invoice
 * Used to pay or view existing invoices
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/invoice.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $invoice ) {
	esc_html_e( 'Invalid invoice id', 'hubloy_membership' );
} else {
	?>
	<p>
	<?php
		printf(
			esc_html__( '# %1$s', 'hubloy_membership' ),
			'<strong>' . esc_html( $invoice->invoice_id ) . '</strong>'
		);
	?>
	</p>
	<?php
	if ( ! $invoice->is_owner() || $invoice->is_paid() ) {
		hubloy_membership_get_template(
			'account/transaction/single/view-transaction.php',
			array(
				'invoice' => $invoice,
				'member'  => $member,
			)
		);
	} else {
		hubloy_membership_get_template(
			'account/transaction/single/pay-transaction.php',
			array(
				'invoice' => $invoice,
				'member'  => $member,
			)
		);
	}
}
