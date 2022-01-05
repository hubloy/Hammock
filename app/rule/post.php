<?php
namespace Hammock\Rule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Rule;
use Hammock\Helper\Cache;

class Post extends Rule {


	/**
	 * List of ids of restricted content
	 *
	 * @since 1.0.0
	 *
	 * @var int[]
	 */
	private $content_restricted = array();

	/**
	 * List of ids of restricted posted content
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $restricted_post_content = array();

	/**
	 * List of restricted sticky post ids
	 *
	 * @since 1.0.0
	 *
	 * @var int[]
	 */
	private $restricted_sticky_posts = array();

	/**
	 * List of restricted comments by post
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $restricted_comments_by_post_id = array();

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
		$this->id   = 'post';
		$this->name = __( 'Posts', 'hammock' );

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
		$count = Cache::get_cache( 'count_posts', 'counts' );
		if ( false !== $count ) {
			return $count;
		}
		$query = "SELECT COUNT( * ) FROM {$wpdb->posts} WHERE post_type = %s";
		$count = $wpdb->get_var( $wpdb->prepare( $query, 'post' ) );
		Cache::set_cache( 'count_posts', $count, 'counts' );
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
		$offset                      = absint( $args['offset'] );
		$limit                       = $offset + absint( $args['number'] );
		$args['posts_per_page']      = $args['number'];
		$args['ignore_sticky_posts'] = true;
		$args['public']              = true;
		$args['post_status']         = 'publish';
		$posts                       = get_posts( $args );
		if ( ! $posts ) {
			return array();
		}
		$data = array();
		foreach ( $posts as $post ) {
			$rule           = $this->get_rule( $post->ID, 'post' );
			$content        = array(
				'id'        => $post->ID,
				'type'      => $post->post_type,
				'title'     => $post->post_title,
				'edit_link' => get_edit_post_link( $post->ID ),
				'view_link' => get_permalink( $post->ID ),
				'rule'      => $rule,
			);
			$data[ $post->ID ] = $content;
		}
		return $data;
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
		Cache::delete_cache( 'count_posts', 'counts' );
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
		if ( 'post' !== $post->post_type ) {
			return;
		}
		Cache::delete_cache( 'count_posts', 'counts' );
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

		// user resitrictions
		add_filter( 'posts_clauses', array( $this, 'manage_posts_clauses' ), 999, 2 );

		add_filter( 'pre_get_posts', array( $this, 'exclude_restricted_posts' ), 999 );
		add_filter( 'option_sticky_posts', array( $this, 'exclude_restricted_sticky_posts' ), 999 );
		add_filter( 'get_pages', array( $this, 'exclude_restricted_pages' ), 999 );

		// handle comment queries to hide comments to comment that is restricted to non-members
		add_filter( 'the_posts', array( $this, 'exclude_restricted_content_comments' ), 999, 2 );
		add_filter( 'pre_get_comments', array( $this, 'exclude_restricted_comments' ), 999 );

		// handle single post previous/next pagination links
		add_filter( 'get_previous_post_where', array( $this, 'exclude_restricted_adjacent_posts' ), 1, 5 );
		add_filter( 'get_next_post_where', array( $this, 'exclude_restricted_adjacent_posts' ), 1, 5 );

		do_action(
			'hammock_protect_post_content',
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
		if ( 'hide' !== $protection_level ) {
			// restrict the post by filtering the post object and replacing the content with a message and maybe excerpt
			add_action( 'the_post', array( $this, 'restrict_post' ), 0 );

			// ensure the restricted post content data is persisted even when third parties try to filter it
			add_filter( 'the_content', array( $this, 'restricted_content_filtering' ), 999 );
		}

		if ( 'hide_content' === $protection_level ) {

			// ensure that RSS enclosures are restricted to avoid leaking of restricted embeds, etc.
			add_filter( 'rss_enclosure', array( $this, 'rss_enclosure_content' ), 999 );

			// restrict content comments
			$this->hide_restricted_content_comments();
		} elseif ( 'redirect' === $protection_level ) {
			if ( $post instanceof \WP_Post ) {
				$this->redirect_restricted_content( $post->ID, 'post', $post->post_type );
			}
		}
	}

	/**
	 * Exclude restricted content
	 *
	 * @param array    $clauses - the clauses
	 * @param WP_Query $wp_query - the query
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function manage_posts_clauses( $clauses, $wp_query ) {
		global $wpdb;
		$protection_level = $this->settings->get_general_setting( 'protection_level' );
		if ( 'hide' !== $protection_level ) {
			return $clauses;
		}
		$restricted = $this->get_member_restricted_content_ids();
		if ( ! empty( $restricted ) ) {
			$place_holders     = implode( ', ', array_fill( 0, count( $restricted ), '%d' ) );
			$clauses['where'] .= $wpdb->prepare( " AND $wpdb->posts.ID NOT IN ($place_holders) ", $restricted );
		}
		$clauses['where'] .= apply_filters( 'hammock_post_rule_manage_posts_clauses', '' );
		return $clauses;
	}


	/**
	 * Exclude restricted posts
	 */
	public function exclude_restricted_posts( $wp_query ) {
		$protection_level = $this->settings->get_general_setting( 'protection_level' );
		if ( 'hide' === $protection_level ) {
			$restricted = $this->get_member_restricted_content_ids();
			if ( ! empty( $restricted ) ) {
				$wp_query->set(
					'post__not_in',
					array_unique(
						array_merge(
							$wp_query->get( 'post__not_in' ),
							reset( $restricted )
						)
					)
				);
			}
		}
	}


