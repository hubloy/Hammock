<?php
namespace Hammock\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Services\Memberships;
use Hammock\Services\Members;
use Hammock\Helper\Duration;

/**
 * Member subscription plan
 *
 * @since 1.0.0
 */
class Plan {
	/**
	 * The database ID
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * The unique plan id
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plan_id = '';

	/**
	 * The member id
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $member_id = 0;

	/**
	 * The membership id
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $membership_id = 0;

	/**
	 * The membership object
	 *
	 * @since 1.0.0
	 *
	 * @var Membership
	 */
	public $membership = null;

	/**
	 * Enabled status
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $enabled = false;

	/**
	 * Subscription status
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $status = '';

	/**
	 * Subscription status detail
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $status_detail = '';

	/**
	 * Preferred gateway
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gateway = '';

	/**
	 * The gateway subscription id
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gateway_subscription_id = '';

	/**
	 * The subscription start date
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $start_date = '';

	/**
	 * The subscription end date
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $end_date = '';

	/**
	 * The subscription start date
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $start_date_timestamp = 0;

	/**
	 * The subscription end date
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $end_date_timestamp = 0;

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
	 * Membership meta
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $meta = array();


	/**
	 * The table name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $table_name;


	/**
	 * Members service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $members_service = null;


	/**
	 * Membership service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $membership_service = null;


	/**
	 * Initialize the Model
	 *
	 * @since 1.0
	 */
	public function __construct( $id = null ) {
		$this->table_name         = Database::get_table_name( Database::PLANS );
		$this->membership_service = new Memberships();
		$this->members_service    = new Members();
		if ( is_numeric( $id ) && $id > 0 ) {
			$this->get_one( $id );
		}
	}

	/**
	 * Save or update plan
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function save() {
		global $wpdb;

		if ( is_numeric( $this->id ) && $this->id > 0 ) {
			$wpdb->update(
				$this->table_name,
				array(
					'membership_id'           => $this->membership_id,
					'enabled'                 => $this->enabled,
					'status'                  => $this->status,
					'gateway'                 => $this->gateway,
					'gateway_subscription_id' => $this->gateway_subscription_id,
					'start_date'              => ! empty( $this->start_date ) ? date_i18n( 'Y-m-d H:i:s', strtotime( $this->start_date ) ) : '',
					'end_date'                => ! empty( $this->end_date ) ? date_i18n( 'Y-m-d H:i:s', strtotime( $this->end_date ) ) : '',
					'date_updated'            => date_i18n( 'Y-m-d H:i:s' ),
				),
				array( 'id' => $this->id )
			);
		} else {
			$result = $wpdb->insert(
				$this->table_name,
				array(
					'plan_id'                 => $this->plan_id,
					'member_id'               => $this->member_id,
					'membership_id'           => $this->membership_id,
					'enabled'                 => $this->enabled,
					'status'                  => $this->status,
					'gateway'                 => $this->gateway,
					'gateway_subscription_id' => $this->gateway_subscription_id,
					'start_date'              => ! empty( $this->start_date ) ? date_i18n( 'Y-m-d H:i:s', strtotime( $this->start_date ) ) : '',
					'end_date'                => ! empty( $this->end_date ) ? date_i18n( 'Y-m-d H:i:s', strtotime( $this->end_date ) ) : '',
					'date_created'            => date_i18n( 'Y-m-d H:i:s' ),
				)
			);

			if ( ! $result ) {
				return false;
			}

			$this->id = (int) $wpdb->insert_id;
		}
		return $this->id;
	}

	/**
	 * Delete plan
	 *
	 * @since 1.0.0
	 */
	public function delete() {
		global $wpdb;

		$member_id     = $this->member_id;
		$membership_id = $this->membership_id;

		/**
		 * Action called before a plan is deleted
		 *
		 * @param object $plan - the plan
		 * @param int $member_id - the member id
		 *
		 * @since 1.0.0
		 */
		do_action( 'hammock_member_before_delete_plan', $this, $member_id, $membership_id );

		Meta::remove_all( $this->id, 'plan' );
		$sql = "DELETE FROM {$this->table_name} WHERE `id` = %d";
		$wpdb->query( $wpdb->prepare( $sql, $this->id ) );

		/**
		 * Action called after plan is deleted
		 *
		 * @param int $member_id - the member id
		 * @param int $membership_id - the membership id
		 *
		 * @since 1.0.0
		 */
		do_action( 'hammock_member_after_delete_plan', $member_id, $membership_id );
	}

