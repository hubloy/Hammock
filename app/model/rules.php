<?php
namespace Hammock\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;

/**
 * Membership rules
 * 
 * @since 1.0.0
 */
class Rules {

	/**
	 * The database ID
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $rule_id = 0;

	/**
	 * The membership id
	 * 
	 * @since 1.0.0
	 * 
	 * @var int
	 */
	public $membership_id = 0;

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
	 * The custom rules
	 * 
	 * @since 1.0.0
	 * 
	 * @var array
	 */
	public $custom_rule = array();

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

	}

	public function has_time_limit() {

	}

	public function get_time_limit() {
		
	}
}