<?php
namespace Hammock\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Model\Settings;
use Hammock\Services\Members;
use Hammock\Services\Memberships;

/**
 * Protection rules
 *
 * @since 1.0.0
 */
class Rule {

	/**
	 * The rule id
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * The rule name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Check if the rule is enabled.
	 *
	 * @since 1.0.0
	 * 
	 * @var bool
	 */
	protected $enabled = false;


	/**
	 * Settings object
	 *
	 * @since 1.0.0
	 */
	protected $settings = null;

	/**
	 * The members service
	 *
	 * @since 1.0.0
	 */
	protected $members_service = null;

	/**
	 * The membership service
	 *
	 * @since 1.0.0
	 */
	protected $membership_service = null;

	/**
	 * Rule constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->settings           = new Settings();
		$this->members_service    = new Members();
		$this->membership_service = new Memberships();
		// Enabled defaults to content protection. Rules can define their own enabled state in init
		$this->enabled         = $this->settings->get_general_setting( 'content_protection' );
		$this->init();

		// Register the rule.
		add_filter( 'hammock_protection_rules', array( $this, 'register_rule' ) );

		if ( $this->enabled ) {
			add_filter( 'hammock_' . $this->id . '_content_has_access', array( $this, 'has_access' ), 10, 3 );
			if ( ! is_super_admin() && ! current_user_can( 'manage_options' ) ) {
				if ( is_admin() || is_network_admin() ) {
					$this->protect_admin_content();
				} else {
					$this->protect_content();
				}
			}
		}
	}

	/**
	 * Main rule init
	 * This function offers a safe way for each rule to initialize itself if
	 * required.
	 *
	 * This function is executed in Admin and Front-End, so it should only
	 * initialize stuff that is really needed!
	 *
	 * @since  1.0.0
	 */
	public function init() {

	}

	/**
	 * Register the rule.
	 *
	 * @param array $rules The current rules.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_rule( $rules ) {
		if ( ! isset( $rules[$this->id] ) ) {
			$rules[$this->id] = $this->name;
		}
		return $rules;
	}

	/**
	 * Set initial protection for front-end.
	 *
	 * To be overridden by child classes.
	 *
	 * @since  1.0.0
	 */
	public function protect_content() {
		do_action(
			'hammock_protect_content',
			$this
		);
	}


	/**
	 * Set initial protection for admin side.
	 *
	 * To be overridden by child classes.
	 *
	 * @since  1.0.0
	 */
	public function protect_admin_content() {
		do_action(
			'hammock_protect_admin_content',
			$this
		);
	}

	/**
	 * Get rule by id and type.
	 * 
	 * @param int    $id - the object id
	 * @param string $type - the object type
	 * 
	 * @return bool|object
	 */
	public function get_rule( $id, $type ) {
		return \Hammock\Model\Rule::get_rules( $type, $id );;
	}

	/**
	 * Update rule
	 * 
	 * @param int    $id - the object id
	 * @param string $type - the object type
	 * @param array  $memberships The memberships
	 * 
	 * @since 1.0.0
	 */
	public function update_rule( $id, $type, $memberships ) {
		$rule = $this->get_rule( $id, $type );
		if ( ! $rule ) {
			$rule->object_type = $type;
			$rule->object_id   = $id;
		}
		$rule->memberships = $memberships;
		$rule->save();
	}

	/**
	 * List memberships
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function list_memberships() {
		return $this->membership_service->list_simple_memberships( 0, false );
	}

	/**
	 * Count items to protect
	 * 
	 * @param array $args Optional arguments.
	 * 
	 * @since 1.0.0
	 * 
	 * @return int
	 */
	public function count_items( $args = array() ) {
		return 0;
	}

	/**
	 * list items to protect
	 * 
	 * @param array $args Optional arguments.
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function list_items( $args = array() ) {
		return array();
	}

	/**
	 * Get the view columns
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_view_columns() {
		return array();
	}

	/**
	 * Save rule
	 *
	 * @param array $memberships Array of memberships
	 * @param int $item_id the item id to apply the rules to
	 * 
	 * @since 1.0.0
	 */
	public function save_rule( $memberships, $item_id ) {

	}

	/**
	 * Verify access to the current content.
	 *
	 * @param bool   $access - The access
	 * @param object $object - the object
	 * @param string $content_type - the content type
	 *
	 * @since  1.0.0
	 *
	 * @return boolean TRUE if has access, FALSE otherwise.
	 */
	public function has_access( $access, $object, $content_type ) {
		if ( is_super_admin() || current_user_can( 'manage_options' ) ) {
			return true;
		}

		$access = apply_filters( 'hammock_guest_has_access', true, $object, $content_type );

		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$member  = $this->members_service->get_member_by_user_id( $user_id );
			if ( $member && $member->id > 0 ) {
				if ( $member->enabled ) {
					$access = apply_filters( 'hammock_enabled_member_has_access', true, $member, $object, $content_type );
				} else {
					$access = apply_filters( 'hammock_disabled_member_has_access', false, $member, $object, $content_type );
				}
			} else {
				$access = apply_filters( 'hammock_non_member_has_access', true, $user_id, $object, $content_type );
			}
		}

