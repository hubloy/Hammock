<?php
namespace Hammock\Emails\Member;

use Hammock\Base\Email;

/**
 * User card expire
 */
class Account extends Email {

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
		$type                = \Hammock\Services\Emails::COMM_TYPE_INFO_UPDATE;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/member-account-update.php';
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
			'title'       => __( 'Account Update', 'hammock' ),
			'description' => __( 'Sent when a member updates any personal information (e.g. credit card, name, address details etc.)', 'hammock' ),
			'heading'     => __( 'Your billing details have been changed.', 'hammock' ),
			'subject'     => sprintf( __( '%s: Your billing details have been changed', 'hammock' ), '[{site_title}]' ),
			'recipient'   => '',
			'enabled'     => true,
		);
	}
}

