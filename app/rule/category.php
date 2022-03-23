<?php
namespace Hammock\Rule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Rule;

class Category extends Rule {

	/**
	 * The taxonomy type
	 * 
	 * @since 1.0.0
	 * 
	 * @var string
	 */
	public $term = 'category';

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Category
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Main rule set up
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->id          = 'term';
		$this->name        = __( 'Categories', 'hammock' );
		$this->has_setting = true;
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
		return wp_count_terms( array( 'taxonomy' => $this->term ) );
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
		$args['offset']      = isset( $args['number'] ) ? ( int ) $args['number'] : 10;
		$args['hide_empty']  = false;
		$args['taxonomy']    = $this->term;
		$args['order']       = 'ASC';
		$args['orderby']     = 'name';
		return get_terms( $args );
	}

	/**
	 * Search items
	 * 
	 * @param string $param The search param.
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function search( $param ) {
		$results = array();
		$query   = new \WP_Term_Query(
			array(
				'taxonomy'  => $this->term,
				'number' 	=> 10,
				'orderby'	=> 'name',
				'search'	=> $param
			)
		);
		foreach ( $query->get_terms() as $term ) {
			if ( $this->has_rule( $term->term_id ) ) {
				continue;
			}
			$results[] = array(
				'id'   => $term->term_id,
				'text' => $term->name
			);
		}
		return $results;
	}

	/**
	 * Check if is a valid item
	 * 
	 * @param int $item_id The item id
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function is_valid_item( $item_id ) {
		$term = get_term( $item_id, $this->term );
		if ( ! $term ) {
			return false;
		}
		return true;
	}

	/**
	 * Get the protected items name
	 * 
	 * @param int $id The item id.
	 * @param bool $edit_link Set to true to return a clickable title admin edit link.
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_protected_item_name( $id, $edit_link = false ) {
		$term = get_term( $item_id, $this->term );
		if ( ! $term ) {
			return '';
		}
		$title = $term->name;
		if ( $edit_link ) {
			$link = get_edit_term_link( $id );
			return $this->make_clickable( $link, $title );
		}
		return $title;
	}

	/**
	 * Set initial protection for front-end.
	 *
	 * To be overridden by children classes.
	 *
	 * @since  1.0.0
	 */
	public function protect_content() {
		add_action( 'wp', array( $this, 'restrict_content' ) );

		add_filter( 'hammock_post_rule_manage_posts_clauses', array( $this, 'manage_term_clauses' ) );
		add_filter( 'get_terms_args', array( $this, 'manage_get_terms_args' ), 999, 2 );
		add_filter( 'terms_clauses', array( $this, 'handle_terms_clauses' ), 999 );
		do_action(
			'hammock_protect_term_content',
			$this
		);
	}


	/**
	 * Restrict content
	 *
	 * @since 1.0.0
	 */
	public function restrict_content() {
		global $post, $wp_query;

		$protection_level = $this->settings->get_general_setting( 'protection_level' );
		if ( 'hide_content' === $protection_level ) {
			// maybe display a restricted notice for a taxonomy term
			add_action( 'loop_start', array( $this, 'restrict_term_notice' ), 1 );
		} elseif ( 'redirect' === $protection_level ) {
			$term = $wp_query && ( $wp_query->is_tax() || $wp_query->is_category() || $wp_query->is_tag() ) ? get_queried_object() : null;

			if ( $term instanceof \WP_Term ) {
				$this->redirect_restricted_content( $term->term_id, 'taxonomy', $term->taxonomy );
			}
		}
	}

	/**
	 * Handle term clauses
	 *
	 * @param string $clause - the clause to change
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function manage_term_clauses( $clause = '' ) {
		global $wpdb;
		$restricted = $this->get_member_restricted_content_ids();
		if ( ! empty( $restricted ) ) {
			$place_holders = implode( ', ', array_fill( 0, count( $restricted ), '%d' ) );

			$subquery = $wpdb->prepare(
				"
					SELECT object_id FROM $wpdb->term_relationships
					LEFT JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
					LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
					WHERE  term_id IN ($place_holders)
				",
				$restricted
			);

			$clause = " AND $wpdb->posts.ID NOT IN ($subquery) ";
		}
		return $clause;
	}

	/**
	 * Exclude restricted content
	 *
	 * @param array        $args - the clauses
	 * @param string|array $taxonomies - the taxonomies
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function manage_get_terms_args( $args, $taxonomies ) {
		$restricted = $this->get_member_restricted_content_ids();
		if ( ! empty( $restricted ) ) {
			$args['exclude'] = array_unique( $restricted );
		}
		return $args;
	}

	/**
	 * Exclude restricted terms
	 *
	 * @param array $clauses - the clauses
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function handle_terms_clauses( $clauses ) {
		global $wpdb;
		$restricted = $this->get_member_restricted_content_ids();
		if ( ! empty( $restricted ) ) {
			$place_holders = implode( ', ', array_fill( 0, count( $restricted ), '%s' ) );
			$subquery      = $wpdb->prepare(
				"
				SELECT sub_t.term_id FROM $wpdb->terms AS sub_t
				INNER JOIN $wpdb->term_taxonomy AS sub_tt ON sub_t.term_id = sub_tt.term_id
				WHERE sub_tt.taxonomy IN ($place_holders)
			",
				$restricted
			);

			$clauses['where'] .= " AND t.term_id NOT IN ($subquery) ";
		}
		return $clauses;
	}

	/**
	 * Displays content restricted notices when browsing restricted terms archives.
	 *
	 * Applies when the restriction mode is "Hide content only":
	 *
	 * @param \WP_Query $wp_query WordPress query object, passed by reference
	 *
	 * @since 1.10.5
	 */
	public function restrict_term_notice( $wp_query ) {
		if ( $wp_query instanceof \WP_Query && $wp_query->is_archive() ) {
			$term = $restricted_term = $wp_query->get_queried_object();
			if ( $term instanceof \WP_Term ) {

				$message_code = '';
				$taxonomy     = $term->taxonomy;
				$terms        = array_merge( array( $term->term_id ), get_ancestors( $term->term_id, $taxonomy, 'taxonomy' ) );

				foreach ( $terms as $term_id ) {

					if ( hammock_is_term_protected( $term_id, $taxonomy ) ) {
						$message_code    = hammock_content_protected_message( $term_id, 'term', $taxonomy );
						$restricted_term = get_term( $term_id, $taxonomy );
					}
				}

				if ( '' !== $message_code ) {
					echo $message_code;
				}
			}
		}
	}

	/**
	 * Get content type
	 */
	protected function get_content_type( $item_id ) {
		global $wpdb;
		$sql    = "SELECT `taxonomy` FROM $wpdb->term_taxonomy  WHERE `term_id` = %d";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, $item_id ) );
		return $result;
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
		$settings  = $this->settings->get_addon_setting( 'category' );
		$protected = isset( $settings['protected'] ) ? $settings['protected'] : array();
		if ( in_array( $content_type, $protected ) ) {
			return true;
		}
		return false;
	}

}


