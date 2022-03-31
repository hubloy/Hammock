<?php
namespace HubloyMembership\Emails\Member;

use HubloyMembership\Base\Email;
use HubloyMembership\Model\Settings;

/**
 * User email verification
 */
class Verify extends Email {

	/**
	 * Setting object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $settings = null;

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
		$this->settings      = new Settings();
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_REGISTRATION_VERIFY;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/member-verify-account.php';
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
			'title'       => __( 'Account Verification', 'hubloy-membership' ),
			'description' => __( 'Sent to customers to verify their account emails', 'hubloy-membership' ),
			'heading'     => __( 'Account Verification', 'hubloy-membership' ),
			'subject'     => sprintf( __( 'Verify your account on %s', 'hubloy-membership' ), '{site_title}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

