<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Model\Invoice;
use Hammock\Model\Member;
use Hammock\Model\Membership;
use Hammock\Model\Settings;
use Hammock\Helper\Currency;
use Hammock\Helper\Duration;
/**
 * Transactions service
 *
 * @since 1.0.0
 */
class Transactions {

	/**
	 * The table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Paid transaction status
	 *
	 * @since 1.0.0
	 */
	const STATUS_PAID = 'paid';

	/**
	 * Pending transaction status
	 *
	 * @since 1.0.0
	 */
	const STATUS_PENDING = 'pending';

	/**
	 * Canceled transaction status
	 *
	 * @since 1.0.0
	 */
	const STATUS_CANCELED = 'canceled';

	/**
	 * Refunded transaction status
	 *
	 * @since 1.0.0
	 */
	const STATUS_REFUNDED = 'refunded';

	/**
	 * Failed transaction status
	 *
	 * @since 1.0.0
	 */
	const STATUS_FAILED = 'failed';


	/**
	 * Main service constructor
	 *
	 * Sets up the service
	 */
	public function __construct() {
		$this->table_name = Database::get_table_name( Database::INVOICE );
	}


	/**
	 * Get Transaction Status Types
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function transaction_status() {
		$transaction_status = array(
			self::STATUS_PAID     => __( 'Paid', 'hammock' ),
			self::STATUS_PENDING  => __( 'Pending', 'hammock' ),
			self::STATUS_CANCELED => __( 'Canceled', 'hammock' ),
			self::STATUS_REFUNDED => __( 'Refunded', 'hammock' ),
			self::STATUS_FAILED   => __( 'Failed', 'hammock' ),
		);
		return apply_filters( 'hammock_transaction_status', $transaction_status );
	}

	/**
	 * Get transaction status
	 * This checks for the stransaction status key in the array
	 *
	 * @param string $status - the status key
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_transaction_status( $status ) {
		$transaction_status = self::transaction_status();
		return isset( $transaction_status[ $status ] ) ? $transaction_status[ $status ] : '';
	}

	/**
	 * Checks if a transaction is paid
	 * This checks the current transaction status with those set when paid
	 * 
	 * @param string $status - the status
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public static function is_paid( $status ) {
		return $status === self::STATUS_PAID;
	}

	/**
	 * Count transactions
	 *
	 * @param array $params - the query params are the fields in the transaction
	 *                        these can be status, member_id, amount, gateway, method
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function count_transaction( $params = array() ) {
		global $wpdb;
		$where = $this->prepare_where( $params );
		$sql   = "SELECT count(`id`) FROM {$this->table_name} $where";
		$total = $wpdb->get_var( $sql );
		return $total;
	}

	/**
	 * List transactions
	 *
	 * @param int   $per_page - items per page
	 * @param int   $page - current page
	 * @param array $params - the query params are the fields in the transaction
	 *                        these can be status, member_id, amount, gateway, method
	 * 
	 * @param bool $as_object - return array as objects
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_transactions( $per_page, $page = 0, $params = array(), $as_object = false ) {
		global $wpdb;
		$page   	  = $per_page * $page;
		$where        = $this->prepare_where( $params );
		$sql          = "SELECT `id` FROM {$this->table_name} $where ORDER BY `id` DESC LIMIT %d, %d";
		$results      = $wpdb->get_results( $wpdb->prepare( $sql, $page, $per_page ) );
		$transactions = array();
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$invoice        = new Invoice( $result->id );
				$transactions[] = $as_object ? ( object ) $invoice->to_html() : $invoice->to_html();
			}
		}
		return $transactions;
	}

	/**
	 * Get invoice
	 * 
	 * @param int|string $id - the invoice id (integer or string)
	 * 
	 * @since 1.0.0
	 * 
	 * @return object
	 */
	public static function get_invoice( $id ) {
		return new Invoice( $id );
	}

