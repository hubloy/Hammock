<?php
namespace HubloyMembership\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Core\Database;
use HubloyMembership\Model\Meta;
use HubloyMembership\Model\Membership;
use HubloyMembership\Helper\Duration;

/**
 * Memberships service
 *
 * @since 1.0.0
 */
class Memberships {

	/**
	 * The table name
	 *
	 * @var string
	 */
	private $table_name;


	/**
	 * The meta object
	 *
	 * @var object
	 */
	private $meta;

	/**
	 * Membership payment type constants.
	 * Permanent access
	 *
	 * @since  1.0.0
	 */
	const PAYMENT_TYPE_PERMANENT = 'permanent';

	/**
	 * Membership payment type constants.
	 * This is a membership set to work within different date ranges
	 *
	 * @since  1.0.0
	 */
	const PAYMENT_TYPE_DATE_RANGE = 'date-range';

	/**
	 * Membership payment type constants.
	 * The only type that auto-renews without asking the user!
	 *
	 * @since  1.0.0
	 */
	const PAYMENT_TYPE_RECURRING = 'recurring';


	/**
	 * Main service constructor
	 *
	 * Sets up the service
	 */
	public function __construct() {
		$this->table_name = Database::get_table_name( Database::MEMBERSHIP );
		$this->meta       = new Meta();
	}

