<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Model\Settings;
use Hammock\Helper\Pages;

/**
 * Protection service
 * Handles content protection and access
 *
 * @since 1.0.0
 */
class Protection {

	/**
	 *
	 * @var array valid post types for content restriction rules
	 */
	private static $valid_post_types_for_restriction_rules;

	/**
	 * Settings object
	 *
	 * @since 1.0.0
	 */
	private $settings = null;

	/**
	 * The post rule
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $post_rule = null;

	/**
	 * The category rule
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $category_rule = null;

	/**
	 * The content rule
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $content_rule = null;

	/**
	 * The media rule
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $media_rule = null;

	/**
	 * The menu rule
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $menu_rule = null;

	/**
	 * The page rule
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $page_rule = null;

	/**
	 * The custom items rule
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $custom_items_rule = null;

	/**
	 * The custom types rule
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $custom_types_rule = null;

	/**
	 * Check if content protection is enabled
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $enabled = false;

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Protection
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
	 * @return Content
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Main service constructor
	 *
	 * Sets up the service
	 */
	public function __construct() {
		$this->settings = new Settings();
		$this->protect_content();
	}

	/**
	 * Protect content
	 *
	 * @since 1.0.0
	 */
	public function protect_content() {
		$this->post_rule         = \Hammock\Rule\Post::instance();
		$this->category_rule     = \Hammock\Rule\Category::instance();
		$this->content_rule      = \Hammock\Rule\Content::instance();
		$this->media_rule        = \Hammock\Rule\Media::instance();
		$this->menu_rule         = \Hammock\Rule\Menu::instance();
		$this->page_rule         = \Hammock\Rule\Page::instance();
		$this->custom_items_rule = \Hammock\Rule\Custom\Items::instance();
		$this->custom_types_rule = \Hammock\Rule\Custom\Types::instance();

		$this->enabled       = $this->settings->get_general_setting( 'content_protection' );
		if ( $this->enabled ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

			/**
			 * Action to load other protection rules
			 *
			 * @since 1.0.0
			 */
			do_action( 'hammock_load_protection_rule' );
		}

		/**
		 * Protection rule filters
		 * All defined in the main Rule class
		 * Checks if current user has access to content
		 *
		 * @since 1.0.0
		 */
		add_filter( 'hammock_enabled_member_has_access', array( $this, 'enabled_member_access' ), 10, 4 );
		add_filter( 'hammock_disabled_member_has_access', array( $this, 'disabled_member_access' ), 10, 4 );
		add_filter( 'hammock_non_member_has_access', array( $this, 'non_member_access' ), 10, 4 );
		add_filter( 'hammock_guest_has_access', array( $this, 'guest_access' ), 10, 3 );
	}

