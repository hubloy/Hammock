<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Model\Rule;
use Hammock\Helper\Cache;

/**
 * Rules service
 *
 * @since 1.0.0
 */
class Rules {

	/**
	 * The table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Main service constructor
	 *
	 * Sets up the service
	 */
	public function __construct() {
		$this->table_name = Database::get_table_name( Database::MEMBERSHIP_RULES );
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
	public function get_rules( $type, $id ) {
		global $wpdb;
		$sql    = "SELECT `id` FROM {$this->table_name} WHERE `object_type` = %s AND `object_id` = %d";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $type, $id ) );
		if ( $result ) {
			$rule = new Rule( $result->id );
			return $rule;
		}
		return false;
	}

	/**
	 * List rule types
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function list_rule_types() {
		return apply_filters( 'hammock_protection_rules', array() );
	}
}
