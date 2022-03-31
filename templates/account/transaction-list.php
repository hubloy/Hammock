<?php
/**
 * Account transaction list page
 * renders a users transactions
 * This view is used to list transactions
 *
 * This template can be overridden by copying it to yourtheme/hubloy-membership/account/transaction-list.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="hubloy-membership-account-transaction hubloy-membership-account-transaction-<?php echo esc_attr( $transaction->id ); ?>">
	<?php
		hubloy-membership_get_template(
			'account/transaction/list/view-transaction.php',
			array(
				'transaction' => $transaction,
				'member'      => $member,
			)
		);
		?>
</tr>