	/**
	 * Get membership
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_memebership() {
		return new Membership( $this->membership_id );
	}

	/**
	 * Get plan by member and membership id
	 *
	 * @param int $member_id - the member id
	 * @param int $membership_id - the membership id
	 *
	 * @since 1.0.0
	 *
	 * @return object|bool
	 */
	public static function get_plan( $member_id, $membership_id ) {
		global $wpdb;
		$table_name = Database::get_table_name( Database::PLANS );
		$sql        = "SELECT `id` FROM {$table_name} WHERE `member_id` = %d AND `membership_id` = %d";
		$item       = $wpdb->get_row( $wpdb->prepare( $sql, $member_id, $membership_id ) );
		if ( $item ) {
			return new Plan( $item->id );
		}
		return false;
	}

	/**
	 * Get plan by member and membership id
	 *
	 * @param string $gateway_subscription_id The gateway subscription id
	 *
	 * @since 1.0.0
	 *
	 * @return object|bool
	 */
	public static function get_plan_by_gateway_subscription_id( $gateway_subscription_id ) {
		global $wpdb;
		$table_name = Database::get_table_name( Database::PLANS );
		$sql        = "SELECT `id` FROM {$table_name} WHERE `gateway_subscription_id` = %s";
		$item       = $wpdb->get_row( $wpdb->prepare( $sql, $gateway_subscription_id ) );
		if ( $item ) {
			return new Plan( $item->id );
		}
		return false;
	}


