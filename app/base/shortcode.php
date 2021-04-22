<?php
namespace Hammock\Base;

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
	 * Render shortcode
	 * 
	 * @param array $atts - the shortcode attributes
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function render( $atts ) {
		ob_start();
		$this->output( $atts );
		return ob_get_clean();
	}

	/**
	 * Get the shortcode content output
	 * 
	 * @param array $atts - the shortcode attributes
	 * 
	 * @since 1.0.0
	 */
	public function output( $atts ) {
		_e( 'Shortcode', 'hammock' );
	}

	/**
	 * Get template
	 * 
	 * @param string $template - relative path of template
	 * @param array $args - the arguments
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_template( $template, $args = array() ) {
		\Hammock\Helper\Template::get_template( $template, $args );
	}
}
?>