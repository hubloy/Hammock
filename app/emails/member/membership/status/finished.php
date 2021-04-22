<?php
namespace Hammock\Emails\Member\Membership\Status;

use Hammock\Base\Email;

/**
 * User card expire
 */
class Finished extends Email {

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
		$type                = \Hammock\Services\Emails::COMM_TYPE_FINISHED;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/status/finished.php';
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
			'title'       => __( 'Membership Ended', 'hammock' ),
			'description' => __( 'Sent to a member when the membership ends', 'hammock' ),
			'heading'     => __( 'Membership Ended', 'hammock' ),
			'subject'     => sprintf( __( 'Your %s membership hase ended', 'hammock' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

