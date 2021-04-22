<?php
namespace Hammock\Emails\Admin;

use Hammock\Base\Email;

/**
 * Admin invoice
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
		$this->id            = 'admin-' . $type;
		$this->is_admin 	 = true;
		$this->template_html = 'emails/admin/invoice.php';
		$this->placeholders  = array(
			'{invoice_number}' => ''
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
			'title'       => __( 'New payment', 'hammock' ),
			'description' => __( 'Email sent to admin to notify a new payment is received', 'hammock' ),
			'heading'     => sprintf( __( 'New payment %s', 'hammock' ), '#{invoice_number}' ),
			'subject'     => sprintf( __( '%s: New payment %s', 'hammock' ), '[{site_title}]', '#{invoice_number}' ),
			'recipient'   => get_option( 'admin_email' ),
			'enabled'     => true,
		);
	}
}

