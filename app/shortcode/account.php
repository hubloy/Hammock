<?php
namespace HubloyMembership\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Shortcode;

/**
 * Account shortcode manager
 * Handles content of the account page
 *
 * @since 1.0.0
 */
class Account extends Shortcode {

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

		if ( isset( $_REQUEST['verify'] ) && isset( $_REQUEST['id'] ) ) {
			$verify  = sanitize_text_field( $_REQUEST['verify'] );
			$user_id = absint( sanitize_text_field( $_REQUEST['id'] ) );

			$user_activation_status = get_user_meta( $user_id, '_hubloy-membership_activation_status', true );

			if ( $user_activation_status && intval( $user_activation_status ) === 2 ) {
				$activation_code = get_user_meta( $user_id, '_hubloy-membership_activation_key', true );
				if ( $activation_code === $verify ) {
					// Account verified
					update_user_meta( $user_id, '_hubloy-membership_activation_status', 3 );
				}
			}
		}

		if ( ! is_user_logged_in() ) {
			$this->get_template( 'account/auth-access.php' );
		} else {
			$this->get_template( 'member-account.php' );
		}
	}
}


