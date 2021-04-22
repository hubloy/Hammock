<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Model\Email;

/**
 * Emails Service
 *
 * @since 1.0.0
 */
class Emails {

	/**
	 * Email type constants.
	 *
	 * @since 1.0.0
	 * @see   $type
	 * @var   string The communication type
	 */
	const COMM_TYPE_REGISTRATION          = 'new-account';
	const COMM_TYPE_REGISTRATION_VERIFY   = 'new-account-verify';
	const COMM_TYPE_RESETPASSWORD         = 'reset-password';
	const COMM_TYPE_SIGNUP          	  = 'new-account';
	const COMM_TYPE_RENEWED               = 'renewed';
	const COMM_TYPE_INVOICE               = 'invoice';
	const COMM_TYPE_BEFORE_FINISHES       = 'before-membership-finishes';
	const COMM_TYPE_FINISHED              = 'membership-finished';
	const COMM_TYPE_AFTER_FINISHES        = 'after-membership-finishes';
	const COMM_TYPE_CANCELLED             = 'membership-cancelled';
	const COMM_TYPE_BEFORE_TRIAL_FINISHES = 'before-membership-trial-finishes';
	const COMM_TYPE_INFO_UPDATE           = 'account-info-update';
	const COMM_TYPE_FAILED_PAYMENT        = 'payment-failed-payment';
	const COMM_TYPE_BEFORE_PAYMENT_DUE    = 'payment-due';
	const COMM_TYPE_AFTER_PAYMENT_DUE     = 'payment-overdue';

	/**
	 * List email types
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function email_types() {
		$admin_emails = array(
			'admin-' . self::COMM_TYPE_SIGNUP,
			'admin-' . self::COMM_TYPE_INVOICE,
		);
		$user_emails  = array(
			'member-' . self::COMM_TYPE_SIGNUP,
			'member-' . self::COMM_TYPE_REGISTRATION,
			'member-' . self::COMM_TYPE_REGISTRATION_VERIFY,
			'member-' . self::COMM_TYPE_RESETPASSWORD,
			'member-' . self::COMM_TYPE_RENEWED,
			'member-' . self::COMM_TYPE_INVOICE,
			'member-' . self::COMM_TYPE_BEFORE_FINISHES,
			'member-' . self::COMM_TYPE_FINISHED,
			'member-' . self::COMM_TYPE_AFTER_FINISHES,
			'member-' . self::COMM_TYPE_CANCELLED,
			'member-' . self::COMM_TYPE_BEFORE_TRIAL_FINISHES,
			'member-' . self::COMM_TYPE_INFO_UPDATE,
			'member-' . self::COMM_TYPE_FAILED_PAYMENT,
			'member-' . self::COMM_TYPE_BEFORE_PAYMENT_DUE,
			'member-' . self::COMM_TYPE_AFTER_PAYMENT_DUE,
		);
		return apply_filters(
			'hammock_email_types',
			array(
				'admin'  => $admin_emails,
				'member' => $user_emails,
			)
		);
	}

	/**
	 * Get email senders
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_email_senders() {
		return apply_filters( 'hammock_get_email_senders', array( 'admin' => array(), 'member' => array() ) );
	}
}

