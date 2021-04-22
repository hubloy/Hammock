<?php
namespace Hammock\Emails\Member\Membership;

use Hammock\Base\Email;

/**
 * Member membership registration
 */
class Signup extends Email {

	/**
	 * Singletone instance of the addon.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the addon.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set up variables
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$type                = \Hammock\Services\Emails::COMM_TYPE_SIGNUP;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/signup.php';
		$this->placeholders  = array(
			'{membership_name}' => ''
		);
	}

	/**
	 * Register defaults
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_defaults() {
		return array(
			'title'       => __( 'Membership Sign-up', 'hammock' ),
			'description' => __( 'Email sent to user after they join a membership', 'hammock' ),
			'heading'     => __( 'New sign-up', 'hammock' ),
			'subject'     => sprintf( __( 'New sign-up to %s membership', 'hammock' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => true,
		);
	}
}

