<?php
namespace HubloyMembership\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Shortcode;
use HubloyMembership\Services\Memberships;

/**
 * Restricted shortcode manager
 * Handles content of the protected page content
 *
 * @since 1.0.0
 */
class Restricted extends Shortcode {

	/**
	 * The membership service
	 *
	 * @since 1.0.0
	 */
	private $membership_service = null;

	/**
	 * Singletone instance of the shortcode.
	 *
	 * @since  1.0.0
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the shortcode.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return object
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init() {
		$this->membership_service = new Memberships();
	}

	/**
	 * Get the shortcode content output
	 *
	 * @param array  $atts  The shortcode attributes
	 * @param string $content The content wrapped in the shortcode
	 *
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' ) {
		global $post;
		if ( isset( $atts['id'] ) ) {
			$membership = $this->membership_service->get_membership_by_id( (int) $atts['id'] );
			if ( ! $membership->is_valid() ) {
				$message = hubloy_membership_content_protected_message( $post->ID, 'post', $post->post_type );
				return $message;
			}
		}
		return $content;
	}
}


