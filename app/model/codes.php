<?php
namespace HubloyMembership\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Core\Database;
use HubloyMembership\Services\Members;
use \HubloyMembership\Helper\Currency;

/**
 * Codes model
 * Main codes model. All custom codes extend this
 */
class Codes {

	/**
	 * The database ID
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * The code
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $code = '';

	/**
	 * The code status
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $status = 'disabled';

	/**
	 * The code amount
	 *
	 * @since 1.0.0
	 *
	 * @var double
	 */
	public $amount = 0.00;

	/**
	 * The amount type
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $amount_type = 'number';

	/**
	 * The custom data
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $custom_data = array();

	/**
	 * The author id
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $author_id = 0;

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
	 * The code type
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $code_type = '';

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
		$this->table_name = Database::get_table_name( Database::CODES );
		$this->init();
		$this->get_code( $id );
	}

	/**
	 * Initialize model
	 *
	 * @since 1.0.0
	 */
	protected function init() {

	}

	/**
	 * If membership is valid
	 * This checks if the id is greater than 0
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_valid() {
		return $this->id > 0;
	}


	/**
	 * Get one code
	 *
	 * @param int|string $id - the code id or the code
	 *
	 * @since 1.0.0
	 */
	public function get_code( $id = null ) {
		if ( is_numeric( $id ) && $id > 0 ) {
			$this->get_one( $id );
		} elseif ( is_string( $id ) && ! is_null( $id ) ) {
			$this->get_by_code( $id );
		}
	}


