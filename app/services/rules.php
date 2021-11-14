<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Model\Rule;

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
	 * @return array
	 */
	public function get_rules( $type, $id ) {

	}
}
