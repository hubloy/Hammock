<?php
namespace HubloyMembership\Emails\Member\Membership\Payment;

use HubloyMembership\Base\Email;

/**
 * User card expire
 */
class Overdue extends Email {

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
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_AFTER_PAYMENT_DUE;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/payment/overdue.php';
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
			'title'       => __( 'Payment overdue', 'hubloy-membership' ),
			'description' => __( 'Sent to a member when payment is overdue', 'hubloy-membership' ),
			'heading'     => __( 'Payment overdue', 'hubloy-membership' ),
			'subject'     => sprintf( __( 'Your %s membership payment is overdue', 'hubloy-membership' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

