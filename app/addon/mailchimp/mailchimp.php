<?php
namespace HubloyMembership\Addon\Mailchimp;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Addon;

class Mailchimp extends Addon {

	/**
	 * The API instance
	 *
	 * @since 1.0.0
	 *
	 * @var API
	 */
	private $api = null;

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
	 * Addon init
	 */
	public function init() {
		$this->id = 'mailchimp';
	}

	/**
	 * Register addon
	 * Register a key value pair of addons
	 *
	 * @param array $addons - the current list of addons
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register( $addons ) {
		if ( ! isset( $addons['mailchimp'] ) ) {
			$addons['mailchimp'] = array(
				'name'        => __( 'MailChimp Integration', 'memberships-by-hubloy' ),
				'description' => __( 'MailChimp integration.', 'memberships-by-hubloy' ),
				'icon'        => 'dashicons dashicons-email',
				'url'         => admin_url( 'admin.php?page=memberships-by-hubloy-marketing' ),
			);
		}
		return $addons;
	}

	/**
	 * Initialize the addon action
	 *
	 * @since 1.0.0
	 */
	public function init_addon() {

	}

	/**
	 * Addon settings
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function settings_page() {
		$view       = new \HubloyMembership\View\Backend\Addons\Mailchimp();
		$settings   = $this->settings();
		$view->data = array(
			'settings' => $settings,
		);
		return $view->render( true );
	}


	/**
	 * Update addon settings
	 *
	 * @param array $response
	 * @param array $data - the post data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_settings( $response = array(), $data ) {
		$apikey     = sanitize_text_field( $data['apikey'] );
		$enabled    = isset( $data['enabled'] ) ? 1 : 0;
		$optin      = isset( $data['double_optin'] ) ? 1 : 0;
		$reg_list   = sanitize_text_field( $data['registered_list'] );
		$sub_list   = sanitize_text_field( $data['subscriber_list'] );
		$unsub_list = sanitize_text_field( $data['unsubscriber_list'] );
		$settings   = $this->settings();

		$settings['enabled']           = $enabled;
		$settings['apikey']            = $apikey;
		$settings['double_optin']      = $optin;
		$settings['registered_list']   = $reg_list;
		$settings['subscriber_list']   = $sub_list;
		$settings['unsubscriber_list'] = $unsub_list;
		$this->settings->set_addon_setting( $this->id, $settings );
		$this->settings->save();
		return array(
			'status'  => true,
			'message' => __( 'MailChimp Settings Saved', 'memberships-by-hubloy' ),
		);
	}


	/**
	 * Used to handle custom addon actions
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function addon_action( $response = array(), $data ) {
		$action = sanitize_text_field( $data['action'] );
		switch ( $action ) {
			case 'check_status':
				$apikey      = sanitize_text_field( $data['apikey'] );
				$exploded    = explode( '-', $apikey );
				$data_center = end( $exploded );
				$this->api   = new Api( $apikey, $data_center );
				$lists       = $this->get_lists();
				if ( is_wp_error( $lists ) ) {
					return array(
						'error'   => true,
						'message' => sprintf( __( 'Error: %s', 'memberships-by-hubloy' ), $lists->get_error_message() ),
					);
				}
				// Set the api key
				$settings                 = $this->settings();
				$settings['apikey']       = $apikey;
				$settings['valid']        = true;
				$settings['double_optin'] = true;
				$this->settings->set_addon_setting( $this->id, $settings );
				$this->settings->save();

				return array(
					'success' => true,
					'message' => __( 'Valid API key', 'memberships-by-hubloy' ),
					'lists'   => $lists,
				);
			break;
			case 'get_lists':
				$settings    = $this->settings();
				$apikey      = $settings['apikey'];
				$exploded    = explode( '-', $apikey );
				$data_center = end( $exploded );
				$this->api   = new Api( $apikey, $data_center );
				$lists       = $this->get_lists();
				if ( is_wp_error( $lists ) ) {
					return array(
						'error'   => true,
						'message' => sprintf( __( 'Error: %s', 'memberships-by-hubloy' ), $lists->get_error_message() ),
					);
				} else {
					return array(
						'success' => true,
						'lists'   => $lists,
					);
				}
				break;
		}
		return array(
			'success' => true,
			'message' => __( 'Action executed', 'memberships-by-hubloy' ),
		);
	}

	/**
	 * Get lists
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_lists( $refresh = false ) {
		$response = $this->api->get_lists();
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$lists  = array(
			'' => __( 'Select List', 'memberships-by-hubloy' ),
		);
		$_lists = $response->lists;
		$total  = $response->total_items;
		if ( is_array( $_lists ) ) {
			foreach ( $_lists as $list ) {
				$list                 = (array) $list;
				$lists[ $list['id'] ] = $list['name'];
			}
		}
		return $lists;
	}
}

