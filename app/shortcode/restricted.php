<?php
namespace Hammock\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Shortcode;
use Hammock\Services\Memberships;

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
	 * @param array $atts - the shortcode attributes
	 * 
	 * @since 1.0.0
	 */
	public function output( $atts ) {
		if ( isset( $atts['id'] ) ) {
			$membership = $this->membership_service->get_membership_by_id( ( int ) $atts['id'] );
			if ( $membership->is_valid() ) {

			}
		}
	}
}

?>