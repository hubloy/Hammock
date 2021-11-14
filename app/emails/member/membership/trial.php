<?php
namespace Hammock\Emails\Member\Membership;

use Hammock\Base\Email;

/**
 * User card expire
 */
class Trial extends Email {

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
		$type                = \Hammock\Services\Emails::COMM_TYPE_BEFORE_TRIAL_FINISHES;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/trial.php';
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
			'title'       => __( 'Before Trial Finishes', 'hammock' ),
			'description' => __( 'Email sent to user before a trial is complete', 'hammock' ),
			'heading'     => __( 'Trial about to end', 'hammock' ),
			'subject'     => sprintf( __( 'Your %s membership trial will end soon', 'hammock' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

