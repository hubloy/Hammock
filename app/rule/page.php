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
		$this->id          = 'page';
		$this->name        = __( 'Pages', 'hammock' );
		$this->has_setting = true;

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
		return $this->count_post_type_items( $this->id, $args );
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
		return $this->list_post_type_items( $this->id, $args );
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
		return $this->search_post_type( $this->id, $param );
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
	 * Check if is a valid item
	 * 
	 * @param int $item_id The item id
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function is_valid_item( $item_id ) {
		$post = get_post( $item_id );
		if ( ! $post ) {
			return false;
		}
		return true;
	}

	/**
	 * Before a post is trashed, delete the cache.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|null
	 */
	public function pre_trash_post( $check, $post ) {
		if ( $this->id !== $post->post_type ) {
			return $check;
		}
		Cache::delete_cache( 'count_page', 'counts' );
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
		if ( $this->id !== $post->post_type ) {
			return;
		}
		Cache::delete_cache( 'count_page', 'counts' );
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
		$post = get_post( $id );
		if ( ! $post ) {
			return '';
		}
		$title = $post->post_title;
		if ( $edit_link ) {
			$link = get_edit_post_link( $id );
			return $this->make_clickable( $link, $title );
		}
		return $title;
	}
}
