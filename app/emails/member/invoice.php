<?php
namespace Hammock\Emails\Member;

use Hammock\Base\Email;

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
		$type                = \Hammock\Services\Emails::COMM_TYPE_INVOICE;
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
			'title'       => __( 'New payment', 'hammock' ),
			'description' => __( 'Sent each time a payment has been made', 'hammock' ),
			'heading'     => sprintf( __( 'Membership receipt %s', 'hammock' ), '#{invoice_number}' ),
			'subject'     => sprintf( __( '%s: Your %s membership receipt %s', 'hammock' ), '[{site_title}]', '{membership_name}', '#{invoice_number}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

