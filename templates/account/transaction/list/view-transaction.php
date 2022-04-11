<?php
/**
 * Account plan roe
 * This view is used as a row for transactions
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/transaction/list/view-transaction.php.
 *
 * @package HubloyMembership/Templates/Account/Transaction/List/View
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<td class="hubloy_membership-account-transaction--id">
	<a href="<?php echo esc_url( hubloy_membership_get_account_page_links( 'view-transaction', $transaction->invoice_id ) ); ?>"><?php echo esc_html( $transaction->invoice_id ); ?></a>
</td>
<td class="hubloy_membership-account-transaction--status"><?php echo esc_html( $transaction->status_name ); ?></td>
<td class="hubloy_membership-account-transaction--gateway"><?php echo esc_html( $transaction->gateway_name ); ?></td>
<td class="hubloy_membership-account-transaction--amount"><?php echo esc_html( $transaction->amount_formated ); ?></td>
<td class="hubloy_membership-account-transaction--date"><?php echo esc_html( $transaction->date_created ); ?></td>
