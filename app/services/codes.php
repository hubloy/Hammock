<?php
namespace HubloyMembership\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Model\Membership;

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
	public function __construct( $type = '' ) {
		$this->set_model( $type );
	}

	/**
	 * Set the model
	 * 
	 * @param string $type The model type.
	 * 
	 * @since 1.1.0
	 */
	public function set_model( $type ) {
		if ( $type === 'coupons' ) {
			$this->model = new \HubloyMembership\Model\Codes\Coupons();
		} else {
			$this->model = new \HubloyMembership\Model\Codes\Invites();
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
			self::STATUS_ENABLED  => __( 'Enabled', 'memberships-by-hubloy' ),
			self::STATUS_DISABLED => __( 'Disabled', 'memberships-by-hubloy' ),
			self::STATUS_EXPIRED  => __( 'Expired', 'memberships-by-hubloy' ),
			self::STATUS_CANCELED => __( 'Canceled', 'memberships-by-hubloy' ),
		);
		return apply_filters( 'hubloy_membership_codes_get_code_statuses', $status );
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
			self::TYPE_FIXED      => __( 'Fixed discount', 'memberships-by-hubloy' ),
			self::TYPE_PRECENTAGE => __( 'Percentage discount', 'memberships-by-hubloy' ),
		);
		return apply_filters( 'hubloy_membership_codes_get_code_amount_types', $types );
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
		$return   = isset( $statuses[ $status ] ) ? $statuses[ $status ] : __( 'N\A', 'memberships-by-hubloy' );
		return apply_filters( 'hubloy_membership_codes_get_code_status', $return, $status );
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
		$return = isset( $types[ $type ] ) ? $types[ $type ] : __( 'N\A', 'memberships-by-hubloy' );
		return apply_filters( 'hubloy_membership_codes_get_code_amount_type', $return, $type );
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
					'message' => __( 'Saved successfully', 'memberships-by-hubloy' ),
				);
			} else {
				return array(
					'status'  => false,
					'message' => __( 'Error saving model', 'memberships-by-hubloy' ),
				);
			}
		} else {
			return array(
				'status'  => false,
				'message' => __( 'Selected Code exists', 'memberships-by-hubloy' ),
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
					'message' => __( 'Updated successfully', 'memberships-by-hubloy' ),
				);
			} else {
				return array(
					'status'  => false,
					'message' => __( 'Selected Code exists', 'memberships-by-hubloy' ),
				);
			}
		} else {
			return array(
				'status'  => false,
				'message' => __( 'Selected Code does not exist', 'memberships-by-hubloy' ),
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
		return apply_filters( 'hubloy_membership_generate_code', $random_coupon );
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

	/**
	 * Validate invite code is valid for membership
	 * 
	 * @param string $code       The invite code.
	 * @param int $membership_id The membership id.
	 * @param string $email      The user email.
	 * 
	 * @since 1.1.0
	 * 
	 * @return array
	 */
	public function validate_invite_code( $code, $membership_id, $email ) {
		$this->set_model( 'invite' );
		$model = false;
		$this->is_valid_for_use( $model, $code, $email );

		$membership = new Membership( $membership_id );
		if ( $model && $membership->is_code_isted( $model->id ) ) {
			return array(
				'status'  => true,
				'message' => __( 'Valid invite code', 'memberships-by-hubloy' ),
				'model'   => $model,
			);
		}

		return array(
			'status' => false,
			'message' => __( 'You do not have permission to use this code', 'memberships-by-hubloy' ),
		);
	}

	/**
	 * Check if a coupon code is valid.
	 * 
	 * @param string $code       The coupon code.
	 * @param string $email      The email address.
	 * 
	 * @since 1.1.0
	 * 
	 * @return array
	 */
	public function validate_coupon_code( $code, $email ) {
		$this->set_model( 'coupons' );
		$model = false;
		$this->is_valid_for_use( $model, $code, $email );
		// Check for membership restriction.
		if ( $model && 'coupons' === $model->get_code_type() ) {
			$usage = $model->get_custom_data( 'usage' );
			if ( $usage ) {
				$current_usage = $model->get_usage( $email );
				if ( $current_usage >= $usage ) {
					// Usage cap reached.
					return array(
						'status' => false,
						'message' => __( 'You have reached the maximum times this coupon is valid for', 'memberships-by-hubloy' ),
					);
				}
			}
			return array(
				'status'  => true,
				'message' => __( 'Valid coupon code', 'memberships-by-hubloy' ),
				'model'   => $model,
			);
		}
		return array(
			'status' => false,
			'message' => __( 'You do not have permission to use this code', 'memberships-by-hubloy' ),
		);
	}

	/**
	 * Check if the selected code is valid for the current use.
	 * 
	 * @param bool|object $model   The code model
	 * @param string $code         The code.
	 * @param string $email        The email of the current user.
	 * 
	 * @since 1.1.0
	 * 
	 * @return array
	 */
	private function is_valid_for_use( &$model, $code, $email ) {
		$model = $this->get_code( $code );
		if ( ! $model ) {
			return array(
				'status' => false,
				'message' => __( 'Invalid code', 'memberships-by-hubloy' ),
			);
		}
		// Check if is email restricted.
		if ( ! $model->is_email_allowed( $email ) ) {
			return array(
				'status' => false,
				'message' => __( 'You do not have permission to this code', 'memberships-by-hubloy' ),
			);
		}
	}

	/**
	 * Record coupon usage.
	 * 
	 * @param \HubloyMembership\Model\Codes\Invoice The invoice.
	 * 
	 * @since 1.1.0
	 */
	private function record_coupon_usage( $invoice ) {
		$coupon_id = $invoice->get_custom_data( 'coupon_id' );
		if ( $coupon_id ) {
			$coupon = new Codes( $coupon_id );
			if ( $coupon->is_valid() ) {
				$member = $invoice->get_member();
				$email  = $member->get_user_info( 'email' );
				$coupon->record_usage( $email );
			}
		}
	}
}

