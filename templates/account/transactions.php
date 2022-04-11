<?php
/**
 * Account transactions method page
 * Manage users transactions
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/transactions.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! hubloy_membership_current_user_can_subscribe() ) {
	esc_html_e( 'No transactions, Subscriptions are not enabled for your account.', 'memberships-by-hubloy' );
} else {
	if ( $member ) {
		$page               = hubloy_membership_get_current_page();
		$status             = hubloy_membership_get_current_status();
		$total_transactions = hubloy_membership_count_member_transactions( $member->id, $status );
		if ( $total_transactions <= 0 ) {
			?>
			<div class="hubloy_membership-notification hubloy_membership-notification--warning">
				<?php esc_html_e( 'No transactions found', 'memberships-by-hubloy' ); ?>
			</div>
			<?php
		} else {
			$transactions = hubloy_membership_list_member_transactions( $member_id, $per_page, $page, $status );
			?>
			<div class="hubloy_membership-list-header">
				<h3><?php printf( esc_html__( '%d transactions', 'memberships-by-hubloy' ), esc_attr( $total_transactions ) ); ?></h3>
			</div>
			<table class="hubloy_membership-account-transactions hubloy_membership-list-table">
				<thead>
					<tr>
						<?php
						foreach ( hubloy_membership_view_transaction_list_table_columns() as $key => $value ) {
							?>
								<th class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></th>
							<?php
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $transactions as $transaction ) {
						hubloy_membership_get_template(
							'account/transaction-list.php',
							array(
								'transaction' => $transaction,
								'member'      => $member,
							)
						);
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<?php
						foreach ( hubloy_membership_view_transaction_list_table_columns() as $key => $value ) {
							?>
								<th class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></th>
							<?php
						}
						?>
					</tr>
				</tfoot>
			</table>
			<?php
		}
	} else {
		esc_html_e( 'You have no subscriptions in your account', 'memberships-by-hubloy' );
	}
}
