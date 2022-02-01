<?php
namespace Hammock\Rule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Rule;
use Hammock\Helper\Cache;
use Hammock\View\Backend\Rules\Access;

class Page extends Rule {

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
	 * @return Post
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
		$this->id   = 'page';
		$this->name = __( 'Pages', 'hammock' );

		add_filter( 'pre_trash_post', array( $this, 'pre_trash_post' ), 10, 2 );
		add_filter( 'pre_delete_post', array( $this, 'pre_delete_post' ), 10, 3 );

		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
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
		global $wpdb;
		$count = Cache::get_cache( 'count_pages', 'counts' );
		if ( false !== $count ) {
			return $count;
		}
		$query = "SELECT COUNT( * ) FROM {$wpdb->posts} WHERE post_type = %s";
		$count = $wpdb->get_var( $wpdb->prepare( $query, 'page' ) );
		Cache::set_cache( 'count_pages', $count, 'counts' );
		return $count;
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
		$args['posts_per_page']      = isset( $args['number'] ) ? ( int ) $args['number'] : 10;
		$args['ignore_sticky_posts'] = true;
		$args['public']              = true;
		$args['post_status']         = 'publish';
		$args['post_type']           = 'page';
		$args['order']               = 'ASC';
		$args['orderby']             = 'title';
		$query                       = new \WP_Query( $args );
		$data                        = array();
		if ( ! $query->have_posts() ) {
			return array();
		}
		$memberships = $this->list_memberships();
		foreach ( $query->posts as $post ) {
			$access         = new Access();
			$rule           = $this->get_rule( $post->ID, 'page' );
			$edit_link      = get_edit_post_link( $post->ID );
			$view_link      = get_permalink( $post->ID );
			$access->data   = array(
				'rule'        => $rule,
				'item'        => $this->id,
				'id'          => $post->ID,
				'memberships' => $memberships,
			);
			$content        = array(
				'id'        => $post->ID,
				'type'      => $post->post_type,
				'title'     => $post->post_title,
				'edit_link' => $edit_link,
				'edit_html' => sprintf( __( '%sEdit%s', 'hammock' ), '<a href="' . $edit_link . '" target="_blank">', '</a>' ),
				'view_link' => $view_link,
				'view_html' => sprintf( __( '%sView%s', 'hammock' ), '<a href="' . $view_link . '" target="_blank">', '</a>' ),
				'access'    => $access->render( true ),
			);
			$data[ $post->ID ] = $content;
		}
		wp_reset_postdata();
		return $data;
	}


	/**
	 * Get the view columns
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_view_columns() {
		return array(
			'id'        => __( 'ID', 'hammock' ),
			'title'     => __( 'Title', 'hammock' ),
			'access'    => __( 'Who has access', 'hammock' ),
			'edit_html' => __( 'Edit', 'hammock' ),
			'view_html' => __( 'View', 'hammock' ),
		);
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
		$post = get_post( $item_id );
		if ( ! $post ) {
			return;
		}
		$this->update_rule( $item_id, 'page', $memberships );
	}

	/**
	 * Before a post is trashed, delete the cache.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|null
	 */
	public function pre_trash_post( $check, $post ) {
		if ( 'post' !== $post->post_type ) {
			return $check;
		}
		Cache::delete_cache( 'count_pages', 'counts' );
		return $check;
	}

	/**
	 * Before a post is deleted.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|null
	 */
	public function pre_delete_post( $check, $post, $force ) {
		$this->pre_trash_post( $check, $post );
		return $check;
	}

	/**
	 * Clear cache when a post is saved.
	 *
	 * @since 1.0.0
	 */
	public function save_post( $post_id, $post, $update ) {
		if ( 'page' !== $post->post_type ) {
			return;
		}
		Cache::delete_cache( 'count_pages', 'counts' );
	}
}
