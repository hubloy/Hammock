<?php
/**
 * Account transactions method page
 * Manage users transactions
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/transactions.php.
 *
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! hammock_current_user_can_subscribe() ) {
	esc_html_e( 'No transactions, Subscriptions are not enabled for your account.', 'hammock' );
} else {
	if ( $member ) {
		$page               = hammock_get_current_page();
		$status             = hammock_get_current_status();
		$total_transactions = hammock_count_member_transactions( $member->id, $status );
		if ( $total_transactions <= 0 ) {
			?>
			<div class="hammock-notification hammock-notification--warning">
				<?php esc_html_e( 'No transactions found', 'hammock' ); ?>
			</div>
			<?php
		} else {
			$transactions = hammock_list_member_transactions( $member_id, $per_page, $page, $status );
			?>
			<div class="hammock-list-header">
				<h3><?php printf( esc_html__( '%d transactions', 'hammock' ), esc_attr( $total_transactions ) ); ?></h3>
			</div>
			<table class="hammock-account-transactions hammock-list-table">
				<thead>
					<tr>
						<?php
						foreach ( hammock_view_transaction_list_table_columns() as $key => $value ) {
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
						hammock_get_template(
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
						foreach ( hammock_view_transaction_list_table_columns() as $key => $value ) {
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
		esc_html_e( 'You have no subscriptions in your account', 'hammock' );
	}
}
