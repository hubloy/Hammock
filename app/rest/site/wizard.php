<?php
namespace HubloyMembership\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\Rest;
use HubloyMembership\Core\Util;
use HubloyMembership\Helper\Pages;

/**
 * Wizard rest route
 *
 * @since 1.0.0
 */
class Wizard extends Rest {

	const BASE_API_ROUTE = '/wizard/';

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
	 * @param string $namespace - the parent namespace
	 *
	 * @since 1.0.0
	 */
	public function set_up_route( $namespace ) {
		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'step',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_step' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'settings',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'update_settings' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'membership',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_membership' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);
	}


	/**
	 * Get the current wizard step
	 * Used if someone has saved a step and would like to go to the next step
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_step( $request ) {
		$step = Util::get_option( 'hubloy_membership_wizard_step' );
		if ( ! $step || ! is_array( $step ) ) {
			$step = array(
				'value' => 10,
				'step'  => 'options',
			);
		}
		return rest_ensure_response( $step );
	}

	/**
	 * Update the basic settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_settings( $request ) {
		$currency          = sanitize_text_field( $request['membership_currency'] );
		$membership_list   = sanitize_text_field( $request['membership_list'] );
		$protected_content = sanitize_text_field( $request['protected_content'] );
		$account_page      = sanitize_text_field( $request['account_page'] );

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

		$settings->set_general_setting( 'currency', $currency );
		$settings->set_general_setting( 'pages', $pages );
		$settings->save();

		$stage = array(
			'value' => 50,
			'step'  => 'membership',
		);

		// Update wizard step
		Util::update_option( 'hubloy_membership_wizard_step', $stage );

		$code = \HubloyMembership\Helper\Currency::get_membership_currency();

		return array(
			'status'   => true,
			'message'  => __( 'Settings updated', 'memberships-by-hubloy' ),
			'data'     => $stage,
			'currency' => esc_html( $code ),
		);
	}

	/**
	 * Save initial membership
	 * Saves and takes the user to the edit page
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function save_membership( $request ) {
		$name     = sanitize_text_field( $request['membership_name'] );
		$enabled  = isset( $request['membership_enabled'] ) ? intval( sanitize_text_field( $request['membership_enabled'] ) ) : 0;
		$type     = sanitize_text_field( $request['membership_type'] );
		$days     = false;
		$end      = false;
		$duration = false;
		$price    = sanitize_text_field( $request['membership_price'] );
		if ( $type === 'date-range' ) {
			$days = sanitize_text_field( $request['membership_days'] );
		} elseif ( $type === 'recurring' ) {
			$duration = sanitize_text_field( $request['recurring_duration'] );
		}

		$service = new \HubloyMembership\Services\Memberships();
		$id      = $service->save( $name, '', $enabled, $type, $price );
		if ( $id ) {

			Util::update_option( 'hubloy_membership_installed', 1 );

			// Update wizard step
			Util::update_option(
				'hubloy_membership_wizard_step',
				array(
					'value' => 100,
					'step'  => 'membership',
				)
			);

			if ( $days ) {
				$service->save_meta( $id, 'membership_days', $days );
			}

			if ( $duration ) {
				$service->update_duration( $id, $duration );
			}

			return array(
				'status'  => true,
				'message' => __( 'Membership Saved', 'memberships-by-hubloy' ),
			);
		} else {
			return array(
				'status'  => false,
				'message' => __( 'Error saving membership', 'memberships-by-hubloy' ),
			);
		}

	}
}
