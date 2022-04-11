<?php
namespace HubloyMembership\Emails\Member;

use HubloyMembership\Base\Email;

/**
 * User account registration
 */
class Invoice extends Email {

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
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_INVOICE;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/member-invoice.php';
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
			'title'       => __( 'New payment', 'memberships-by-hubloy' ),
			'description' => __( 'Sent each time a payment has been made', 'memberships-by-hubloy' ),
			'heading'     => sprintf( __( 'Membership receipt %s', 'memberships-by-hubloy' ), '#{invoice_number}' ),
			'subject'     => sprintf( __( '%1$s: Your %2$s membership receipt %3$s', 'memberships-by-hubloy' ), '[{site_title}]', '{membership_name}', '#{invoice_number}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

