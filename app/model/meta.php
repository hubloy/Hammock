<?php
namespace Hammock\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;

/**
 * Meta database model
 *
 * @since 1.0.0
 */
class Meta {


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
		$this->table_name = Database::get_table_name( Database::META );
	}

	/**
	 * Get one record from the database
	 *
	 * @param int    $object_id - the current object id
	 * @param string $type - the meta type
	 * @param string $key - the meta key
	 *
	 * @since 1.0.0
	 *
	 * @return bool|array
	 */
	public function get_one( $object_id, $type, $key ) {
		global $wpdb;
		$sql    = "SELECT `meta_id`, `object_id`, `meta_type`, `meta_key`, `meta_value`, `date_created`, `date_updated` FROM {$this->table_name} WHERE `object_id` = %d AND `meta_type` = %s AND `meta_key` = %s";
		$result = $wpdb->get_row( $wpdb->prepare( $sql, $object_id, $type, $key ) );
		if ( $result ) {
			$date_format = get_option( 'date_format' );
			return array(
				'meta_id'      => $result->meta_id,
				'object_id'    => $result->object_id,
				'meta_type'    => $result->meta_type,
				'meta_key'     => $result->meta_key,
				'meta_value'   => is_array( $result->meta_value ) ? array_map( 'maybe_unserialize', $result->meta_value ) : maybe_unserialize( $result->meta_value ),
				'date_created' => date_i18n( $date_format, strtotime( $result->date_created ) ),
				'date_updated' => ! empty( $result->date_updated ) ? date_i18n( $date_format, strtotime( $result->date_updated ) ) : '',
			);
		}
		return false;
	}

	/**
	 * Save Meta
	 *
	 * @param int    $object_id - the current object id
	 * @param string $type - the meta type
	 * @param string $key - the meta key
	 * @param object $value - the meta value
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool
	 */
	public function save( $object_id, $type, $key, $value ) {
		global $wpdb;

		$key   = wp_unslash( $key );
		$value = wp_unslash( $value );
		$value = maybe_serialize( $value );

		$result = $wpdb->insert(
			$this->table_name,
			array(
				'object_id'    => $object_id,
				'meta_type'    => $type,
				'meta_key'     => $key,
				'meta_value'   => $value,
				'date_created' => date_i18n( 'Y-m-d H:i:s' ),
			)
		);

		if ( ! $result ) {
			return false;
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Update meta
	 * This checks the database table if the meta exists and performs and update
	 * If it does not exist, an insert will be done
	 *
	 * @param int    $object_id - the current object id
	 * @param string $type - the meta type
	 * @param string $key - the meta key
	 * @param object $value - the meta value
	 *
	 * @since 1.0.0
	 */
	public function update( $object_id, $type, $key, $value ) {
		global $wpdb;

		$sql     = "SELECT `meta_id` FROM {$this->table_name} WHERE meta_type = %s AND `object_id` = %d AND `meta_key` = %s";
		$meta_id = $wpdb->get_var( $wpdb->prepare( $sql, $type, $object_id, $key ) );

		if ( $meta_id ) {
			$value = wp_unslash( $value );
			$value = maybe_serialize( $value );
			$wpdb->update(
				$this->table_name,
				array(
					'meta_value'   => $value,
					'date_updated' => date_i18n( 'Y-m-d H:i:s' ),
				),
				array( 'meta_id' => $meta_id )
			);
		} else {
			$this->save( $object_id, $type, $key, $value );
		}
	}

	/**
	 * Delete meta
	 *
	 * @param int    $object_id - the current object id
	 * @param string $type - the meta type
	 * @param string $key - the meta key
	 *
	 * @since 1.0.0
	 */
	public function delete( $object_id, $type, $key ) {
		global $wpdb;
		$sql = "DELETE FROM {$this->table_name} WHERE meta_type = %s AND `object_id` = %d AND `meta_key` = %s";
		$wpdb->query( $wpdb->prepare( $sql, $type, $object_id, $key ) );
	}

	/**
	 * Get all meta by object id and object type
	 *
	 * @param int    $object_id - the current object id
	 * @param string $type - the meta type
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_all( $object_id, $type ) {
		global $wpdb;
		$meta_data  = array();
		$table_name = Database::get_table_name( Database::META );
		$sql        = "SELECT `meta_id`, `object_id`, `meta_type`, `meta_key`, `meta_value`, `date_created`, `date_updated` FROM {$table_name} WHERE `object_id` = %d AND `meta_type` = %s";
		$results    = $wpdb->get_results( $wpdb->prepare( $sql, $object_id, $type ) );
		foreach ( $results as $result ) {
			$date_format                    = get_option( 'date_format' );
			$meta_data[ $result->meta_key ] = array(
				'meta_id'      => $result->meta_id,
				'object_id'    => $result->object_id,
				'meta_type'    => $result->meta_type,
				'meta_key'     => $result->meta_key,
				'meta_value'   => is_array( $result->meta_value ) ? array_map( 'maybe_unserialize', $result->meta_value ) : maybe_unserialize( $result->meta_value ),
				'date_created' => date_i18n( $date_format, strtotime( $result->date_created ) ),
				'date_updated' => ! empty( $result->date_updated ) ? date_i18n( $date_format, strtotime( $result->date_updated ) ) : '',
			);
		}
		return $meta_data;
	}

	/**
	 * Remove all object meta
	 *
	 * @param int    $object_id - the current object id
	 * @param string $type - the meta type
	 *
	 * @since 1.0.0
	 */
	public static function remove_all( $object_id, $type ) {
		global $wpdb;
		$table_name = Database::get_table_name( Database::META );
		$sql        = "DELETE FROM {$table_name} WHERE `object_id` = %d AND `meta_type` = %s";
		$wpdb->query( $wpdb->prepare( $sql, $object_id, $type ) );
	}
}

