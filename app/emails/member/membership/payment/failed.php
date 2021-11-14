<?php
namespace Hammock\Emails\Member\Membership\Payment;

use Hammock\Base\Email;

/**
 * User card expire
 */
class Failed extends Email {

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
		$type                = \Hammock\Services\Emails::COMM_TYPE_FAILED_PAYMENT;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/payment/failed.php';
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
			'title'       => __( 'Payment failed', 'hammock' ),
			'description' => __( 'Sent to a member when payment fails', 'hammock' ),
			'heading'     => __( 'Payment failed', 'hammock' ),
			'subject'     => sprintf( __( 'Your %s membership payment failed', 'hammock' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

