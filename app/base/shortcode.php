<?php
namespace HubloyMembership\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shortcode base class
 * All shortcodes extend this calss
 *
 * @since 1.0.0
 */
class Shortcode {


	/**
	 * Initalize Shortcode
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->init();
	}


	/**
	 * Main shortcode init
	 * This function offers a safe way for each shortcode to initialize itself if
	 * required.
	 *
	 * @since  1.0.0
	 */
	public function init() {

	}

	/**
	 * Render shortcode
	 *
	 * @param array  $atts - the shortcode attributes
	 * @param string $content The default content between the shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function render( $atts, $content = '' ) {
		ob_start();
		$this->output( $atts, $content );
		return ob_get_clean();
	}

	/**
	 * Get the shortcode content output
	 *
	 * @param array  $atts - the shortcode attributes
	 * @param string $content The default content between the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' ) {
		esc_html_e( 'Shortcode', 'hubloy-membership' );
	}

	/**
	 * Get template
	 *
	 * @param string $template - relative path of template
	 * @param array  $args - the arguments
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_template( $template, $args = array() ) {
		\HubloyMembership\Helper\Template::get_template( $template, $args );
	}
}

