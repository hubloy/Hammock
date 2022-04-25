<?php
namespace HubloyMembership\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Core\Database;
use HubloyMembership\Services\Gateways;
use HubloyMembership\Services\Transactions;
use HubloyMembership\Services\Members;
use HubloyMembership\Helper\Duration;
use HubloyMembership\Helper\Currency;

/**
 * Invoice model
 *
 * @since 1.0.0
 */
class Invoice {

	/**
	 * The database ID
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * The gateway slug
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gateway = '';

	/**
	 * The status
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $status = '';

	/**
	 * Status of overdue invoice
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $is_overdue = false;

	/**
	 * The member id
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $member_id = 0;

	/**
	 * The plan id
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $plan_id = 0;

	/**
	 * The invoice id
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $invoice_id = '';

	/**
	 * The amount
	 *
	 * @since 1.0.0
	 *
	 * @var double
	 */
	public $amount = 0.00;

	/**
	 * The tax rate
	 * The percentage tax rate
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $tax_rate = 0;

	/**
	 * The gateway identifier
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gateway_identifier = '';

	/**
	 * The notes
	 * Each note new line is saved as an array value
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $notes = array();

	/**
	 * Custom data
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $custom_data = array();

	/**
	 * The user id
	 * Allow a user to pay for another member
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * The invoice due date
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $due_date = '';

	/**
	 * Date created
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $date_created = '';

	/**
	 * Date updated
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $date_updated = '';

	/**
	 * Admin edit url
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $admin_edit_url = '';

	/**
	 * The table name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * Initialize the Model
	 *
	 * @since 1.0
	 */
	public function __construct( $id = null ) {
		$this->table_name = Database::get_table_name( Database::INVOICE );
		if ( is_numeric( $id ) && $id > 0 ) {
			$this->get_one( $id );
		} elseif ( is_string( $id ) && ! is_null( $id ) ) {
			$this->get_by_invoice_id( $id );
		}
	}

