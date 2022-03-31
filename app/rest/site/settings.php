<?php
namespace HubloyMembership\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\Rest;
use HubloyMembership\Helper\Currency;
use HubloyMembership\Helper\Duration;
use HubloyMembership\Services\Addons;
use HubloyMembership\Helper\Pages;

/**
 * Settings rest route
 *
 * @since 1.0.0
 */
class Settings extends Rest {

	const BASE_API_ROUTE = '/settings/';

	/**
	 * Singletone instance of the rest route.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the rest route.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Settings
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set up the api routes
	 *
	 * @param String $namespace - the parent namespace
	 *
	 * @since 1.0.0
	 */
	public function set_up_route( $namespace ) {
		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'get',
			array(
				'method'              => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_settings' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'update',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'update_settings' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'pages',
			array(
				'method'              => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_pages' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'currencies',
			array(
				'method'              => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_currencies' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'days',
			array(
				'method'              => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_days' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'nav',
			array(
				'method'              => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_nav_items' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);
	}

	/**
	 * Get settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_settings( $request ) {
		$settings = new \HubloyMembership\Model\Settings();
		return $settings->get_general_settings();
	}

	/**
	 * Update settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_settings( $request ) {
		$content_protection   = isset( $request['content_protection'] ) ? intval( sanitize_text_field( $request['content_protection'] ) ) : 0;
		$admin_toolbar        = isset( $request['admin_toolbar'] ) ? intval( sanitize_text_field( $request['admin_toolbar'] ) ) : 0;
		$account_verification = isset( $request['account_verification'] ) ? intval( sanitize_text_field( $request['account_verification'] ) ) : 0;
		$currency             = sanitize_text_field( $request['membership_currency'] );
		$invoice_prefix       = sanitize_text_field( $request['invoice_prefix'] );
		$protection_level     = sanitize_text_field( $request['protection_level'] );

		// Pages
		$membership_list   = sanitize_text_field( $request['membership_list'] );
		$protected_content = sanitize_text_field( $request['protected_content'] );
		$account_page      = sanitize_text_field( $request['account_page'] );

		// Data
		$delete_on_uninstall = isset( $request['delete_on_uninstall'] ) ? intval( sanitize_text_field( $request['delete_on_uninstall'] ) ) : 0;

		$settings    = new \HubloyMembership\Model\Settings();
		$pages       = $settings->get_general_setting( 'pages', array() );
		$flush_rules = false;
		if ( $membership_list == 'c' ) {
			$page_id                  = Pages::create( 'membership_list' );
			$pages['membership_list'] = $page_id;
		} else {
			$pages['membership_list'] = $membership_list;
		}

		if ( $protected_content == 'c' ) {
			$page_id                    = Pages::create( 'protected_content' );
			$pages['protected_content'] = $page_id;
		} else {
			$pages['protected_content'] = $protected_content;
		}

		if ( $account_page == 'c' ) {
			$flush_rules           = true;
			$page_id               = Pages::create( 'account_page' );
			$pages['account_page'] = $page_id;
		} else {
			if ( isset( $pages['account_page'] ) && ( $pages['account_page'] !== $account_page ) ) {
				$flush_rules = true;
			}
			$pages['account_page'] = $account_page;
		}

		if ( $flush_rules ) {
			// Flush rules especially for new account pages to be set up well
			flush_rewrite_rules();
		}

		if ( $settings->get_general_setting( 'account_verification' ) !== $account_verification ) {
			$type                = \HubloyMembership\Services\Emails::COMM_TYPE_REGISTRATION_VERIFY;
			$verifcation_enabled = ( $account_verification === 1 );
			do_action( 'hubloy-membership_email_sender_member-' . $type, $verifcation_enabled );
		}

		$settings->set_general_setting( 'content_protection', $content_protection );
		$settings->set_general_setting( 'admin_toolbar', $admin_toolbar );
		$settings->set_general_setting( 'account_verification', $account_verification );
		$settings->set_general_setting( 'currency', $currency );
		$settings->set_general_setting( 'prefix', $invoice_prefix );
		$settings->set_general_setting( 'protection_level', $protection_level );
		$settings->set_general_setting( 'pages', $pages );
		$settings->set_general_setting( 'delete_on_uninstall', $delete_on_uninstall );
		$settings->save();

		return array(
			'status'  => true,
			'message' => __( 'Settings updated', 'hubloy-membership' ),
		);
	}

	/**
	 * Return an array of pages
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_pages( $request ) {
		$pages      = Pages::list_pages();
		$pages['c'] = __( 'Create New Page', 'hubloy-membership' );
		return $pages;
	}

	/**
	 * Get currencies
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_currencies( $request ) {
		return Currency::list_currencies();
	}

	/**
	 * List days
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_days( $request ) {
		return Duration::list_days();
	}

	/**
	 * Get the nav items
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_nav_items( $request ) {
		return Addons::addon_settings_links();
	}
}

