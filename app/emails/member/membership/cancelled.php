<?php
namespace HubloyMembership\Emails\Member\Membership;

use HubloyMembership\Base\Email;

/**
 * User card expire
 */
class Cancelled extends Email {

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
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_CANCELLED;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/canceled.php';
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
			'title'       => __( 'Membership Cancelled', 'hubloy-membership' ),
			'description' => __( 'Email sent to user after a membership subscription is cancelled', 'hubloy-membership' ),
			'heading'     => __( 'Membership Cancelled', 'hubloy-membership' ),
			'subject'     => sprintf( __( 'Membership %s cancelled', 'hubloy-membership' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

