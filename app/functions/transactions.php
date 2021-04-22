<?php
/**
 * Membership transactions
 * These functions can be used within themes or external resources
 * 
 * @package Hammock/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Count the transactions for the member
 * 
 * @param int $member_id - the member id
 * @param string $status - the transaction status. 
 * 		This defaults to all to show all transactions.
 * 		Refer to \Hammock\Services\Transactions::transaction_status() for a list of transactions
 * 
 * @since 1.0.0
 * 
 * @return int
 */
function hammock_count_member_transactions( $member_id, $status = 'all' ) {
	$transactions 	= new \Hammock\Services\Transactions();
	$status 		= strtolower( $status );
	if ( $status === 'all' ) {
		return $transactions->count_transaction( array(
			'member_id' => $member_id
		) );
	} else {
		return $transactions->count_transaction( array(
			'member_id' => $member_id,
			'status'	=> $status
		) );
	}
}

/**
 * List member transactions
 * 
 * @param int $member_id - the member id
 * @param int $per_page - items to show per page
 * @param int $page - the current page. Defaults to 0
 * @param string $status - the transaction status. 
 * 		This defaults to all to show all transactions.
 * 		Refer to \Hammock\Services\Transactions::transaction_status() for a list of transactions
 * 
 * @since 1.0.0
 * 
 * @return array
 */
function hammock_list_member_transactions( $member_id, $per_page, $page = 0, $status = 'all' ) {
	$transactions 	= new \Hammock\Services\Transactions();
	$status 		= strtolower( $status );
	if ( $status === 'all' ) {
		return $transactions->list_transactions( $per_page, $page, array(
			'member_id' => $member_id
		), true );
	} else {
		return $transactions->list_transactions( $per_page, $page, array(
			'member_id' => $member_id,
			'status'	=> $status
		), true );
	}
}
?>