	/**
	 * Generate the where clause
	 *
	 * @param array $params - the params
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function prepare_where( $params ) {
		global $wpdb;
		$where = '';
		if ( ! empty( $params ) ) {
			if ( isset( $params['gateway'] ) && ! empty( $params['gateway'] ) ) {
				$where .= $wpdb->prepare( 'WHERE gateway = %s', $params['gateway'] );
			}
			if ( isset( $params['method'] ) && ! empty( $params['method'] ) ) {
				if ( empty( $where ) ) {
					$where .= $wpdb->prepare( 'WHERE method = %s', $params['method'] );
				} else {
					$where .= $wpdb->prepare( ' AND method = %s', $params['method'] );
				}
			}
			if ( isset( $params['status'] ) && ! empty( $params['status'] ) ) {
				if ( empty( $where ) ) {
					$where .= $wpdb->prepare( 'WHERE status = %s', $params['status'] );
				} else {
					$where .= $wpdb->prepare( ' AND status = %s', $params['status'] );
				}
			}
			if ( isset( $params['member_id'] ) && intval( $params['member_id'] ) > 0 ) {
				if ( empty( $where ) ) {
					$where .= $wpdb->prepare( 'WHERE member_id = %d', $params['member_id'] );
				} else {
					$where .= $wpdb->prepare( ' AND member_id = %d', $params['member_id'] );
				}
			}
			if ( isset( $params['amount'] ) && doubleval( $params['amount'] ) ) {
				if ( empty( $where ) ) {
					$where .= $wpdb->prepare( 'WHERE amount = %d', $params['amount'] );
				} else {
					$where .= $wpdb->prepare( ' AND amount = %d', $params['amount'] );
				}
			}
		}
		return apply_filters( 'hammock_transactions_generate_where', $where, $params );
	}

	/**
	 * Create transaction
	 * 
	 * @param int $user_id - the user id
	 * @param int $membership_id - the membership id
	 * @param string $status - the invoice status
	 * @param string $gateway - the gateway
	 * @param string $due_date - the due date
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function create_transaction( $user_id, $membership_id, $status, $gateway, $due_date ) {
		$service 	= new Members();
		$member 	= $service->get_member_by_user_id( $user_id );
		if ( $member && $member->exists() ) {
			if ( $status == self::STATUS_PAID ) {
				$args = array(
					'status' 	=> Members::STATUS_ACTIVE
				);
			} else {
				$args = array(
					'status' 	=> Members::STATUS_PENDING
				);
			}
			
			$plan = $member->add_plan( $membership_id, $args );
			if ( $plan ) {
				$membership = new Membership( $membership_id );

				$invoice_id = $this->save_transaction( $gateway, $status, $member, $plan, $membership->get_price(), $due_date );

				if ( $invoice_id ) {
					return array(
						'status' 	=> true,
						'message'	=> __( 'Invoice saved', 'hammock' ),
						'id'		=> $invoice_id
					);
				} else {
					return array(
						'status' 	=> false,
						'message'	=> __( 'Error saving invoice', 'hammock' ),
					);
				}
				
			} else {
				return array(
					'status'	=> false,
					'message'	=> __( 'Error adding plan to member', 'hammock' )
				);
			}
		} else {
			return array(
				'status'	=> false,
				'message'	=> __( 'Member does not exist', 'hammock' )
			);
		}
	}

	/**
	 * Save Transaction
	 * 
	 * @param string $gateway - the gateway
	 * @param string $status - the status
	 * @param object $member - the member object
	 * @param object $plan - the plan object
	 * @param double $amount - the amount
	 * @param string $due_date - the due date
	 * @param array $custom_data - the optional custom data
	 * 
	 * @since 1.0.0
	 * 
	 * @return int|bool
	 */
	public function save_transaction( $gateway, $status, $member, $plan, $amount, $due_date = '', $invoice_id = false, $custom_data = array() ) {
		$invoice = new Invoice();
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			$user_id = $member ? $member->user_id : 0;
		}

		$invoice->user_id		= $user_id;
		$invoice->gateway 		= $gateway;
		$invoice->status 		= $status;
		$invoice->member_id 	= $member ? $member->id : 0;
		$invoice->plan_id 		= $plan ? $plan->id : 0;
		$invoice->amount 		= Currency::format_price( $amount );
		$invoice->custom_data  	= $custom_data;
		$invoice->due_date		= $due_date;
		$invoice->invoice_id	= $invoice_id ? $invoice_id : date( 'YmdHis' );
		if ( $amount == 0 ) {
			$invoice->status = self::STATUS_PAID;
		}
		$invoice->save();

		if ( $invoice->id > 0 ) {
			/**
			 * Action called when invoice is successfully saved
			 * 
			 * @param object $invoice - the invoice
			 * @param object $member - the member
			 * @param object $plan - the plan
			 * 
			 * @since 1.0.0
			 */
			do_action( 'hammock_invoice_saved', $invoice, $member, $plan );

			/**
			 * Action called when invoice is successfully saved based on status
			 * 
			 * @param object $invoice - the invoice
			 * @param object $member - the member
			 * @param object $plan - the plan
			 * 
			 * @since 1.0.0
			 */
			do_action( 'hammock_invoice_saved_' . $status, $invoice, $member, $plan );

			return $invoice->id;
		}
		return false;
	}

	/**
	 * Update invoice by id
	 * 
	 * @param int $id - the invoice id
	 * @param string $gateway - the gateway
	 * @param string $status - the status
	 * @param double $amount - the amount
	 * @param string $due_date - the due date
	 * @param array $custom_data - the optional custom data
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function update_transaction( $id, $gateway, $status, $amount, $due_date, $custom_data = array() ) {
		$invoice = new Invoice( $id );
		if ( $invoice->id > 0 ) {
			if ( !empty( $gateway ) ) {
				$invoice->gateway 	= $gateway;
			}
			$invoice->status 		= $status;
			$invoice->amount 		= Currency::format_price( $amount );
			$invoice->due_date		= $due_date;
			$invoice->custom_data  	= array_merge( $invoice->custom_data, $custom_data );
			$invoice->save();

			/**
			 * Action called when invoice is successfully saved based on status
			 * 
			 * @param object $invoice - the invoice
			 * 
			 * @since 1.0.0
			 */
			do_action( 'hammock_invoice_updated', $invoice  );

			/**
			 * Action called when invoice is successfully saved based on status
			 * 
			 * @param object $invoice - the invoice
			 * 
			 * @since 1.0.0
			 */
			do_action( 'hammock_invoice_updated_' . $status, $invoice  );
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Get transaction stats to be used in the dashboard
	 * This returns an array of the sum transactions per day of the week
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_weekly_transaction_stats() {
		global $wpdb;
		$members	= array();
		$sql 		= "SELECT sum(`amount`) as total, WEEKDAY(`due_date`) as week_day FROM {$this->table_name} WHERE `status` = %s AND `due_date` BETWEEN (FROM_DAYS(TO_DAYS(CURDATE())-MOD(TO_DAYS(CURDATE())-1,7))) AND (FROM_DAYS(TO_DAYS(CURDATE())-MOD(TO_DAYS(CURDATE())-1,7)) + INTERVAL 7 DAY)";
		$results    = $wpdb->get_results( $wpdb->prepare( $sql, self::STATUS_PAID ) );
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$week_day = Duration::mysql_week_day_to_string( $result->week_day );
				$members[$week_day] = $result->total;
			}
		}
		return $members;
	}
}

