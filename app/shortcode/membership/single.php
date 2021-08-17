<?php
namespace Hammock\Shortcode\Membership;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Shortcode;

/**
 * Single Membership shortcode manager
 * Display a single membership
 * 
 * @since 1.0.0
 */
class Single extends Shortcode {

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


	/**
	 * Get the shortcode content output
	 * 
	 * @param array $atts - the shortcode attributes
	 * 
	 * @since 1.0.0
	 */
	public function output( $atts ) {
		$this->get_template( 'membership-list.php' );
	}
}

?>