	/**
	 * Removes sticky posts from ever showing up when using the "hide completely" restriction mode and the user doesn't have access.
	 *
	 * @param array $sticky_posts - array of sticky post IDs
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function exclude_restricted_sticky_posts( $sticky_posts ) {
		global $wp_query;

		if ( ! empty( $sticky_posts ) ) {
			$protection_level = $this->settings->get_general_setting( 'protection_level' );
			if ( ! empty( $this->restricted_sticky_posts ) && is_array( $this->restricted_sticky_posts ) ) {

				$sticky_posts = $this->restricted_sticky_posts;

			} elseif ( 'hide' === $protection_level ) {
				$restricted = $this->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					// Prevent infinite loops
					remove_filter( 'option_sticky_posts', array( $this, 'exclude_restricted_sticky_posts' ), 999 );

					$sticky_posts = array_diff( $sticky_posts, $restricted );

					add_filter( 'option_sticky_posts', array( $this, 'exclude_restricted_sticky_posts' ), 999 );

					$this->restricted_sticky_posts = $sticky_posts;
				}
			}
		}
		return $sticky_posts;
	}

	/**
	 * Excludes restricted pages from `get_pages()` calls.
	 *
	 * @param array $pages indexed array of page objects
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function exclude_restricted_pages( $pages ) {
		global $wp_query;
		$protection_level = $this->settings->get_general_setting( 'protection_level' );
		if ( 'hide' === $protection_level ) {

			foreach ( $pages as $index => $page ) {
				$restricted = hammock_is_post_protected( $page );
				if ( $restricted ) {
					unset( $pages[ $index ] );
				}
			}

			$pages = array_values( $pages );
		}

		return $pages;
	}

	/**
	 * Excludes restricted comments from comment feed.
	 *
	 * @param array    $posts - array of posts
	 * @param WP_Query $query - the
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function exclude_restricted_content_comments( $posts, $query ) {

		if ( ! empty( $query->comment_count ) && is_comment_feed() ) {

			foreach ( $query->comments as $key => $comment ) {

				$post_id = (int) $comment->comment_post_ID;

				$restricted = hammock_is_post_protected( $post_id );

				// if not, exclude this comment from the feed
				if ( $restricted ) {
					unset( $query->comments[ $key ] );
				}
			}

			// re-index and re-count comments
			$query->comments      = array_values( $query->comments );
			$query->comment_count = count( $query->comments );
		}

		return $posts;
	}

	/**
	 * Filters the comment query to exclude posts the user doesn't have access to.
	 *
	 * @param WP_Comment_Query $comment_query - the comment query
	 *
	 * @since 1.0.0
	 */
	public function exclude_restricted_comments( $comment_query ) {
		global $post;

		/**
		 * Filters the restrictable comment types.
		 *
		 * @param array $restrictable_comment_types array of comment types
		 *
		 * @since 1.0.0
		 */
		$restrictable_comment_types = apply_filters( 'hammock_post_rule_exclude_restricted_comments_types', array( '', 'trackback', 'pingback', 'review', 'contribution_comment' ) );

		if ( isset( $comment_query->query_vars['type'] ) && in_array( $comment_query->query_vars['type'], $restrictable_comment_types, true ) ) {

			$the_post_id = ! empty( $comment_query->query_vars['post_id'] ) && is_numeric( $comment_query->query_vars['post_id'] ) ? (int) $comment_query->query_vars['post_id'] : 0;
			$the_post_id = 0 === $the_post_id && ! empty( $comment_query->query_vars['parent__in'] ) && is_array( $comment_query->query_vars['parent__in'] ) && 1 === count( $comment_query->query_vars['parent__in'] ) ? current( $comment_query->query_vars['parent__in'] ) : $the_post_id;

			$restricted = hammock_is_post_protected( $post );

			if ( $restricted ) {

				$user_id = get_current_user_id();

				if ( isset( $this->restricted_comments_by_post_id[ $user_id ] ) ) {

					$post__not_in = $this->restricted_comments_by_post_id[ $user_id ];

				} else {

					$restrictions         = $this->get_member_restricted_content_ids();
					$post__not_in         = isset( $comment_query->query_vars['post__not_in'] ) && is_array( $comment_query->query_vars['post__not_in'] ) ? array_filter( $comment_query->query_vars['post__not_in'] ) : array();
					$original_post_not_in = $post__not_in; // used later to make sure posts marked for exclusion are not removed from this array

					// exclude restricted posts from the query
					if ( ! empty( $restrictions ) ) {
						$post__not_in = array_merge( $restrictions, (array) $post__not_in );
					}

					// handles exclusions
					if ( ! empty( $post__not_in ) ) {
						foreach ( $post__not_in as $i => $post_id ) {
							if ( ! in_array( $post_id, $original_post_not_in, false ) ) {
								unset( $post__not_in[ $i ] );
							}
						}
					}

					$post__not_in = array_unique( $post__not_in );

					$this->restricted_comments_by_post_id[ $user_id ] = $post__not_in;
				}

				if ( ! empty( $post__not_in ) ) {
					$comment_query->query_vars['post__not_in'] = $post__not_in;
				}
			}
		}
	}