	/**
	 * Add protection meta box
	 *
	 * @since 1.0.0
	 */
	public function add_meta_box() {
		global $post;

		// sanity check
		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		$screen  = get_current_screen();
		$screens = self::get_post_types_for_meta_box();

		if ( ! $screen || ! in_array( $screen->id, $screens, true ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'page' == $post->post_type && Pages::is_membership_page( $post->ID ) ) {
			return;
		}

		add_meta_box(
			'hammock-content-protection',
			__( 'Memberships', 'hammock' ),
			array( $this, 'render_meta_box' ),
			$screen->id,
			'normal',
			'default'
		);
	}

	/**
	 * Render metabox to protect posts
	 *
	 * @param \WP_Post $post - the post object
	 */
	public function render_meta_box( $post ) {

	}

	/**
	 * Filter to check if enabled member has access
	 *
	 * @param bool   $access
	 * @param Member $member - the member object
	 * @param object $object - the object
	 * @param string $content_type - the content type
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function enabled_member_access( $access, $member, $object, $content_type ) {
		$plans = $member->get_plans();
		if ( ! empty( $plans ) ) {
			if ( $object instanceof \WP_Post ) {
				$post_meta = get_post_meta( $object->ID, '_hammock_mebership_access', true );
				if ( $post_meta && is_array( $post_meta ) && ! empty( $post_meta ) ) {
					foreach ( $plans as $plan_id ) {
						if ( hammock_is_member_plan_active( $plan_id ) ) {
							if ( in_array( $plan_id, $post_meta ) ) {
								$access = true;
							}
						}
					}
				}
			} elseif ( $object instanceof \WP_Term ) {
				$term_meta = get_term_meta( $object->term_id, '_hammock_mebership_access', true );
				if ( $term_meta && is_array( $term_meta ) && ! empty( $term_meta ) ) {
					foreach ( $plans as $plan_id ) {
						if ( hammock_is_member_plan_active( $plan_id ) ) {
							if ( in_array( $plan_id, $term_meta ) ) {
								$access = true;
							}
						}
					}
				}
			}
		}
		return $access;
	}

	/**
	 * Filter to check if disabled member has access
	 *
	 * @param bool   $access
	 * @param Member $member - the member object
	 * @param object $object - the object
	 * @param string $content_type - the content type
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function disabled_member_access( $access, $member, $object, $content_type ) {
		if ( $this->enabled ) {
			if ( $object instanceof \WP_Post && $this->post_rule != null ) {
				$restricted = $this->post_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->ID, $restricted ) ) {
						$access = false;
					}
				}
			} elseif ( $object instanceof \WP_Term && $this->category_rule != null ) {
				$restricted = $this->category_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->term_id, $restricted ) ) {
						$access = false;
					}
				}
			}
		}
		return $access;
	}

	/**
	 * Filter to check if non member user has access
	 *
	 * @param bool   $access
	 * @param int    $user_id - the user id
	 * @param object $object - the object
	 * @param string $content_type - the content type
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function non_member_access( $access, $user_id, $object, $content_type ) {
		if ( $this->enabled ) {
			if ( $object instanceof \WP_Post && $this->post_rule != null ) {
				$restricted = $this->post_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->ID, $restricted ) ) {
						$access = false;
					}
				}
			} elseif ( $object instanceof \WP_Term && $this->category_rule != null ) {
				$restricted = $this->category_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->term_id, $restricted ) ) {
						$access = false;
					}
				}
			}
		}
		return $access;
	}

	/**
	 * Guest access
	 *
	 * @param bool   $access
	 * @param object $object - the object
	 * @param string $content_type - the content type
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function guest_access( $access, $object, $content_type ) {
		if ( $this->enabled ) {
			if ( $object instanceof \WP_Post && $this->post_rule != null ) {
				$restricted = $this->post_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->ID, $restricted ) ) {
						$access = false;
					}
				}
			} elseif ( $object instanceof \WP_Term && $this->category_rule != null ) {
				$restricted = $this->category_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->term_id, $restricted ) ) {
						$access = false;
					}
				}
			}
		}
		return $access;
	}

	/**
	 * Check if the post type has access to the current user
	 *
	 * @param string $type - the content type
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_access( $type ) {
		if ( is_super_admin() || current_user_can( 'manage_options' ) ) {
			return true;
		}

		$post      = get_queried_object();
		$post_id   = ! empty( $post->ID ) ? $post->ID : 0;
		$post_type = ! empty( $post->post_type ) ? $post->post_type : '';
		if ( empty( $post_type ) && ! empty( $post->query_var ) ) {
			$post_type = $post->query_var;
		}

		if ( in_array( $post_type, self::get_custom_post_types() ) ) {
			return apply_filters( 'hammock_post_content_has_access', true, $post, $post_type );
		}
		return false;
	}


	/**
	 * Get post types that should not be protected.
	 *
	 * Default WP post types
	 *
	 * @since  1.0.0
	 *
	 * @return array The excluded post types.
	 */
	public static function get_excluded_content() {

		return apply_filters(
			'hammock_content_get_excluded_content',
			array(
				'post',
				'page',
				'attachment',
				'revision',
				'nav_menu_item',
			)
		);
	}

	/**
	 * Get custom post types.
	 *
	 * Excludes membership plugin and default wp post types.
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public static function get_custom_post_types() {
		$args     = apply_filters(
			'hammock_get_post_types_args',
			array(
				'public'   => true,
				'_builtin' => false,
			)
		);
		$cpts     = get_post_types( $args );
		$excluded = self::get_excluded_content();

		return apply_filters(
			'hammock_get_custom_post_types',
			array_diff( $cpts, $excluded )
		);
	}

	/**
	 * Returns valid post types for content restriction rules.
	 *
	 * @param bool $exclude_products whether to exclude products from results (default true, exclude them)
	 *
	 * @since 1.0.0
	 *
	 * @return array associative array of post type names and labels
	 */
	public static function get_post_types_for_meta_box( $exclude_products = true ) {

		if ( empty( self::$valid_post_types_for_restriction_rules ) ) {

			self::$valid_post_types_for_restriction_rules = array();

			foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
				self::$valid_post_types_for_restriction_rules[ $post_type->name ] = $post_type;
			}
		}

		$post_types = self::$valid_post_types_for_restriction_rules;

		if ( ! empty( $post_types ) ) {

			/**
			 * Filters the excluded (blacklisted) post types from content restriction content type options.
			 *
			 * For example, post types listed here won't appear among restrictable options in Membership Plans admin UI.
			 *
			 * @since 1.0.0
			 *
			 * @param array $post_types List of post types to exclude
			 */
			$excluded_post_types = apply_filters(
				'hammock_content_restriction_excluded_post_types',
				array(
					'attachment',
					'wc_product_tab',
					'wooframework',
				)
			);

			// skip excluded custom post types
			if ( ! empty( $excluded_post_types ) ) {
				foreach ( $excluded_post_types as $post_type ) {
					if ( isset( $post_types[ $post_type ] ) ) {
						unset( $post_types[ $post_type ] );
					}
				}
			}

			// skip products - they have their own restriction rules
			if ( $exclude_products && ! empty( $post_types ) ) {
				if ( isset( $post_types['product'] ) ) {
					unset( $post_types['product'] );
				}
				if ( isset( $post_types['product_variation'] ) ) {
					unset( $post_types['product_variation'] );
				}
			}
		}

		return $post_types;
	}
}

