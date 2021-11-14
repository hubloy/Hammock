<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Codes {

	/**
	 * Various code status
	 */
	const STATUS_ENABLED  = 'enabled';
	const STATUS_DISABLED = 'disabled';
	const STATUS_EXPIRED  = 'expired';
	const STATUS_CANCELED = 'canceled';

	/**
	 * Amount types
	 */
	const TYPE_FIXED      = 'number';
	const TYPE_PRECENTAGE = 'percentage';

	/**
	 * The current code mode
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $model = null;


	/**
	 * Main constructor
	 *
	 * @param string $type - the code type
	 *
	 * @since 1.0.0
	 */
	public function __construct( $type ) {
		if ( $type === 'coupons' ) {
			$this->model = new \Hammock\Model\Codes\Coupons();
		} else {
			$this->model = new \Hammock\Model\Codes\Invites();
		}
	}

	/**
	 * Get the code statuses
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_code_statuses() {
		$status = array(
			self::STATUS_ENABLED  => __( 'Enabled', 'hammock' ),
			self::STATUS_DISABLED => __( 'Disabled', 'hammock' ),
			self::STATUS_EXPIRED  => __( 'Expired', 'hammock' ),
			self::STATUS_CANCELED => __( 'Canceled', 'hammock' ),
		);
		return apply_filters( 'hammock_codes_get_code_statuses', $status );
	}

	/**
	 * Get code amount types
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_code_amount_types() {
		$types = array(
			self::TYPE_FIXED      => __( 'Fixed discount', 'hammock' ),
			self::TYPE_PRECENTAGE => __( 'Percentage discount', 'hammock' ),
		);
		return apply_filters( 'hammock_codes_get_code_amount_types', $types );
	}

	/**
	 * Get single code status
	 *
	 * @param string $status
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_code_status( $status ) {
		$statuses = self::get_code_statuses();
		$return   = isset( $statuses[ $status ] ) ? $statuses[ $status ] : __( 'N\A', 'hammock' );
		return apply_filters( 'hammock_codes_get_code_status', $return, $status );
	}

	/**
	 * Get code amount type
	 *
	 * @param string $type - the type
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_code_amount_type( $type ) {
		$types  = self::get_code_amount_types();
		$return = isset( $types[ $type ] ) ? $types[ $type ] : __( 'N\A', 'hammock' );
		return apply_filters( 'hammock_codes_get_code_amount_type', $return, $type );
	}

	/**
	 * Get model
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_model() {
		return $this->model;
	}

	/**
	 * Save Code
	 *
	 * @param array $params - the post params
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function save_code( $params ) {
		$code   = isset( $params['code'] ) ? sanitize_text_field( $params['code'] ) : $this->generate_code();
		$status = $params['status'];
		$amount = isset( $params['amount'] ) ? sanitize_text_field( $params['amount'] ) : '';
		$type   = isset( $params['amount_type'] ) ? sanitize_text_field( $params['amount_type'] ) : '';
		$author = get_current_user_id();
		$model  = $this->get_model();
		$exists = $model->code_exists( $code );
		if ( ! $exists ) {
			$model->code        = $code;
			$model->status      = $status;
			$model->amount      = $amount;
			$model->amount_type = $type;
			$model->author_id   = $author;
			$restrict           = isset( $params['restrict'] ) ? sanitize_text_field( $params['restrict'] ) : array();
			$expire             = isset( $params['expire'] ) ? sanitize_text_field( $params['expire'] ) : '';
			$custom_data        = array(
				'restrict' => $restrict,
				'expire'   => $expire,
			);
			if ( $model->get_code_type() === 'coupons' ) {
				$usage                = isset( $params['usage'] ) ? sanitize_text_field( $params['usage'] ) : '';
				$custom_data['usage'] = $usage;
			}
			$model->custom_data = $custom_data;
			$model_id           = $model->save();
			if ( $model_id > 0 ) {
				return array(
					'status'  => true,
					'id'      => $model_id,
					'message' => __( 'Saved successfully', 'hammock' ),
				);
			} else {
				return array(
					'status'  => false,
					'message' => __( 'Error saving model', 'hammock' ),
				);
			}
		} else {
			return array(
				'status'  => false,
				'message' => __( 'Selected Code exists', 'hammock' ),
			);
		}
	}

	/**
	 * Update Code
	 *
	 * @param array $params - the post params
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_code( $params ) {
		$code   = isset( $params['code'] ) ? sanitize_text_field( $params['code'] ) : $this->generate_code();
		$status = $params['status'];
		$amount = isset( $params['amount'] ) ? sanitize_text_field( $params['amount'] ) : '';
		$type   = isset( $params['amount_type'] ) ? sanitize_text_field( $params['amount_type'] ) : '';
		$id     = intval( sanitize_text_field( $params['id'] ) );
		$author = get_current_user_id();
		$model  = $this->get_model();
		$model->get_code( $id );
		if ( $model->id > 0 ) {
			if ( $model->code !== $code ) {
				$exists = $model->code_exists( $code );
			} else {
				$exists = false;
			}
			if ( ! $exists ) {
				$model->code        = $code;
				$model->status      = $status;
				$model->amount      = $amount;
				$model->amount_type = $type;
				$model->author_id   = $author;
				$restrict           = isset( $params['restrict'] ) ? sanitize_text_field( $params['restrict'] ) : '';
				$expire             = isset( $params['expire'] ) ? sanitize_text_field( $params['expire'] ) : '';
				$custom_data        = array(
					'restrict' => $restrict,
					'expire'   => $expire,
				);
				if ( $model->get_code_type() === 'coupons' ) {
					$usage                = isset( $params['usage'] ) ? sanitize_text_field( $params['usage'] ) : '';
					$custom_data['usage'] = $usage;
				}
				$model->custom_data = $custom_data;
				$model->save();
				return array(
					'status'  => true,
					'message' => __( 'Updated successfully', 'hammock' ),
				);
			} else {
				return array(
					'status'  => false,
					'message' => __( 'Selected Code exists', 'hammock' ),
				);
			}
		} else {
			return array(
				'status'  => false,
				'message' => __( 'Selected Code does not exist', 'hammock' ),
			);
		}
	}


	/**
	 * Generate code
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function generate_code() {

		// Generate unique coupon code
		$random_coupon = '';
		$length        = 12;
		$charset       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$count         = strlen( $charset );

		while ( $length-- ) {
			$random_coupon .= $charset[ mt_rand( 0, $count - 1 ) ];
		}

		$random_coupon = implode( '-', str_split( strtoupper( $random_coupon ), 4 ) );
		return apply_filters( 'hammock_generate_code', $random_coupon );
	}

	/**
	 * Get code by id or code
	 *
	 * @param string|int $param - the code id or code
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_code( $param ) {
		$model = $this->get_model();
		$model->get_code( $param );
		return $model;
	}

	/**
	 * Get active codes as a key -> value
	 *
	 * @param array $ids - optional ids to get by
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function drop_down( $ids = array() ) {
		$model = $this->get_model();
		return $model->drop_down( self::STATUS_ENABLED, $ids );
	}
}

