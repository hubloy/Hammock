<?php
/**
 * Account transaction list page
 * renders a users transactions
 * This view is used to list transactions
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/transaction-list.php.
 *
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="hammock-account-transaction hammock-account-transaction-<?php echo esc_attr( $transaction->id ); ?>">
	<?php
		hammock_get_template(
			'account/transaction/list/view-transaction.php',
			array(
				'transaction' => $transaction,
				'member'      => $member,
			)
		);
		?>
</tr>
