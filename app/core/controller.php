<?php
namespace HubloyMembership\Core;

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
		\HubloyMembership\Controller\Site\Users::instance();
		\HubloyMembership\Controller\Site\Memberships::instance();
		\HubloyMembership\Controller\Site\Members::instance();
		\HubloyMembership\Controller\Site\Addon::instance();
		\HubloyMembership\Controller\Site\Rules::instance();
		\HubloyMembership\Controller\Site\Gateway::instance();
		\HubloyMembership\Controller\Site\Coupons::instance();
		\HubloyMembership\Controller\Site\Invites::instance();
		\HubloyMembership\Controller\Site\Settings::instance();
		\HubloyMembership\Controller\Site\Activity::instance();
		// \HubloyMembership\Controller\Site\Marketing::instance();
		\HubloyMembership\Controller\Site\Shortcodes::instance();

		\HubloyMembership\Controller\Site\Transactions::instance();
		\HubloyMembership\Controller\Site\Communication::instance();

		\HubloyMembership\Controller\Front\Auth::instance();
		\HubloyMembership\Controller\Front\Signup::instance();
		\HubloyMembership\Controller\Front\Account::instance();
		\HubloyMembership\Controller\Front\Template::instance();
		\HubloyMembership\Controller\Front\Transaction::instance();
	}

	/**
	 * Load rest routes
	 *
	 * @since 1.0.0
	 */
	public static function load_routes() {
		\HubloyMembership\Rest\Site\Codes::instance();
		\HubloyMembership\Rest\Site\Rules::instance();
		\HubloyMembership\Rest\Site\Wizard::instance();
		\HubloyMembership\Rest\Site\Emails::instance();
		\HubloyMembership\Rest\Site\Addons::instance();
		\HubloyMembership\Rest\Site\Members::instance();
		\HubloyMembership\Rest\Site\Activity::instance();
		\HubloyMembership\Rest\Site\Settings::instance();
		\HubloyMembership\Rest\Site\Gateways::instance();
		\HubloyMembership\Rest\Site\Dashboard::instance();
		\HubloyMembership\Rest\Site\Memberships::instance();
		\HubloyMembership\Rest\Site\Transactions::instance();
	}
}

