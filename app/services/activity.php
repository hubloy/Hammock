<?php
namespace HubloyMembership\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Core\Database;

/**
 * Activity service
 *
 * @since 1.0.0
 */
class Activity {

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
		$this->table_name = Database::get_table_name( Database::ACTIVITY );
	}

	/**
	 * Save activity
	 *
	 * @param array $args - the array values of the arguments
	 *
	 * @since 1.0.0
	 */
	public function save( $args ) {
		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'ref_id'      => 0,
				'ref_type'    => '',
				'action'      => '',
				'object_type' => '',
				'object_name' => '',
				'object_id'   => '',
			)
		);

		$user = get_user_by( 'id', get_current_user_id() );
		if ( $user ) {
			$args['user_caps'] = strtolower( key( $user->caps ) );
			if ( empty( $args['user_id'] ) ) {
				$args['user_id'] = $user->ID;
			}
		} else {
			$args['user_caps'] = 'guest';
			if ( empty( $args['user_id'] ) ) {
				$args['user_id'] = 0;
			}
		}

		$wpdb->insert(
			$this->table_name,
			array(
				'ref_id'       => $args['ref_id'],
				'ref_type'     => $args['ref_type'],
				'action'       => $args['action'],
				'object_type'  => $args['object_type'],
				'object_name'  => $args['object_name'],
				'object_id'    => $args['object_id'],
				'user_id'      => $args['user_id'],
				'caps'         => $args['user_caps'],
				'date_created' => date_i18n( 'Y-m-d H:i:s' ),
			)
		);
	}

	/**
	 * Log Member
	 *
	 * @param int    $member_id - the member id
	 * @param string $action - the action
	 * @param string $object_type - the object type
	 * @param string $object_name - the object name
	 * @param int    $object_id - the object id
	 *
	 * @since 1.0.0
	 */
	public function log_member( $member_id, $action, $object_type, $object_name, $object_id ) {
		$this->save(
			array(
				'ref_id'      => $member_id,
				'ref_type'    => 'member',
				'action'      => $action,
				'object_type' => $object_type,
				'object_name' => $object_name,
				'object_id'   => $object_id,
			)
		);
	}

	/**
	 * Count activities
	 *
	 * @param int    $ref_id - the reference id
	 * @param string $ref_type - the reference type
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function count_activities( $ref_id, $ref_type ) {
		global $wpdb;
		$sql   = "SELECT count(`log_id`) FROM {$this->table_name} WHERE `ref_id` = %d AND `ref_type` = %s";
		$total = $wpdb->get_var( $wpdb->prepare( $sql, $ref_id, $ref_type ) );
		return $total;
	}

	/**
	 * List activities
	 *
	 * @param int    $ref_id - the reference id
	 * @param string $ref_type - the reference type
	 * @param int    $per_page - items per page
	 * @param int    $page - current page
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_activities( $ref_id, $ref_type, $per_page, $page = 0 ) {
		global $wpdb;
		$page       = $per_page * $page;
		$sql        = "SELECT * FROM {$this->table_name} WHERE `ref_id` = %d AND `ref_type` = %s ORDER BY `log_id` DESC LIMIT %d, %d";
		$results    = $wpdb->get_results( $wpdb->prepare( $sql, $ref_id, $ref_type, $page, $per_page ) );
		$activities = array();
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$activities[] = \HubloyMembership\Model\Activity::to_html( $result );
			}
		}
		return $activities;
	}

	/**
	 * Delete activities
	 *
	 * @param int    $ref_id - the reference id
	 * @param string $ref_type - the reference type
	 *
	 * @since 1.0.0
	 */
	public function delete_activities( $ref_id, $ref_type ) {
		global $wpdb;
		$sql = "DELETE FROM {$this->table_name} WHERE `ref_id` = %d AND `ref_type` = %s";
		$wpdb->query( $wpdb->prepare( $sql, $ref_id, $ref_type ) );
	}
}


