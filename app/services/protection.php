<?php
namespace HubloyMembership\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Model\Settings;
use HubloyMembership\Helper\Pages;

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
	public static function instance( $protect = true ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $protect );
		}

		return self::$instance;
	}


	/**
	 * Main service constructor
	 *
	 * Sets up the service
	 */
	public function __construct( $protect = true ) {
		$this->settings = new Settings();
		$this->init_rules();
		if ( $protect ) {
			$this->protect_content();
		}
	}

	/**
	 * Initialize the rules
	 *
	 * @since  1.0.0
	 */
	private function init_rules() {
		$this->page_rule         = \HubloyMembership\Rule\Page::instance();
		$this->post_rule         = \HubloyMembership\Rule\Post::instance();
		$this->category_rule     = \HubloyMembership\Rule\Category::instance();
		$this->content_rule      = \HubloyMembership\Rule\Content::instance();
		$this->media_rule        = \HubloyMembership\Rule\Media::instance();
		$this->menu_rule         = \HubloyMembership\Rule\Menu::instance();
		$this->custom_types_rule = \HubloyMembership\Rule\Custom\Types::instance();
		$this->custom_items_rule = \HubloyMembership\Rule\Custom\Items::instance();
	}

	/**
	 * Protect content
	 *
	 * @since 1.0.0
	 */
	public function protect_content() {
		$this->enabled = $this->settings->get_general_setting( 'content_protection' );
		if ( $this->enabled ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

			/**
			 * Action to load other protection rules
			 *
			 * @since 1.0.0
			 */
			do_action( 'hubloy_membership_load_protection_rule' );
		}

		/**
		 * Protection rule filters
		 * All defined in the main Rule class
		 * Checks if current user has access to content
		 *
		 * @since 1.0.0
		 */
		add_filter( 'hubloy_membership_enabled_member_has_access', array( $this, 'enabled_member_access' ), 10, 4 );
		add_filter( 'hubloy_membership_disabled_member_has_access', array( $this, 'disabled_member_access' ), 10, 4 );
		add_filter( 'hubloy_membership_non_member_has_access', array( $this, 'non_member_access' ), 10, 4 );
		add_filter( 'hubloy_membership_guest_has_access', array( $this, 'guest_access' ), 10, 3 );
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
			'hubloy_membership-content-protection',
			__( 'Memberships', 'memberships-by-hubloy' ),
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
				$post_meta = get_post_meta( $object->ID, '_hubloy_membership_mebership_access', true );
				foreach ( $plans as $plan_id ) {
					if ( hubloy_membership_is_member_plan_active( $plan_id ) ) {
						$has_access = $this->check_member_post_access( $object->ID, $plan_id, $post_meta );
						return $has_access;
					}
				}
			} elseif ( $object instanceof \WP_Term ) {
				$term_meta = get_term_meta( $object->term_id, '_hubloy_membership_mebership_access', true );
				foreach ( $plans as $plan_id ) {
					if ( hubloy_membership_is_member_plan_active( $plan_id ) ) {
						$has_access = $this->check_member_term_access( $object->term_id, $plan_id, $term_meta );
						return $has_access;
					}
				}
			} else {
				return apply_filter( 'hubloy_membership_check_enabled_member_has_access', $access, $member, $object, $content_type, $plans );
			}
		}
		return $access;
	}

	/**
	 * Check member post access
	 *
	 * @param int   $post_id The post or page id
	 * @param int   $plan_id The membership id
	 * @param mixed $post_meta The post meta
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function check_member_post_access( $post_id, $plan_id, $post_meta ) {
		if ( $post_meta && is_array( $post_meta ) && ! empty( $post_meta ) ) {
			return in_array( $post_id, $post_meta, true );
		}
		return $this->page_rule->rule_applies( $post_id, $plan_id ) || $this->post_rule->rule_applies( $post_id, $plan_id );
	}

	/**
	 * Check member term access
	 *
	 * @param int   $term_id The term id
	 * @param int   $plan_id The membership id
	 * @param mixed $term_meta The term meta
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function check_member_term_access( $term_id, $plan_id, $term_meta ) {
		if ( $term_meta && is_array( $term_meta ) && ! empty( $term_meta ) ) {
			return in_array( $term_id, $term_meta, true );
		}
		return $this->category_rule->rule_applies( $term_id, $plan_id );
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
						return false;
					}
				}
				$access = ! $this->post_has_rule( $object->ID );
			} elseif ( $object instanceof \WP_Term && $this->category_rule != null ) {
				$restricted = $this->category_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->term_id, $restricted ) ) {
						return false;
					}
				}
				$access = ! $this->term_has_rule( $object->term_id );
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
						return false;
					}
				}
				$access = ! $this->post_has_rule( $object->ID );
			} elseif ( $object instanceof \WP_Term && $this->category_rule != null ) {
				$restricted = $this->category_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->term_id, $restricted ) ) {
						return false;
					}
				}
				$access = ! $this->term_has_rule( $object->term_id );
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
						return false;
					}
				}
				$access = ! $this->post_has_rule( $object->ID );
			} elseif ( $object instanceof \WP_Term && $this->category_rule != null ) {
				$restricted = $this->category_rule->get_member_restricted_content_ids();
				if ( ! empty( $restricted ) ) {
					if ( in_array( $object->term_id, $restricted ) ) {
						return false;
					}
				}
				$access = ! $this->term_has_rule( $object->term_id );
			}
		}
		return $access;
	}

	/**
	 * Check if a post or page has an active rule
	 *
	 * @param int $post_id The post id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function post_has_rule( $post_id ) {
		return $this->page_rule->has_active_rule( $post_id ) || $this->post_rule->has_active_rule( $post_id );
	}


	/**
	 * Check if a term has an active rule
	 *
	 * @param int $term_id The term id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function term_has_rule( $term_id ) {
		return $this->category_rule->has_active_rule( $term_id );
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
			return apply_filters( 'hubloy_membership_post_content_has_access', true, $post, $post_type );
		}
		return false;
	}

	public function &__get( $key ) {
		if ( method_exists( $this, 'get_' . $key ) ) {
			$value = call_user_func( array( $this, 'get_' . $key ) );
		} else {
			$value = &$this->{$key};
		}

		return $value;
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
			'hubloy_membership_content_get_excluded_content',
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
			'hubloy_membership_get_post_types_args',
			array(
				'public'   => true,
				'_builtin' => false,
			)
		);
		$cpts     = get_post_types( $args );
		$excluded = self::get_excluded_content();

		return apply_filters(
			'hubloy_membership_get_custom_post_types',
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
				'hubloy_membership_content_restriction_excluded_post_types',
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

