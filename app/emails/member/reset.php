<?php
namespace HubloyMembership\Emails\Member;

use HubloyMembership\Base\Email;

/**
 * User email password reset
 */
class Reset extends Email {

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
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_RESETPASSWORD;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/member-reset-password.php';
		$this->placeholders  = array(
			'{reset_url}' => '',
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
			'title'       => __( 'Reset Password', 'memberships-by-hubloy' ),
			'description' => __( 'Sent when customers reset their password', 'memberships-by-hubloy' ),
			'heading'     => __( 'Password Reset Request', 'memberships-by-hubloy' ),
			'subject'     => sprintf( __( 'Password Reset Request for %s', 'memberships-by-hubloy' ), '{site_title}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

