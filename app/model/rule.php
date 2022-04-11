<?php
namespace HubloyMembership\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Core\Database;
use HubloyMembership\Helper\Cache;

/**
 * Membership rule
 *
 * @since 1.0.0
 */
class Rule {

	/**
	 * The database ID
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $rule_id = 0;

	/**
	 * The memberships
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $memberships = array();

	/**
	 * The object type
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $object_type = '';

	/**
	 * The object id
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $object_id = 0;

	/**
	 * If the rule is time based
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $time_limit = false;

	/**
	 * The rule time duration in milliseconds
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $time_duration = 0;

	/**
	 * The rule status
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $status = '';

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
	 * Initialize the Model
	 *
	 * @since 1.0
	 */
	public function __construct( $id = null ) {
		$this->table_name = Database::get_table_name( Database::MEMBERSHIP_RULES );
		if ( is_numeric( $id ) && $id > 0 ) {
			$this->get_one( $id );
		}
	}

	/**
	 * Get rule by id
	 *
	 * @param int $id - the rule id
	 *
	 * @since 1.0.0
	 */
	public function get_one( $id ) {
		global $wpdb;
		$sql  = "SELECT `rule_id`, `memberships`, `object_type`, `object_id`, `status`, `time_limit`, `time_duration`, `date_created`, `date_updated` FROM {$this->table_name} WHERE `rule_id` = %d";
		$item = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );
		if ( $item ) {
			$date_format         = get_option( 'date_format' );
			$this->rule_id       = $id;
			$this->object_type   = $item->object_type;
			$this->object_id     = $item->object_id;
			$this->memberships   = is_array( $item->memberships ) ? array_map( 'maybe_unserialize', $item->memberships ) : maybe_unserialize( $item->memberships );
			$this->status        = $item->status;
			$this->time_limit    = $item->time_limit;
			$this->time_duration = $item->time_duration;
			$this->date_created  = date_i18n( $date_format, strtotime( $item->date_created ) );
			$this->date_updated  = ! empty( $item->date_updated ) ? date_i18n( $date_format, strtotime( $item->date_updated ) ) : '';
		}
	}

	/**
	 * Checks if the rule exists
	 * This validates the id is greater than 0
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function exists() {
		return $this->rule_id > 0;
	}

	/**
	 * Check if rule has time limit.
	 * Return the duration if it has a limit
	 *
	 * @since 1.0.0
	 *
	 * @return bool|int
	 */
	public function has_time_limit() {
		if ( ! $this->time_limit ) {
			return false;
		}
		return $this->time_duration;
	}

	/**
	 * Check if a rule has a membership.
	 *
	 * @param int $membership_id The membership id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_membership( $membership_id ) {
		if ( ! is_array( $this->memberships ) || empty( $this->memberships ) ) {
			return false;
		}
		return in_array( $membership_id, $this->memberships, true );
	}

	/**
	 * Check if is enabled
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return ( 'enabled' === $this->status );
	}

	/**
	 * Save or update a rule
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function save() {
		global $wpdb;
		$memberships = wp_unslash( $this->memberships );
		$memberships = maybe_serialize( $memberships );
		if ( $this->rule_id > 0 ) {
			$wpdb->update(
				$this->table_name,
				array(
					'object_type'   => $this->object_type,
					'object_id'     => $this->object_id,
					'memberships'   => $memberships,
					'status'        => $this->status,
					'time_limit'    => $this->time_limit,
					'time_duration' => $this->time_duration,
					'date_updated'  => ! empty( $this->date_updated ) ? date_i18n( 'Y-m-d H:i:s', strtotime( $this->date_updated ) ) : '',
				),
				array( 'rule_id' => $this->rule_id )
			);
		} else {
			$result = $wpdb->insert(
				$this->table_name,
				array(
					'object_type'   => $this->object_type,
					'object_id'     => $this->object_id,
					'memberships'   => $memberships,
					'status'        => $this->status,
					'time_limit'    => $this->time_limit,
					'time_duration' => $this->time_duration,
					'date_created'  => date_i18n( 'Y-m-d H:i:s' ),
				)
			);

			if ( ! $result ) {
				return false;
			} else {
				$this->rule_id = (int) $wpdb->insert_id;
				return true;
			}
		}
	}

	/**
	 * Delete a rule
	 *
	 * @since 1.0.0
	 */
	public function delete() {
		global $wpdb;

		do_action( 'hubloy_membership_rule_before_delete_rule', $this->rule_id );

		$sql = "DELETE FROM {$this->table_name} WHERE `rule_id` = %d";
		$wpdb->query( $wpdb->prepare( $sql, $this->rule_id ) );

		do_action( 'hubloy_membership_rule_after_delete_rule', $this->rule_id );
		$this->rule_id = 0;
	}

	/**
	 * Render values to readable strings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function to_html() {
		$membership_count = is_array( $this->memberships ) ? count( $this->memberships ) : 0;
		$item_title       = apply_filters( 'hubloy_membership_rule_title_name', $this->object_type, $this->object_id );
		return apply_filters(
			'hubloy_membership_membership_rule_to_html',
			array(
				'rule_id'       => $this->rule_id,
				'memberships'   => $this->memberships,
				'date_created'  => $this->date_created,
				'date_updated'  => $this->date_updated,
				'status'        => $this->status,
				'status_name'   => ucfirst( $this->status ),
				'object_type'   => $this->object_type,
				'object_id'     => $this->object_id,
				'rule_name'     => apply_filters( 'hubloy_membership_rule_type_name', $this->object_type ),
				'title'         => $item_title,
				'time_limit'    => $this->time_limit,
				'time_duration' => $this->time_duration,
				'desc'          => sprintf( _n( '%1$s membership has access to %2$s', '%1$s memberships have access to %2$s', $membership_count, 'memberships-by-hubloy' ), number_format_i18n( $membership_count ), $item_title ),
			),
			$this
		);
	}


	/**
	 * Get rules by type
	 *
	 * @param string $type - the object type
	 * @param int    $id - the object id
	 *
	 * @since 1.0.0
	 *
	 * @return object|bool
	 */
	public static function get_rules( $type, $id ) {
		global $wpdb;
		$rule = Cache::get_cache( 'get_rule_' . $type . '_' . $id, 'rule' );
		if ( $rule && is_object( $rule ) ) {
			return $rule;
		}
		$table_name = Database::get_table_name( Database::MEMBERSHIP_RULES );
		$sql        = "SELECT `rule_id` FROM {$table_name} WHERE `object_type` = %s AND `object_id` = %d";
		$result     = $wpdb->get_row( $wpdb->prepare( $sql, $type, $id ) );
		if ( $result ) {
			$rule = new self( $result->rule_id );
			Cache::set_cache( 'get_rule_' . $type . '_' . $id, $rule, 'rule' );
			return $rule;
		}
		return false;
	}

	/**
	 * Get restricted items by type.
	 *
	 * @param string $type The rule type
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_restricted_items( $type ) {
		global $wpdb;
		$items = Cache::get_cache( 'get_restricted_' . $type, 'rule' );
		if ( is_array( $items ) ) {
			return $items;
		}
		$table_name = Database::get_table_name( Database::MEMBERSHIP_RULES );
		$sql        = "SELECT `object_id`, `memberships` FROM {$table_name} WHERE `object_type` = %s AND `status` = %s";
		$items      = $wpdb->get_results( $wpdb->prepare( $sql, $type, 'enabled' ) );
		Cache::set_cache( 'get_restricted_' . $type, $items, 'rule' );
		return $items;
	}
}
