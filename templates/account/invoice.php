<?php
/**
 * Account invoice
 * This manages the invoice
 * Used to pay or view existing invoices
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/invoice.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $invoice ) {
	esc_html_e( 'Invalid invoice id', 'memberships-by-hubloy' );
} else {
	?>
	<p>
	<?php
		printf(
			esc_html__( '# %1$s', 'memberships-by-hubloy' ),
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