	/**
	 * Get one by id
	 * Checks the database and sets all values in the model
	 *
	 * @param string $id - the code id
	 *
	 * @since 1.0.0
	 */
	protected function get_one( $id ) {
		global $wpdb;
		$sql    = "SELECT `id`, `code`, `status`, `amount`, `amount_type`, `custom_data`, `author_id`, `date_created`, `last_updated` FROM {$this->table_name} WHERE `id` = %d AND `code_type` = %s";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $id, $this->code_type ) );
		if ( $result ) {
			$this->populate( $result );
		}
	}

	/**
	 * Get one by id
	 * Checks the database and sets all values in the model
	 *
	 * @param string $code - the code
	 *
	 * @since 1.0.0
	 */
	protected function get_by_code( $code ) {
		global $wpdb;
		$sql    = "SELECT `id`, `code`, `status`, `amount`, `amount_type`, `custom_data`, `author_id`, `date_created`, `last_updated` FROM {$this->table_name} WHERE `code` = %s AND `code_type` = %s";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $code, $this->code_type ) );
		if ( $result ) {
			$this->populate( $result );
		}
	}

	/**
	 * Check if code exists
	 *
	 * @param string $code - the code
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function code_exists( $code ) {
		global $wpdb;
		$sql    = "SELECT `id` FROM {$this->table_name} WHERE `code` = %s AND `code_type` = %s";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, $code, $this->code_type ) );
		if ( $result ) {
			return true;
		}
		return false;
	}

	/**
	 * Populate model fields
	 *
	 * @param object $result - the query result
	 *
	 * @since 1.0.0
	 */
	protected function populate( $result ) {
		$date_format        = get_option( 'date_format' );
		$this->id           = $result->id;
		$this->code         = $result->code;
		$this->status       = $result->status;
		$this->amount       = $result->amount;
		$this->amount_type  = $result->amount_type;
		$this->custom_data  = is_array( $result->custom_data ) ? array_map( 'maybe_unserialize', $result->custom_data ) : maybe_unserialize( $result->custom_data );
		$this->author_id    = $result->author_id;
		$this->date_created = date_i18n( $date_format, strtotime( $result->date_created ) );
		$this->date_updated = ! empty( $result->last_updated ) ? date_i18n( $date_format, strtotime( $result->last_updated ) ) : '';
	}

	/**
	 * Save or update code
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function save() {
		global $wpdb;

		$value = wp_unslash( $this->custom_data );
		$value = maybe_serialize( $value );

		if ( $this->id > 0 ) {
			$wpdb->update(
				$this->table_name,
				array(
					'code'         => $this->code,
					'status'       => $this->status,
					'amount'       => $this->amount,
					'amount_type'  => $this->amount_type,
					'custom_data'  => $value,
					'author_id'    => $this->author_id,
					'last_updated' => date_i18n( 'Y-m-d H:i:s' ),
				),
				array( 'id' => $this->id )
			);
		} else {
			$result = $wpdb->insert(
				$this->table_name,
				array(
					'code'         => $this->code,
					'status'       => $this->status,
					'amount'       => $this->amount,
					'amount_type'  => $this->amount_type,
					'custom_data'  => $value,
					'author_id'    => $this->author_id,
					'code_type'    => $this->code_type,
					'date_created' => date_i18n( 'Y-m-d H:i:s' ),
				)
			);

			if ( ! $result ) {
				return false;
			} else {
				$this->id = (int) $wpdb->insert_id;
			}
		}

		/**
		 * Action called after code is saved
		 *
		 * @param object $code
		 *
		 * @since 1.0.0
		 */
		do_action( 'hubloy_membership_after_save_code_' . $this->code_type, $this );

		return $this->id;
	}

	/**
	 * Get the code value
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_code_value() {
		if ( 'percentage' === $this->amount_type ) {
			$value = $this->amount . '%';
		} else {
			$code  = Currency::get_membership_currency();
			$value = $code . '' . $this->amount;
		}
		/**
		 * Filter to get code value
		 *
		 * @param string $value - the value
		 * @param object $code
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		return apply_filters( 'hubloy_membership_get_code_value_' . $this->code_type, $value, $this );
	}

	/**
	 * Calculate the discount value.
	 * This is mainly for coupons.
	 * 
	 * @param int $total The invoice total.
	 * 
	 * @since 1.1.0
	 * 
	 * @return int
	 */
	public function calculate_discount_value( $total ) {
		if ( 'percentage' === $this->amount_type ) {
			return Currency::round( ( $this->amount * $total ) / 100 );
		}
		return $this->amount;
	}

	/**
	 * Get code type
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_code_type() {
		return $this->code_type;
	}

	/**
	 * Count codes
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function count() {
		global $wpdb;
		$sql     = "SELECT COUNT(id) FROM {$this->table_name} WHERE `code_type` = %s";
		$results = $wpdb->get_var( $wpdb->prepare( $sql, $this->code_type ) );
		return $results;
	}

	/**
	 * Get allowed emails
	 * 
	 * @since 1.1.0
	 * 
	 * @return array
	 */
	public function get_allowed_emails() {
		$data = $this->get_custom_data( 'restrict' );
		if ( ! $data ) {
			return array();
		}
		return explode( ',', strtolower( $data ) );
	}

	/**
	 * Get Custom data value
	 *
	 * @param string $meta_key - the meta key
	 *
	 * @since 1.1.0
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
	 * Check if an email is allowed to use the code.
	 * 
	 * @param string $email The email
	 * 
	 * @since 1.1.0
	 * 
	 * @return bool
	 */
	public function is_email_allowed( $email ) {
		$emails = $this->get_allowed_emails();
		return in_array( strtolower( $email ), $emails, true );
	}

	/**
	 * List all codes for pagination
	 *
	 * @param int  $per_page - items per page
	 * @param int  $page - current page
	 * @param bool $to_html - return readable html array used in tables
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_all( $per_page, $page = 0, $to_html = true ) {
		global $wpdb;
		$codes   = array();
		$sql     = "SELECT `id` FROM {$this->table_name} WHERE `code_type` = %s ORDER BY `id` DESC LIMIT %d, %d";
		$results = $wpdb->get_results( $wpdb->prepare( $sql, $this->code_type, $page, $per_page ) );
		if ( ! empty( $results ) ) {
			$called_class = get_called_class();
			foreach ( $results as $result ) {
				$code    = new $called_class( $result->id );
				$codes[] = $to_html ? $code->to_html() : $code;
			}
		}
		return $codes;
	}

	/**
	 * Get drop down of key => value
	 * This returns a simple key value representaion of the codes. The key is the id and the value is the code
	 *
	 * @param string $status - the code status
	 * @param array  $ids - the ids
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function drop_down( $status, $ids = array() ) {
		global $wpdb;
		$codes = array();
		$and   = '';
		if ( ! empty( $ids ) ) {
			$and = ' AND `id` IN (' . implode( ',', array_fill( 0, count( $ids ), '%d' ) ) . ')';
			$and = $wpdb->prepare( $and, $ids );
		}
		$sql     = "SELECT `id`, `code` FROM {$this->table_name} WHERE `code_type` = %s AND `status` = %s $and ORDER BY `id` DESC";
		$results = $wpdb->get_results( $wpdb->prepare( $sql, $this->code_type, $status ) );
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$codes[] = array(
					'id'   => $result->id,
					'name' => $result->code,
				);
			}
		}
		return $codes;
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
			'hubloy_membership_' . $this->code_type . '_code_to_html',
			array(
				'id'           => $this->id,
				'code'         => $this->code,
				'status'       => \HubloyMembership\Services\Codes::get_code_status( $this->status ),
				'base_status'  => $this->status,
				'amount'       => $this->amount,
				'amount_type'  => $this->amount_type,
				'code_value'   => $this->get_code_value(),
				'custom_data'  => $this->custom_data,
				'author'       => $this->author_id,
				'author_data'  => Members::user_details( $this->author_id ),
				'date_created' => $this->date_created,
				'date_updated' => $this->date_updated,
			),
			$this
		);
	}

}


