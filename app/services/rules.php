<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Model\Rule;
use Hammock\Helper\Cache;
use Hammock\Helper\Pagination;

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
	 * The protection service
	 * 
	 * @var object
	 * 
	 * @since 1.0.0
	 */
	private $protection_service = null;

	/**
	 * Membership rule status type constants.
	 * Enabled rule status
	 *
	 * @since  1.0.0
	 */
	const STATUS_ENABLED = 'enabled';

	/**
	 * Membership rule status type constants.
	 * Disabled rule status
	 *
	 * @since  1.0.0
	 */
	const STATUS_DISABLED = 'disabled';


	/**
	 * Main service constructor
	 *
	 * Sets up the service
	 */
	public function __construct() {
		$this->table_name = Database::get_table_name( Database::MEMBERSHIP_RULES );
		$this->protection_service = Protection::instance( false );
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
		return Rule::get_rules( $type, $id );
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

	/**
	 * Get rule type data. This gets data specific to the rules to show in the admin.
	 *
	 * @param $args The rule data. This expects
	 * 		'type' The rule type
	 * 		'data' The rule data. Either list or count
	 * 		'offset' Optional page offset
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
  	 */
	public function get_rule_type_data( $args ) {
		$defaults = array(
			'type'   => '',
			'paged'  => 0,
			'number' => 10,
		);
		$args   = wp_parse_args( $args, $defaults );
		$types  = $this->list_rule_types();
		$type   = $args['type'];
		$type   = strtolower( $type );
		if ( ( 'all' !== $type ) && ! isset( $types[ $type ] ) ) {
			return array(
				'success' => false
			);
		}
		$offset = ( int ) $args['paged'];


		$per_page     = $args['number'];
		$total        = $this->count_rules( $type );
		$pages        = Pagination::generate_pages( $total, $per_page, $offset );
		$pager        = array(
			'total'   => $total,
			'pages'   => $pages,
			'current' => $offset,
		);
		$items       = $this->list_rules( array(
			'paged'    => $offset,
			'per_page' => $per_page,
			'type'     => $type,
		));
		return array(
			'pager'   => $pager,
			'items'   => $items,
			'success' => true,
		);
	}

	/**
	 * Count rules
	 *
	 * @param string $type The rule type.
	 * 
	 * @since 1.0.0
	 * 
	 * @return int
	 */
	public function count_rules( $type ) {
		global $wpdb;
		$where = '';
		if ( 'all' !== $type ) {
			$where = $wpdb->prepare( "WHERE `object_type` = %s", $type );
		}
		$count = Cache::get_cache( 'rules_' . $type, 'counts' );
		if ( false !== $count ) {
			return $count;
		}
		$query = "SELECT COUNT( * ) FROM {$this->table_name} $where";
		$count = $wpdb->get_var( $query );
		Cache::set_cache( 'rules_' . $type , $count, 'counts' );
		return $count;
	}

	/**
	 * List rules by type
	 * 
	 * @param string $type The rule type
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function list_rules( $args ) {
		global $wpdb;
		$type 	  = $args['type'];
		$page 	  = $args['paged'];
		$per_page = $args['per_page'];
		$where    = '';
		if ( 'all' !== $type ) {
			$where = $wpdb->prepare( "WHERE `object_type` = %s", $type );
		}
		$lists = Cache::get_cache( 'rules_' . $type, 'list' );
		if ( false !== $lists ) {
			return $lists;
		}
		$lists   = array();
		$query   = "SELECT `rule_id` FROM {$this->table_name} $where LIMIT %d, %d";
		$results = $wpdb->get_results( $wpdb->prepare( $query, $page, $per_page ) );
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$rule    = new Rule( $result->rule_id );
				$lists[] = $rule->to_html();
			}
		}
		Cache::set_cache( 'rules_' . $type , $lists, 'list' );
		return $lists;
	}

	/**
	 * Save a rule
	 * 
	 * @param $args The rule data. This expects
	 * 		'type' The rule type
	 * 		'id' The rule componenet id
	 * 		'memberships' List array of membership ids to assign
	 * 
	 * @since 1.0.0
	 * 
	 * @return WP_Error|array Return an error if rule not found. Return an array if saved.
	 */
	public function save_rule( $args ) {
		$defaults = array(
			'type'        => '',
			'id'          => 0,
			'status'      => self::STATUS_ENABLED,
			'memberships' => array(),
		);
		$args   = wp_parse_args( $args, $defaults );
		$rule   = $this->get_rule_by_type( $args['type'] );
		if ( ! $rule ) {
			return new \WP_Error( 'not_found', __( 'Rule type not found', 'hammock' ) );
		}

		$rule->save_rule( $args['memberships'], $args['id'], $args['status'] );
		return array(
			'message' => __( 'Rule saved', 'hammock' )
		);
	}

	/**
	 * Get rule by type.
	 * 
	 * @param string $type The rule type id
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool|object
	 */
	private function get_rule_by_type( $type ) {
		$types  = $this->list_rule_types();
		$type   = strtolower( $type );
		if ( ! isset( $types[$type] ) ) {
			return false;
		}
		$rule = false;
		switch ( $type ) {
			case 'post':
				$rule = $this->protection_service->post_rule;
				break;
			case 'page':
				$rule = $this->protection_service->page_rule;
				break;
			case 'menu':
				$rule = $this->protection_service->menu_rule;
				break;
			case 'media':
				$rule = $this->protection_service->media_rule;
				break;
			case 'content':
				$rule = $this->protection_service->content_rule;
				break;
			case 'term':
				$rule = $this->protection_service->category_rule;
				break;
			case 'custom_items':
				$rule = $this->protection_service->custom_items_rule;
				break;
			case 'custom_types':
				$rule = $this->protection_service->custom_types_rule;
				break;
		}
		return $rule;
	}
}
