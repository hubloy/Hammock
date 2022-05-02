<?php
namespace HubloyMembership\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Core\Database;

/**
 * Usage model
 * Handles usages of different models
 *
 * @since 1.0.0
 */
class Usage {

	/**
	 * The usage id.
	 * 
	 * @since 1.1.0
	 * 
	 * @var int
	 */
    public $id = 0;

	/**
	 * The object id.
	 * 
	 * @since 1.1.0
	 * 
	 * @var int
	 */
    public $object_id = 0;

	/**
	 * The reference object type.
	 * 
	 * @since 1.1.0
	 * 
	 * @var string
	 */
    public $object_type = '';

	/**
	 * The item reference. This can be an id or string.
	 * 
	 * @since 1.1.0
	 * 
	 * @var string
	 */
    public $item_ref = '';

	/**
	 * The total usage.
	 * 
	 * @since 1.1.0
	 * 
	 * @var int
	 */
    public $total_usage = 0;
    
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
	public function __construct() {
		$this->table_name = Database::get_table_name( Database::USAGE );
	}

	/**
	 * Get one record from the database
	 *
	 * @param int    $object_id   The current object id
	 * @param string $object_type The object type
	 * @param string $item_ref    The item reference
	 *
	 * @since 1.1.0
	 *
	 * @return bool|array
	 */
	public function get_one( $object_id, $object_type, $item_ref ) {
		global $wpdb;
		$sql    = "SELECT `id`, `object_id`, `object_type`, `item_ref`, `total_usage` FROM {$this->table_name} WHERE `object_id` = %d AND `object_type` = %s AND `item_ref` = %s";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $object_id, $object_type, $item_ref ) );
		if ( $result ) {
			$this->id          = $result->id;
			$this->object_id   = $result->object_id;
			$this->object_type = $result->object_type;
			$this->item_ref    = $result->item_ref;
			$this->total_usage = $result->total_usage;
		}
	}

	/**
	 * Get usage
	 * 
	 * @since 1.1.0
	 * 
	 * @return int
	 */
	public function get_usage() {
		return $this->total_usage;
	}

	/**
	 * Increment the usage.
	 * 
	 * @since 1.1.0
	 */
	public function register_usage() {
		$this->total_usage++;
	}

	/**
	 * Save or update usage.
	 * If the usage exists, we will only save the usage counts.
	 * 
	 * @since 1.1.0
	 */
	public function save() {
		if ( $this->id > 0 ) {
			$wpdb->update(
				$this->table_name,
				array(
					'total_usage' => $this->total_usage,
				),
				array( 'id' => $this->id )
			);
		} else {
			$result = $wpdb->insert(
				$this->table_name,
				array(
					'total_usage'  => $this->total_usage,
					'object_id'    => $this->object_id,
					'object_type'  => $this->object_type,
					'item_ref'     => $this->item_ref,
					'date_created' => date_i18n( 'Y-m-d H:i:s' ),
				)
			);

			if ( $result ) {
				$this->id = (int) $wpdb->insert_id;
			}
		}

		/**
		 * Action called after a usage is saved.
		 * 
		 * @param object $usage The current usage.
		 * 
		 * @since 1.1.0
		 */
		do_action( 'hubloy_membership_after_save_usage', $this );
	}
}
