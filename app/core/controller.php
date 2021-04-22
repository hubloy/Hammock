<?php
namespace Hammock\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin controller loader
 *
 * @since 1.0.0
 */
class Controller {

	/**
	 * Load controllers
	 *
	 * @since 1.0.0
	 */
	public static function load_controllers() {
		\Hammock\Controller\Site\Users::instance();
		\Hammock\Controller\Site\Addon::instance();
		\Hammock\Controller\Site\Wizard::instance();
		\Hammock\Controller\Site\Members::instance();
		\Hammock\Controller\Site\Gateway::instance();
		\Hammock\Controller\Site\Coupons::instance();
		\Hammock\Controller\Site\Invites::instance();
		\Hammock\Controller\Site\Settings::instance();
		\Hammock\Controller\Site\Activity::instance();
		\Hammock\Controller\Site\Shortcodes::instance();
		\Hammock\Controller\Site\Memberships::instance();
		\Hammock\Controller\Site\Transactions::instance();
		\Hammock\Controller\Site\Communication::instance();

		\Hammock\Controller\Front\Auth::instance();
		\Hammock\Controller\Front\Signup::instance();
		\Hammock\Controller\Front\Account::instance();
		\Hammock\Controller\Front\Transaction::instance();
	}

	/**
	 * Load rest routes
	 *
	 * @since 1.0.0
	 */
	public static function load_routes() {
		\Hammock\Rest\Site\Codes::instance();
		\Hammock\Rest\Site\Emails::instance();
		\Hammock\Rest\Site\Addons::instance();
		\Hammock\Rest\Site\Members::instance();
		\Hammock\Rest\Site\Activity::instance();
		\Hammock\Rest\Site\Settings::instance();
		\Hammock\Rest\Site\Gateways::instance();
		\Hammock\Rest\Site\Memberships::instance();
		\Hammock\Rest\Site\Transactions::instance();
	}
}

