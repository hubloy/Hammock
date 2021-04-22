<?php
/**
 * Account invoice
 * This manages the invoice
 * Used to pay or view existing invoices 
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/invoice.php.
 * 
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !$invoice ) {
	_e( 'Invalid invoice id', 'hammock' );
} else {
	?>
	<p>
	<?php
		printf(
			__( '# %1$s', 'hammock' ),
			'<strong>' . esc_html( $invoice->invoice_id ) . '</strong>'
		);
	?>
	</p>
	<?php
	if ( $invoice->is_paid() ) {
		hammock_get_template( 'account/transaction/single/view-transaction.php', array(
			'invoice'	=> $invoice,
			'member' 	=> $member
		) );
	} else {
		hammock_get_template( 'account/transaction/single/pay-transaction.php', array(
			'invoice'	=> $invoice,
			'member' 	=> $member
		) );
	}
}