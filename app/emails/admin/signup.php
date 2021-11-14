<?php
namespace Hammock\Emails\Admin;

use Hammock\Base\Email;

/**
 * Admin membership registration
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
		$this->id            = 'admin-' . $type;
		$this->is_admin      = true;
		$this->template_html = 'emails/admin/signup.php';
		$this->placeholders  = array(
			'{membership_name}' => '',
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
			'description' => __( 'Email sent to admin to notify a new user has registered', 'hammock' ),
			'heading'     => sprintf( __( 'New sign-up %s', 'hammock' ), '{membership_name}' ),
			'subject'     => sprintf( __( '%1$s: New sign-up %2$s', 'hammock' ), '[{site_title}]', '{membership_name}' ),
			'recipient'   => get_option( 'admin_email' ),
			'enabled'     => true,
		);
	}
}