		return apply_filters(
			'hammock_rule_has_access',
			$access,
			$object,
			$content_type,
			$this
		);
	}

	/**
	 * Get member restricted content ids
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_member_restricted_content_ids() {
		if ( is_user_logged_in() ) {
			if ( ! is_super_admin() && ! current_user_can( 'manage_options' ) ) {
				$user_id = get_current_user_id();
				$member  = $this->members_service->get_member_by_user_id( $user_id );
				if ( $member && $member->id > 0 && $member->enabled ) {
					return $this->get_resticted_content_ids( true, $user_id, $member );
				} else {
					return $this->get_resticted_content_ids( true, $user_id, false );
				}
			}
		} else {
			return $this->get_resticted_content_ids( false, 0, false );
		}
		return array();
	}

	/**
	 * Get resitrcted content ids based on access
	 *
	 * @param bool   $logged_in - logged in status
	 * @param int    $user_id - current user id
	 * @param object $member - the active member
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_resticted_content_ids( $logged_in = false, $user_id = 0, $member = null ) {
		$restricted  = $this->get_all_restricted();
		$content_ids = array();
		foreach ( $restricted as $key => $item ) {
			// Check if there are any memberships
			if ( ! empty( $item['value'] ) ) {
				$content_type = $this->get_content_type( $item['id'] );
				if ( ! $this->does_content_have_protection( $item['id'], $content_type ) ) {
					unset( $restricted[ $key ] );
				} else {
					$content_ids[ $item['id'] ] = $item['id'];
				}
			} else {
				unset( $restricted[ $key ] );
			}
		}

		if ( $logged_in ) {
			if ( $member ) {
				$plans = $member->get_plans();
				foreach ( $restricted as $key => $item ) {
					$values = is_array( $item['value'] ) ? $item['value'] : array();
					foreach ( $plans as $plan_id ) {
						if ( hammock_is_member_plan_active( $plan_id ) ) {
							if ( in_array( $plan_id, $values ) ) {
								unset( $content_ids[ $item['id'] ] );
							}
						}
					}
				}
			}
		}
		return ! empty( $content_ids ) ? array_values( $content_ids ) : array();
	}

	/**
	 * Gets all restricted ids with their corresponding content ids
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_all_restricted() {
		global $wpdb;
		$output = array();
		$sql    = false;
		if ( $this->id === 'post' ) {
			$sql = "SELECT `post_id` as item_id, `meta_value` FROM {$wpdb->postmeta} WHERE `meta_key` = %s";
		} elseif ( $this->id === 'term' ) {
			$sql = "SELECT `term_id` as item_id, `meta_value` FROM {$wpdb->termmeta} WHERE `meta_key` = %s";
		}

		if ( $sql ) {
			$results = $wpdb->get_results( $wpdb->prepare( $sql, '_hammock_mebership_access' ) );
			foreach ( $results as $result ) {
				$value    = is_array( $result->meta_value ) ? array_map( 'maybe_unserialize', $result->meta_value ) : maybe_unserialize( $result->meta_value );
				$output[] = array(
					'id'    => $result->item_id,
					'value' => $value,
				);
			}
		}
		return $output;
	}

	/**
	 * Get term posts
	 * This checks the term posts for protection and applies the same rule
	 *
	 * @param int   $term_id - the term id
	 * @param array $value - the protection value
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_term_posts( $item_id, $value ) {
		global $wpdb;
		$output  = array();
		$sql     = "SELECT p.ID
			FROM $wpdb->posts AS p
			INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id)
			INNER JOIN $wpdb->term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
			INNER JOIN $wpdb->terms AS t ON (t.term_id = tt.term_id)
			WHERE   p.post_status = 'publish' AND t.term_id = %d ORDER BY p.post_date DESC";
		$results = $wpdb->get_results( $wpdb->prepare( $sql, $item_id ) );
		foreach ( $results as $result ) {
			$output[] = array(
				'id'    => $result->ID,
				'value' => $value, // Same rules
			);
		}

		return $output;
	}

	/**
	 * Get content type
	 */
	protected function get_content_type( $item_id ) {
		return '';
	}

	/**
	 * Check if content has protection
	 *
	 * @param int    $item_id - the item id
	 * @param string $content_type - the content type
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function does_content_have_protection( $item_id, $content_type ) {
		return false;
	}

	/**
	 * Redirects restricted content based on content/product restriction rules.
	 *
	 * @param int    $object_id - ID of the object redirecting from (post ID, term ID)
	 * @param string $object_type - type of object redirecting from (post_type, taxonomy)
	 * @param string $object_type_name - name of the type of object redirecting form (e.g. post, product, product_cat, category...)
	 *
	 * @since 1.0.0
	 */
	public function redirect_restricted_content( $object_id, $object_type, $object_type_name ) {

		// bail out early if no valid ID
		if ( (int) $object_id < 1 ) {
			return;
		}

		$restricted = false;
		$pages      = $this->settings->get_general_setting( 'pages' );
		$page_id    = isset( $pages['protected_content'] ) ? $pages['protected_content'] : false;

		if ( empty( $page_id ) ) {

			$restricted = false; // we don't have a page to redirect to (shouldn't happen)

		} elseif ( 'post' === $object_type ) {

			if ( (int) $object_id === $page_id ) {
				$restricted = false; // the restricted content page cannot be itself restricted
			} else {
				$restricted = hammock_is_post_protected();
			}
		} elseif ( 'taxonomy' === $object_type ) {

			$terms    = array_merge( array( $object_id ), get_ancestors( $object_id, $object_type_name, $object_type ) );
			$taxonomy = $object_type_name;

			foreach ( $terms as $term_id ) {

				$restricted = hammock_is_term_protected( $term_id, $taxonomy );

				if ( $restricted ) {

					$object_id = $term_id;
					break;
				}
			}
		}

		if ( $restricted ) {

			if ( 'post' === $object_type ) {

				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					return;
				}

				if ( is_tax() ) {
					return;
				}
			}

			wp_redirect( esc_url( get_permalink( $page_id ) ) );
			exit;
		}
	}
}

