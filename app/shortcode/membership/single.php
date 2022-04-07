<?php
namespace HubloyMembership\Shortcode\Membership;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Shortcode;
use HubloyMembership\Model\Membership;

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
	 * @param array  $atts - the shortcode attributes
	 * @param string $content The content wrapped in the shortcode
	 *
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' ) {
		$attributes = shortcode_atts(
			array(
				'id' => false,
			),
			$atts
		);

		if ( $attributes['id'] ) {
			$plan_id = (int) $attributes['id'];
			$plan    = new Membership( $plan_id );

			if ( $plan->is_valid() ) {
				$this->get_template(
					'plans/single-plan-card.php',
					array(
						'plan' => $plan,
					)
				);
			}
		}
	}
}


