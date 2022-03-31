<?php
namespace HubloyMembership\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pages
 * Handles page creation and retrieving
 *
 * @since 1.0.0
 */
class Pages {

	/**
	 * List of pages
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $drop_down = array();

	/**
	 * List pages
	 * Uses the `get_pages()` functions
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function list_pages() {
		if ( empty( self::$drop_down ) ) {
			$pages             = get_pages();
			self::$drop_down[] = __( 'Select Page', 'hubloy-membership' );
			foreach ( $pages as $page ) {
				self::$drop_down[ $page->ID ] = $page->post_title;
			}
		}
		return apply_filters( 'hubloy-membership_page_drop_downs', self::$drop_down );
	}

	/**
	 * Create page
	 *
	 * @param string $title - the page title
	 * @param string $content - the page content
	 * @param bool   $is_shortcode - true or false for shortcode
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool
	 */
	public static function create_page( $title, $content = '', $is_shortcode = true, $slug = false ) {
		if ( $is_shortcode ) {
			$content = '<!-- wp:shortcode -->' . $content . '<!-- /wp:shortcode -->';
		}

		if ( ! $slug ) {
			$slug = sanitize_title( $title );
		}

		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $title,
			'post_content'   => $content,
			'post_parent'    => 0,
			'comment_status' => 'closed',
		);
		$page_id   = wp_insert_post( $page_data );
		if ( is_wp_error( $page_id ) ) {
			return false;
		}
		return $page_id;
	}

	/**
	 * Create a page and set the default content
	 *
	 * @param string $type - the page type
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool - the page id or false if failed
	 */
	public static function create( $type ) {
		$type = trim( $type );
		switch ( $type ) {
			case 'membership_list':
				$page_id = self::create_page( __( 'Membership List', 'hubloy-membership' ), '[hubloy-membership_membership_list]' );
				return $page_id;
			break;
			case 'protected_content':
				$page_id = self::create_page( __( 'Protected Content', 'hubloy-membership' ), '[hubloy-membership_protected_content]' );
				return $page_id;
			break;
			case 'registration':
				$page_id = self::create_page( __( 'Register', 'hubloy-membership' ), '[hubloy-membership_registration]' );
				return $page_id;
			break;
			case 'thank_you_page':
				$page_id = self::create_page( __( 'Thank You', 'hubloy-membership' ), '[hubloy-membership_thank_you_page]' );
				return $page_id;
			break;
			case 'account_page':
				$page_id = self::create_page( __( 'Account', 'hubloy-membership' ), '[hubloy-membership_account_page]' );
				return $page_id;
			break;
			default:
				return 0;
			break;
		}
	}

	/**
	 * Checks if the current page is a membership page
	 *
	 * @param int $page_id - the page id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_membership_page( $page_id ) {
		$settings = \HubloyMembership\Model\Settings::instance();
		$pages    = $settings->get_general_setting( 'pages', array() );
		$page_ids = array_values( $pages );
		if ( in_array( $page_id, $page_ids ) ) {
			return true;
		}
		return false;
	}
}