	/**
	 * Get Payment Types
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function payment_types() {
		$payment_types = array(
			self::PAYMENT_TYPE_PERMANENT  => __( 'One payment for permanent access', 'hubloy-membership' ),
			self::PAYMENT_TYPE_DATE_RANGE => __( 'One payment for date range access', 'hubloy-membership' ),
			self::PAYMENT_TYPE_RECURRING  => __( 'Recurring payment', 'hubloy-membership' ),
		);
		return apply_filters( 'hubloy-membership_membership_payment_types', $payment_types );
	}

	/**
	 * Get payment durations
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function payment_durations() {
		return apply_filters(
			'hubloy-membership_membership_payment_durations',
			array(
				Duration::PERIOD_TYPE_DAYS   => __( 'Daily', 'hubloy-membership' ),
				Duration::PERIOD_TYPE_WEEKS  => __( 'Weekly', 'hubloy-membership' ),
				Duration::PERIOD_TYPE_MONTHS => __( 'Monthly', 'hubloy-membership' ),
				Duration::PERIOD_TYPE_YEARS  => __( 'Annually', 'hubloy-membership' ),
			)
		);
	}

	/**
	 * Get readable payment duration
	 *
	 * @param string $duration
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_payment_duration( $duration ) {
		$durations = self::payment_durations();
		return isset( $durations[ $duration ] ) ? $durations[ $duration ] : __( 'N/A', 'hubloy-membership' );
	}


	/**
	 * Get tial durations
	 * This is set for the trial period
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function trial_duration() {
		return apply_filters(
			'hubloy-membership_membership_trial_duration',
			array(
				Duration::PERIOD_TYPE_DAYS   => __( 'Day', 'hubloy-membership' ),
				Duration::PERIOD_TYPE_WEEKS  => __( 'Week', 'hubloy-membership' ),
				Duration::PERIOD_TYPE_MONTHS => __( 'Month', 'hubloy-membership' ),
				Duration::PERIOD_TYPE_YEARS  => __( 'Year', 'hubloy-membership' ),
			)
		);
	}

	/**
	 * Get readable trial duration
	 *
	 * @param string $duration
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_trial_duration( $duration ) {
		$durations = self::trial_duration();
		return isset( $durations[ $duration ] ) ? $durations[ $duration ] : __( 'N/A', 'hubloy-membership' );
	}

	/**
	 * Get Membership type
	 *
	 * @param  string $type - /the membership type
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_type( $type ) {
		$types = self::payment_types();
		return isset( $types[ $type ] ) ? $types[ $type ] : __( 'N/A', 'hubloy-membership' );
	}

	/**
	 * Count all Memberships
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function count_memberships() {
		global $wpdb;
		$sql   = "SELECT count(`id`) FROM {$this->table_name}";
		$total = $wpdb->get_var( $sql );
		return $total;
	}


	/**
	 * Get membership by membership id
	 *
	 * @param string $membership_id - items per page
	 *
	 * @since 1.0.0
	 *
	 * @return object|bool - false if not found. Object if found
	 */
	public function get_membership_by_membership_id( $membership_id ) {
		global $wpdb;
		$sql    = "SELECT `id` FROM {$this->table_name} WHERE `membership_id` = %s";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, $membership_id ) );
		if ( $result ) {
			return $this->get_membership_by_id( $result );
		}
		return false;
	}

	/**
	 * Get membership by id
	 *
	 * @param int $id - the id
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function get_membership_by_id( $id ) {
		return new Membership( $id );
	}

	/**
	 * List Memberships
	 *
	 * @param int $per_page - items per page
	 * @param int $page - current page
	 *
	 * @since 1.0.0
	 *
	 * @return array ( @see Membership )
	 */
	public function list_memberships( $per_page, $page = 0 ) {
		global $wpdb;
		$memberships = array();
		$page        = $per_page * $page;
		$sql         = "SELECT `id` FROM {$this->table_name} ORDER BY `id` DESC LIMIT %d, %d";
		$results     = $wpdb->get_results( $wpdb->prepare( $sql, $page, $per_page ) );

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$memberships[] = $this->get_membership_by_id( $result->id );
			}
		}

		return $memberships;
	}


	/**
	 * List Memberships
	 *
	 * @param int $per_page - items per page
	 * @param int $page - current page
	 *
	 * @since 1.0.0
	 *
	 * @return array ( @see Membership )
	 */
	public function list_html_memberships( $per_page, $page = 0 ) {
		global $wpdb;
		$memberships = array();
		$page        = $per_page * $page;
		$sql         = "SELECT `id` FROM {$this->table_name} ORDER BY `id` DESC LIMIT %d, %d";
		$results     = $wpdb->get_results( $wpdb->prepare( $sql, $page, $per_page ) );

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$membership    = $this->get_membership_by_id( $result->id );
				$memberships[] = $membership->to_html();
			}
		}

		return $memberships;
	}

	/**
	 * List key value of memberships. This will return id and name only of all memberships
	 *
	 * @param int  $member_id - the member id (optional)
	 * @param bool $include_select - include select in the drop down (optional)
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_simple_memberships( $member_id = 0, $include_select = true ) {
		global $wpdb;
		$where = '';
		if ( $include_select ) {
			$memberships = array(
				0 => __( 'Select Membership', 'hubloy-membership' ),
			);
		} else {
			$memberships = array();
		}
		if ( $member_id > 0 ) {
			$members = new Members();
			$ids     = $members->get_member_plan_membership_ids( $member_id );
			if ( $ids ) {
				$where = " AND `id` NOT IN($ids)";
			}
		}
		$sql     = "SELECT `id`, `name` FROM {$this->table_name} WHERE `enabled` = 1 $where ORDER BY `id` DESC";
		$results = $wpdb->get_results( $sql );
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$memberships[ $result->id ] = $result->name;
			}
		}
		return $memberships;
	}

	/**
	 * List memberships a user can sign up to
	 *
	 * @param int $user_id - the user id
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_signup_memberships( $user_id = 0 ) {
		global $wpdb;
		$where       = '';
		$memberships = array();
		if ( $user_id > 0 ) {
			$members = new Members();
			$member  = $members->get_member_by_user_id( $user_id );
			if ( $member ) {
				$ids = $members->get_member_plan_membership_ids( $member->id );
				if ( $ids ) {
					$where = " AND `id` NOT IN($ids)";
				}
			}
		}
		$sql     = "SELECT `id` FROM {$this->table_name} WHERE `enabled` = 1 $where ORDER BY `id` DESC";
		$results = $wpdb->get_results( $sql );
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$memberships[ $result->id ] = $this->get_membership_by_id( $result->id );
			}
		}
		return $memberships;
	}

	/**
	 * Save Membership
	 *
	 * @param string $name - the membership name
	 * @param string $details - the membership details
	 * @param bool   $enabled - membership status
	 * @param string $type - membership type
	 * @param string $price - membership price
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function save( $name, $details, $enabled, $type, $price ) {
		global $wpdb;
		$membership_id = wp_generate_password( 12, false );
		$result        = $wpdb->insert(
			$this->table_name,
			array(
				'membership_id' => $membership_id,
				'name'          => $name,
				'details'       => $details,
				'enabled'       => $enabled,
				'price'         => $price,
				'type'          => $type,
				'date_created'  => date_i18n( 'Y-m-d H:i:s' ),
			)
		);

		if ( ! $result ) {
			return false;
		} else {

			$id = (int) $wpdb->insert_id;

			/**
			 * Action called when the general details are updated
			 *
			 * @param int $id - the membership id
			 *
			 * @since 1.0.0
			 */
			do_action( 'hubloy-membership_memberships_plan_created', $id );
			return $id;
		}
	}

	/**
	 * Update General settings of membership
	 *
	 * @param int    $id - the membership id
	 * @param string $name - the membership name
	 * @param string $details - the membership details
	 * @param bool   $enabled - membership status
	 * @param string $type - membership type
	 * @param bool   $lmited - enable or disable limitation
	 * @param int    $available - total available
	 *
	 * @since 1.0.0
	 */
	public function update_general( $id, $name, $details, $enabled, $type, $lmited, $available ) {
		global $wpdb;
		$wpdb->update(
			$this->table_name,
			array(
				'name'            => $name,
				'details'         => $details,
				'enabled'         => $enabled,
				'type'            => $type,
				'limit_spaces'    => $lmited,
				'total_available' => $available,
				'date_updated'    => date_i18n( 'Y-m-d H:i:s' ),
			),
			array( 'id' => $id )
		);

		/**
		 * Action called when the general details are updated
		 *
		 * @param int $id - the membership id
		 *
		 * @since 1.0.0
		 */
		do_action( 'hubloy-membership_memberships_general_updated', $id );

		/**
		 * General action for all updates on a membership
		 *
		 * @param int $id - the membership id
		 *
		 * @since 1.0.0
		 */
		do_action( 'hubloy-membership_memberships_updated', $id );
	}

	/**
	 * Update the membership duration
	 *
	 * @param int    $id - the membership id
	 * @param string $duration - the membership duration
	 *
	 * @since 1.0.0
	 */
	public function update_duration( $id, $duration ) {
		global $wpdb;
		$wpdb->update(
			$this->table_name,
			array(
				'duration'     => $duration,
				'date_updated' => date_i18n( 'Y-m-d H:i:s' ),
			),
			array( 'id' => $id )
		);

		/**
		 * Action called when duration is updated
		 *
		 * @param int $id - the membership id
		 *
		 * @since 1.0.0
		 */
		do_action( 'hubloy-membership_memberships_duration_updated', $id );

		/**
		 * General action for all updates on a membership
		 *
		 * @param int $id - the membership id
		 *
		 * @since 1.0.0
		 */
		do_action( 'hubloy-membership_memberships_updated', $id );
	}

	/**
	 * Update membership price section
	 *
	 * @param int     $id - the membership id
	 * @param double  $price - the membership price
	 * @param double  $signup_price - the membership sign up price for new members
	 * @param boolean $trial_enabled - trial enabled
	 * @param int     $trial_price - the trial price
	 * @param int     $trial_period - the trial period
	 * @param string  $trial_duration - the trial duration period
	 *
	 * @since 1.0.0
	 */
	public function update_price( $id, $price, $signup_price, $trial_enabled, $trial_price, $trial_period, $trial_duration ) {
		global $wpdb;
		$wpdb->update(
			$this->table_name,
			array(
				'price'          => $price,
				'signup_price'   => $signup_price,
				'trial_enabled'  => $trial_enabled,
				'trial_price'    => $trial_price,
				'trial_period'   => $trial_period,
				'trial_duration' => $trial_duration,
				'date_updated'   => date_i18n( 'Y-m-d H:i:s' ),
			),
			array( 'id' => $id )
		);

		/**
		 * Action called when price is updated
		 *
		 * @param int $id - the membership id
		 *
		 * @since 1.0.0
		 */
		do_action( 'hubloy-membership_memberships_price_updated', $id );

		/**
		 * General action for all updates on a membership
		 *
		 * @param int $id - the membership id
		 *
		 * @since 1.0.0
		 */
		do_action( 'hubloy-membership_memberships_updated', $id );
	}

	/**
	 * Save Meta
	 *
	 * @param int    $membership_id - the membership id
	 * @param string $key - the meta key
	 * @param string $value - the meta value
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function save_meta( $membership_id, $key, $value ) {
		return $this->meta->save( $membership_id, 'membership', $key, $value );
	}

	/**
	 * Save or update meta
	 *
	 * @param int    $membership_id - the membership id
	 * @param string $key - the meta key
	 * @param string $value - the meta value
	 *
	 * @since 1.0.0
	 */
	public function update_meta( $membership_id, $key, $value ) {
		$this->meta->update( $membership_id, 'membership', $key, $value );
	}
}

