<?php
namespace Hammock\Shortcode\Membership;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Shortcode;
use Hammock\Model\Membership;

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
        $attributes = shortcode_atts( array(
			'plan_id' => false
		), $atts );

        if ( $attributes['plan_id'] ) {
            $plan_id    = ( int ) $attributes['plan_id'];
            $plan       = new Membership( $plan_id );

            if ( $plan->id > 0 ) {
                $this->get_template( 
                    'plans/single-plan-card.php', array(
                        'plan'	=> $plan,
                    ) 
                );
            }
        }
	}
}

?>