<?php
namespace HubloyMembership\Emails\Member;

use HubloyMembership\Base\Email;

/**
 * User account registration
 */
class Registration extends Email {

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
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_REGISTRATION;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/member-new-account.php';
		$this->placeholders  = array();
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
			'title'       => __( 'Account Registration', 'memberships-by-hubloy' ),
			'description' => __( 'Sent once an account is created', 'memberships-by-hubloy' ),
			'heading'     => sprintf( __( 'Welcome to %s', 'memberships-by-hubloy' ), '{site_title}' ),
			'subject'     => sprintf( __( 'Your %s account has been created!', 'memberships-by-hubloy' ), '{site_title}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