	/**
	 * Get one by id
	 * Checks the database and sets all values in the model
	 *
	 * @param string $id - the membership id
	 *
	 * @since 1.0.0
	 */
	public function get_one( $id ) {
		global $wpdb;
		$sql    = "SELECT `id`, `gateway`, `status`, `member_id`, `plan_id`, `invoice_id`, `amount`, `tax_rate`, `gateway_identifier`, `notes`, `custom_data`, `user_id`, `due_date`, `date_created`, `last_updated` FROM {$this->table_name} WHERE `id` = %d";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );
		if ( $result ) {
			$this->populate( $result );
		}
	}

	/**
	 * Get by invoice id
	 *
	 * @param string $invoice_id
	 *
	 * @since 1.0.0
	 */
	public function get_by_invoice_id( $invoice_id ) {
		global $wpdb;
		$sql    = "SELECT `gateway`, `status`, `member_id`, `plan_id`, `id`, `invoice_id`, `amount`, `tax_rate`, `gateway_identifier`, `notes`, `custom_data`, `user_id`, `due_date`, `date_created`, `last_updated` FROM {$this->table_name} WHERE `invoice_id` = %s";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $invoice_id ) );
		if ( $result ) {
			$this->populate( $result );
		}
	}

	/**
	 * Get by gateway identifier
	 *
	 * @param string $gateway_identifier
	 *
	 * @since 1.0.0
	 */
	public function get_by_gateway_identifier( $gateway_identifier ) {
		global $wpdb;
		$sql    = "SELECT `gateway`, `status`, `member_id`, `plan_id`, `id`, `invoice_id`, `amount`, `tax_rate`, `gateway_identifier`, `notes`, `custom_data`, `user_id`, `due_date`, `date_created`, `last_updated` FROM {$this->table_name} WHERE `gateway_identifier` = %s";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $gateway_identifier ) );
		if ( $result ) {
			$this->populate( $result );
		}
	}


	/**
	 * Populate the invoice variables
	 *
	 * @param object $result - the database result object
	 *
	 * @since 1.0.0
	 */
	private function populate( $result ) {
		$date_format              = get_option( 'date_format' );
		$this->id                 = $result->id;
		$this->gateway            = $result->gateway;
		$this->status             = $result->status;
		$this->member_id          = $result->member_id;
		$this->plan_id            = $result->plan_id;
		$this->invoice_id         = $result->invoice_id;
		$this->amount             = $result->amount;
		$this->tax_rate           = $result->tax_rate;
		$this->gateway_identifier = $result->gateway_identifier;
		$this->notes              = is_array( $result->notes ) ? array_map( 'maybe_unserialize', $result->notes ) : maybe_unserialize( $result->notes );

		$this->custom_data    = is_array( $result->custom_data ) ? array_map( 'maybe_unserialize', $result->custom_data ) : maybe_unserialize( $result->custom_data );
		$this->user_id        = $result->user_id;
		$this->due_date       = ! empty( $result->due_date ) ? date_i18n( $date_format, strtotime( $result->due_date ) ) : '';
		$this->date_created   = date_i18n( $date_format, strtotime( $result->date_created ) );
		$this->date_updated   = ! empty( $result->last_updated ) ? date_i18n( $date_format, strtotime( $result->last_updated ) ) : '';
		$this->admin_edit_url = $this->admin_edit_url();
		$this->is_overdue     = $this->is_overdue();
	}

	/**
	 * Save invoice
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool
	 */
	public function save() {
		global $wpdb;

		$value = wp_unslash( $this->custom_data );
		$value = maybe_serialize( $value );

		$notes = wp_unslash( $this->notes );
		$notes = maybe_serialize( $notes );

		if ( $this->id > 0 ) {
			$wpdb->update(
				$this->table_name,
				array(
					'gateway'            => $this->gateway,
					'status'             => $this->status,
					'member_id'          => $this->member_id,
					'plan_id'            => $this->plan_id,
					'amount'             => $this->amount,
					'tax_rate'           => $this->tax_rate,
					'gateway_identifier' => $this->gateway_identifier,
					'notes'              => $notes,
					'custom_data'        => $value,
					'user_id'            => $this->user_id,
					'due_date'           => ! empty( $this->due_date ) ? date_i18n( 'Y-m-d H:i:s', strtotime( $this->due_date ) ) : '',
					'last_updated'       => date_i18n( 'Y-m-d H:i:s' ),
				),
				array( 'id' => $this->id )
			);

			/**
			 * Action called after an invoice is updated
			 *
			 * @param object $invoice - the invoice object
			 *
			 * @since 1.0.0
			 */
			do_action( 'hubloy_membership_after_invoice_update', $this );

		} else {
			$result = $wpdb->insert(
				$this->table_name,
				array(
					'gateway'            => $this->gateway,
					'status'             => $this->status,
					'member_id'          => $this->member_id,
					'plan_id'            => $this->plan_id,
					'invoice_id'         => preg_replace( '/\s+/', '', $this->invoice_id ),
					'amount'             => $this->amount,
					'tax_rate'           => $this->tax_rate,
					'gateway_identifier' => $this->gateway_identifier,
					'notes'              => $notes,
					'custom_data'        => $value,
					'user_id'            => $this->user_id,
					'due_date'           => ! empty( $this->due_date ) ? date_i18n( 'Y-m-d H:i:s', strtotime( $this->due_date ) ) : '',
					'date_created'       => date_i18n( 'Y-m-d H:i:s' ),
				)
			);

			if ( ! $result ) {
				return false;
			} else {
				$this->id = (int) $wpdb->insert_id;
				$this->after_save();

				/**
				 * Action called after an invoice is saved
				 *
				 * @param object $invoice - the invoice object
				 *
				 * @since 1.0.0
				 */
				do_action( 'hubloy_membership_after_invoice_save', $this );
			}
		}
		return $this->id;
	}

	/**
	 * After save update the invoice id
	 *
	 * @since 1.0.0
	 */
	private function after_save() {
		global $wpdb;
		$settings         = new Settings();
		$prefix           = $settings->get_general_setting( 'prefix' );
		$invoice_id       = \HubloyMembership\Helper\Invoice::generate_invoice_number( $this->id );
		$invoice_id       = $prefix . $invoice_id;
		$invoice_id       = preg_replace( '/\s+/', '', $invoice_id ); // trim white spaces
		$this->invoice_id = $invoice_id;
		$wpdb->update(
			$this->table_name,
			array(
				'invoice_id' => $invoice_id,
			),
			array( 'id' => $this->id )
		);
	}

	/**
	 * If invoice is valid
	 * This checks if the id is greater than 0
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() {
		return $this->id > 0;
	}

	/**
	 * Delete invoice
	 *
	 * @since 1.0.0
	 */
	public function delete() {
		global $wpdb;
		$sql = "DELETE FROM {$this->table_name} WHERE `id` = %d";
		$wpdb->query( $wpdb->prepare( $sql, $this->id ) );
	}

	/**
	 * Apply a discount.
	 * 
	 * @param int $amount The total discount amount.
	 * @param bool $save  Set to false to not save. Defaults to true.
	 * 
	 * @since 1.1.0
	 */
	public function apply_discount( $amount, $save = true ) {
		$this->custom_data[ 'discount' ] = $amount;
		$invoice->add_note( sprintf( __( 'Discount of %d applied', 'memberships-by-hubloy' ), $amount ) );
		if ( $save ) {
			$this->save();
		}
	}

	/**
	 * Get the gateway name
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function gateway_name() {
		if ( empty( $this->gateway ) ) {
			return apply_filters( 'hubloy_membership_invoice_gateway_blank', __( 'None', 'memberships-by-hubloy' ), $this );
		} else {
			$gateways = Gateways::load_gateways();
			return isset( $gateways[ $this->gateway ] ) ? $gateways[ $this->gateway ]['name'] : apply_filters( 'hubloy_membership_invoice_gateway_missing', $this->gateway, $this );
		}
	}

	/**
	 * Admin edit url
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function admin_edit_url() {
		return admin_url( 'admin.php?page=hubloy_membership-transactions#/transaction/' . $this->id );
	}

	/**
	 * Check if an invoice is paid
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_paid() {
		$is_paid = Transactions::is_paid( $this->status );
		return apply_filters( 'hubloy_membership_invoice_is_paid', $is_paid, $this );
	}

	/**
	 * Check if invoice is overdue
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_overdue() {
		$is_paid = $this->is_paid();
		if ( ! empty( $this->due_date ) && ! $is_paid ) {
			if ( Transactions::STATUS_CANCELED !== $this->status ) {
				$is_past = Duration::is_past_date( $this->due_date );
				return $is_past;
			}
		}
		return false;
	}

	/**
	 * Get plan
	 *
	 * @since 1.0.0
	 *
	 * @return Plan
	 */
	public function get_plan() {
		return new Plan( $this->plan_id );
	}

	/**
	 * Get user details
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_user_details() {
		return Members::user_details( $this->user_id );
	}

	/**
	 * Check if the current user is the invoice owner
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_owner() {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$current_user_id = get_current_user_id();
		$is_owner        = ( $this->user_id === $current_user_id );
		return apply_filters( 'hubloy_membership_current_user_is_invoice_owner', $is_owner, $this->id );
	}

	/**
	 * Get hte status name
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_status_name() {
		return Transactions::get_transaction_status( $this->status );
	}

	/**
	 * Get the invoice amount
	 * 
	 * @since 1.1.0
	 * 
	 * @return int
	 */
	public function get_amount() {
		$discount = $this->get_total_discount();
		$total    = $this->amount - $discount;
		$total    = Currency::round( $total, HUBMEMB_ROUNDING_PRECISION );
		return apply_filters( 'hubloy_membership_invoice_amount', $total, $this->id );
	}

	/**
	 * Get the total discount
	 * 
	 * @since 1.1.0
	 * 
	 * @return int
	 */
	public function get_total_discount() {
		$discount = $this->get_custom_data( 'discount' );
		if ( $discount ) {
			return $discount;
		}
		return 0;
	}

	/**
	 * Get the amount formatted
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_amount_formated() {
		return hubloy_membership_format_currency( $this->get_amount() );
	}

	/**
	 * Get member
	 * 
	 * @since 1.1.0
	 * 
	 * @return object
	 */
	public function get_member() {
		return new Member( $this->member_id );
	}

	/**
	 * Get Custom data value
	 *
	 * @param string $meta_key - the meta key
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_custom_data( $meta_key ) {
		if ( isset( $this->custom_data[ $meta_key ] ) ) {
			return $this->custom_data[ $meta_key ];
		}
		return false;
	}


	/**
	 * Set custom data
	 *
	 * @param String $key -  the setting key
	 * @param mixed  $value - the value
	 *
	 * @since 1.0.0
	 */
	public function set_custom_data( $key, $value ) {
		$this->custom_data[ $key ] = $value;
	}

	/**
	 * Add a note to the current notes
	 *
	 * @param string $note - the note
	 *
	 * @since 1.0.0
	 */
	public function add_note( $note ) {
		$this->notes[] = is_string( $note ) ? $note : var_export( $note, true );
	}

	/**
	 * Checks if the invoice has error
	 * This checks the meta table if the invoice has an error
	 * If there is no error, false will be returned
	 * If an error is present, the error key and message will be returned
	 *
	 * @since 1.0.0
	 *
	 * @return bool|array
	 */
	public function has_error() {
		$error = $this->get_custom_data( 'error' );
		return $error;
	}

	/**
	 * Return html represenation of invoice
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function to_html() {
		return apply_filters(
			'hubloy_membership_invoice_to_html',
			array(
				'id'              => $this->id,
				'gateway'         => $this->gateway,
				'gateway_name'    => $this->gateway_name(),
				'status'          => $this->status,
				'status_name'     => $this->get_status_name(),
				'is_overdue'      => $this->is_overdue(),
				'member_id'       => $this->member_id,
				'member_user'     => $this->get_member(),
				'plan_id'         => $this->plan_id,
				'plan'            => $this->get_plan(),
				'invoice_id'      => $this->invoice_id,
				'amount'          => $this->get_amount(),
				'amount_formated' => $this->get_amount_formated(),
				'custom_data'     => $this->custom_data,
				'user_id'         => $this->user_id,
				'user_data'       => $this->get_user_details(),
				'due'             => ! empty( $this->due_date ) ? date_i18n( 'Y-m-d', strtotime( $this->due_date ) ) : '',
				'due_date'        => ! empty( $this->due_date ) ? $this->due_date : __( 'N/A', 'memberships-by-hubloy' ),
				'date_created'    => $this->date_created,
				'date_updated'    => $this->date_updated,
				'admin_edit_url'  => $this->admin_edit_url,
			),
			$this
		);
	}
}

