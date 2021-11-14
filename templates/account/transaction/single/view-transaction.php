<?php
/**
 * Account transaction view
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/transaction/single/pay-transaction.php.
 *
 * @package Hammock/Templates/Account/Transaction/Single/View
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hammock-account-transaction--view-transaction hammock-account-transaction-<?php echo $invoice->id; ?>">
	<?php do_action( 'hammock_account_transaction_before_table', $invoice ); ?>
	
	<?php do_action( 'hammock_account_transaction_after_table', $invoice ); ?>
</div>
