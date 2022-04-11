<?php
/**
 * Account transaction list page
 * renders a users transactions
 * This view is used to list transactions
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/transaction-list.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="hubloy_membership-account-transaction hubloy_membership-account-transaction-<?php echo esc_attr( $transaction->id ); ?>">
	<?php
		hubloy_membership_get_template(
			'account/transaction/list/view-transaction.php',
			array(
				'transaction' => $transaction,
				'member'      => $member,
			)
		);
		?>
</tr>
