<?php
namespace HubloyMembership\Emails\Member\Membership\Status;

use HubloyMembership\Base\Email;

/**
 * User card expire
 */
class Before extends Email {

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
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_BEFORE_FINISHES;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/status/before.php';
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
			'title'       => __( 'Before Membership Ends', 'hubloy-membership' ),
			'description' => __( 'Sent to a member before the membership finishes', 'hubloy-membership' ),
			'heading'     => __( 'Membership Ending Soon', 'hubloy-membership' ),
			'subject'     => sprintf( __( 'Reminder: your %s membership will end soon', 'hubloy-membership' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

