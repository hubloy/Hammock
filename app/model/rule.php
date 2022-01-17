<?php
namespace Hammock\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Helper\Cache;

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
		$sql  = "SELECT `rule_id`, `memberships`, `object_type`, `object_id`, `time_limit`, `time_duration`, `date_created`, `date_updated` FROM {$this->table_name} WHERE `rule_id` = %d";
		$item = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );
		if ( $item ) {
			$date_format         = get_option( 'date_format' );
			$this->rule_id       = $id;
			$this->object_type   = $item->object_type;
			$this->object_id     = $item->object_id;
			$this->memberships   = is_array( $item->memberships ) ? array_map( 'maybe_unserialize', $item->memberships ) : maybe_unserialize( $item->custom_rule );
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
					'member_id'     => $this->member_id,
					'memberships'   => $memberships,
					'time_limit'    => $this->time_limit,
					'time_duration' => $this->time_duration,
					'due_date'      => ! empty( $this->due_date ) ? date_i18n( 'Y-m-d H:i:s', strtotime( $this->due_date ) ) : '',
				),
				array( 'rule_id' => $this->rule_id )
			);
		} else {
			$result = $wpdb->insert(
				$this->table_name,
				array(
					'object_type'   => $this->object_type,
					'object_id'     => $this->object_id,
					'member_id'     => $this->member_id,
					'memberships'   => $memberships,
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

		do_action( 'hammock_rule_before_delete_rule', $this->rule_id );

		$sql = "DELETE FROM {$this->table_name} WHERE `rule_id` = %d";
		$wpdb->query( $wpdb->prepare( $sql, $this->rule_id ) );

		do_action( 'hammock_rule_after_delete_rule', $this->rule_id );
		$this->rule_id = 0;
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
		$rule   = Cache::get_cache( 'get_rule_' . $type . '_' . $id, 'rule' );
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
}
