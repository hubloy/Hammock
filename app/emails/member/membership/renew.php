<?php
namespace HubloyMembership\Emails\Member\Membership;

use HubloyMembership\Base\Email;

/**
 * User card expire
 */
class Renew extends Email {

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
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_RENEWED;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/renew.php';
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
			'title'       => __( 'Membership Renewed', 'memberships-by-hubloy' ),
			'description' => __( 'Email sent to user after a membership subscription is renewed', 'memberships-by-hubloy' ),
			'heading'     => __( 'Membership Renewed', 'memberships-by-hubloy' ),
			'subject'     => sprintf( __( 'Membership %s has been renewed', 'memberships-by-hubloy' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