	/**
	 * Handles restricted posts in queries for adjacent (previous/next) posts.
	 *
	 * These queries are normally used in building prev/next post links in single post views.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string   $where_clause `WHERE` clause in the SQL
	 * @param bool     $in_same_term whether post should be in a same taxonomy term (optional)
	 * @param int[]    $excluded_terms array of excluded term IDs (optional)
	 * @param string   $taxonomy taxonomy name used to identify the term used when `$in_same_term` is true (optional)
	 * @param \WP_Post $post related post object the adjacent posts are retrieved for
	 * @return string updated `WHERE` clause
	 */
	public function exclude_restricted_adjacent_posts( $where_clause, $in_same_term, $excluded_terms, $taxonomy, $post ) {
		$protection_level = $this->settings->get_general_setting( 'protection_level' );
		if ( '' !== $where_clause && $post instanceof \WP_Post && 'hide' === $protection_level ) {
			$restricted_post_ids = $this->get_member_restricted_content_ids();
			$restricted_post_ids = ! empty( $restricted_post_ids ) ? implode( ',', array_filter( array_map( 'absint', $restricted_post_ids ) ) ) : null;

			if ( ! empty( $restricted_post_ids ) ) {
				$where_clause .= " AND p.ID NOT IN ({$restricted_post_ids}) ";
			}
		}

		return $where_clause;
	}

	/**
	 * Restricts a post based on content restriction rules.
	 *
	 * @param WP_Post $post - the post object, passed by reference
	 *
	 * @since 1.0.0
	 */
	public function restrict_post( $post ) {

		if ( ! in_array( $post->ID, $this->content_restricted, false ) && hammock_is_post_protected( $post->ID ) ) {
			$message = hammock_content_protected_message( $post->ID, 'post', $post->post_type );

			$this->restrict_post_content( $post, $message );
			$this->restrict_comments( $post );
		}

		if ( in_array( $post->ID, $this->content_restricted, false ) ) {
			$this->content_restricted[] = (int) $post->ID;
		}
	}


	/**
	 * Restricts post content.
	 *
	 * @param \WP_Post $post - the post object, passed by reference
	 * @param string   $message - the new content HTML
	 *
	 * @since 1.0.0
	 */
	private function restrict_post_content( \WP_Post $post, $message ) {
		global $page, $pages, $multipages, $numpages;

		// update the post object passed by reference
		$post->post_content = $message;
		$post->post_excerpt = $message;

		$page       = 1;
		$pages      = array( $message );
		$multipages = 0;
		$numpages   = 1;

		$this->restricted_post_content[ $post->ID ] = $message;
	}

	/**
	 * Restrict comments
	 *
	 * @param \WP_Post $post the post object, passed by reference
	 *
	 * @since 1.0.0
	 */
	private function restrict_comments( \WP_Post $post ) {
		$post->comment_status = 'closed';
		$post->comment_count  = 0;
	}


	/**
	 * Makes sure the restricted content data is persisted.
	 *
	 * @param string $content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function restricted_content_filtering( $content ) {
		global $post;
		if ( $post && ! empty( $post->ID ) && array_key_exists( $post->ID, $this->restricted_post_content ) ) {
			$content = $this->restricted_post_content[ $post->ID ];
		}
		return $content;
	}


	/**
	 * Restricts content feed enclosures if the related post is restricted and current user doesn't have access.
	 *
	 * @param string $enclosure feed enclosure
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function rss_enclosure_content( $enclosure ) {
		global $post;

		$can_view = true;

		if ( $enclosure && $post ) {
			$can_view = hammock_is_post_protected( $post );
		}

		return $can_view ? $enclosure : '';
	}


	/**
	 * Hides restricted content comments (including product reviews).
	 *
	 * @since 1.9.0
	 */
	private function hide_restricted_content_comments() {
		global $post, $wp_query;

		if ( $post ) {

			$restricted = hammock_is_post_protected( $post );

			if ( $restricted ) {
				$wp_query->comment_count   = 0;
				$wp_query->current_comment = 999999;
			}
		}
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
		$settings        = $this->settings->get_addon_setting( 'category' );
		$post_categories = wp_get_post_categories( $item_id, array( 'fields' => 'id=>slug' ) );
		$protected       = isset( $settings['protected'] ) ? $settings['protected'] : array();
		foreach ( $post_categories as $id => $slug ) {
			if ( in_array( $slug, $protected ) ) {
				return true;
			}
		}
		return false;
	}
}