	/**
	 * Get one by id
	 * Checks the database and sets all values in the model
	 *
	 * @param string $id - the member id
	 *
	 * @since 1.0.0
	 */
	public function get_one( $id ) {
		global $wpdb;
		$sql  = "SELECT `plan_id`, `date_created`, `date_updated`, `member_id`, `membership_id`, `enabled`, `status`, `gateway`, `gateway_subscription_id`, `start_date`, `end_date` FROM {$this->table_name} WHERE `id` = %d";
		$item = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );
		if ( $item ) {
			$date_format                   = get_option( 'date_format' );
			$this->id                      = $id;
			$this->plan_id                 = $item->plan_id;
			$this->date_created            = date_i18n( $date_format, strtotime( $item->date_created ) );
			$this->date_updated            = ! empty( $item->date_updated ) ? date_i18n( $date_format, strtotime( $item->date_updated ) ) : '';
			$this->start_date              = ! empty( $item->start_date ) ? date_i18n( $date_format, strtotime( $item->start_date ) ) : '';
			$this->end_date                = ! empty( $item->end_date ) ? date_i18n( $date_format, strtotime( $item->end_date ) ) : '';
			$this->start_date_timestamp    = ! empty( $item->start_date ) ? strtotime( $item->start_date ) : 0;
			$this->end_date_timestamp      = ! empty( $item->end_date ) ? strtotime( $item->end_date ) : 0;
			$this->member_id               = $item->member_id;
			$this->membership_id           = $item->membership_id;
			$this->membership              = $this->get_memebership();
			$this->enabled                 = ( $item->enabled == 1 );
			$this->status                  = $item->status;
			$this->status_detail           = Members::get_status( $item->status );
			$this->gateway                 = $item->gateway;
			$this->gateway_subscription_id = $item->gateway_subscription_id;
			$this->meta                    = Meta::get_all( $id, 'plan' );
		}
	}

	/**
	 * Active status of subscription
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_active() {
		$is_active = false;
		if ( Members::STATUS_ACTIVE == $this->status ) {
			if ( $this->is_expired() ) {
				$is_active = false;
			} else {
				$is_active = true;
			}
		} else {
			$is_active = $this->has_trial();
		}
		return apply_filters( 'hammock_plan_is_active', $is_active, $this );
	}

	/**
	 * Check if plan is expired
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_expired() {
		if ( ! empty( $this->end_date ) ) {
			if ( Duration::is_past_date( $this->end_date, $this->start_date ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Trial status of subscription
	 * Trial is set on start and end date
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_trial() {
		$has_trial = false;
		if ( Members::STATUS_TRIAL == $this->status ) {
			if ( $this->is_expired() ) {
				$has_trial = false;
			} else {
				$has_trial = true;
			}
		}
		return apply_filters( 'hammock_plan_has_trial', $has_trial, $this );
	}

	/**
	 * Set plan trial status and period
	 *
	 * @param Membership $membership - the membership
	 *
	 * @since 1.0.0
	 */
	public function set_trial( $membership ) {
		$this->status     = Members::STATUS_TRIAL;
		$end_date         = Duration::add_interval( $membership->trial_period, $membership->trial_duration );
		$this->start_date = date_i18n( 'Y-m-d H:i:s' );
		$this->end_date   = $end_date;
	}

	/**
	 * Set the active membership
	 * This sets the membership status and the correct dates
	 *
	 * @param Membership $membership - the membership
	 * @param bool $change_start Set to true to change the start date
	 *
	 * @since 1.0.0
	 */
	public function set_active_membership( $membership, $change_start = true ) {
		$this->status = Members::STATUS_ACTIVE;
		if ( $change_start ) {
			$this->start_date = date_i18n( 'Y-m-d H:i:s' );
		}
		if ( $membership->type === Memberships::PAYMENT_TYPE_PERMANENT ) {
			$this->end_date   = '';
		} elseif ( $membership->type === Memberships::PAYMENT_TYPE_DATE_RANGE ) {
			$days = $membership->get_meta_value( 'membership_days' );
			if ( $days ) {
				$end_date         = Duration::add_interval( $days, Duration::PERIOD_TYPE_DAYS );
				$this->end_date   = $end_date;
			}
		} elseif ( $membership->type === Memberships::PAYMENT_TYPE_RECURRING ) {
			$end_date         = Duration::add_interval( 1, $membership->duration );
			$this->end_date   = $end_date;
		}
	}

	/**
	 * Refresh meta
	 * Loads meta from the database for the plan
	 *
	 * @since 1.0.0
	 */
	public function refresh_meta() {
		$this->meta = Meta::get_all( $this->id, 'plan' );
	}

	/**
	 * Get meta value from key
	 *
	 * @param string $meta_key - the meta key
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_meta_value( $meta_key ) {
		if ( isset( $this->meta[ $meta_key ] ) ) {
			return $this->meta[ $meta_key ]['meta_value'];
		}
		return false;
	}

	/**
	 * Record the payment and manage the plan.
	 * Continue or dicontinue a plan.
	 *
	 * @param \Hammock\Model\Invoice The invoice
	 *
	 * @since 1.0.0
	 */
	public function record_payment( $invoice ) {
		$membership = $this->get_memebership();
		$this->set_active_membership( $membership );
		$this->save();

		/**
		 * Action called after a new payment is recorded
		 * 
		 * @param \Hammock\Model\Plan The current plan
		 * @param \Hammock\Model\Invoice The invoice
		 * 
		 * @since 1.0.0
		 */
		do_action( 'hammock_plan_record_payment', $this, $invoice );
	}

	/**
	 * Cancel subscription
	 *
	 * @since 1.0.0
	 */
	public function cancel() {
		$this->status = Members::STATUS_CANCELED;
		$this->save();
	}

	/**
	 * Render values to readable strings
	 *
	 * @since 1.0.0
	 */
	public function to_html() {
		return apply_filters(
			'hammock_plan_to_html',
			array(
				'id'              => $this->id,
				'plan_id'         => $this->plan_id,
				'date_created'    => $this->date_created,
				'date_updated'    => $this->date_updated,
				'start_date'      => $this->start_date,
				'end_date'        => empty( $this->end_date ) ? __( 'N/A', 'hammock' ) : $this->end_date,
				'start_date_edit' => date_i18n( 'Y-m-d', $this->start_date_timestamp ),
				'end_date_edit'   => $this->end_date_timestamp > 0 ? date_i18n( 'Y-m-d', $this->end_date_timestamp ) : '',
				'member_id'       => $this->member_id,
				'membership'      => $this->membership->is_valid() ? $this->membership->to_html() : false,
				'enabled'         => $this->enabled,
				'status'          => $this->status_detail,
				'status_simple'   => $this->status,
				'gateway'         => empty( $this->gateway ) ? __( 'N/A', 'hammock' ) : $this->gateway,
				'is_active'       => $this->is_active(),
				'has_trial'       => $this->has_trial(),
			),
			$this
		);
	}
}

