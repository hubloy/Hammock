<?php
namespace Hammock\Addon\Mailchimp;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Addon;

class Mailchimp extends Addon {

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
				'name'        => __( 'MailChimp Integration', 'hammock' ),
				'description' => __( 'MailChimp integration.', 'hammock' ),
				'icon'        => 'dashicons dashicons-email',
				'url'   	  => admin_url( 'admin.php?page=hammock-marketing' ),
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
		$view       = new \Hammock\View\Backend\Addons\Mailchimp();
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
		$apikey 		= sanitize_text_field( $data['apikey'] );
		$optin 			= isset( $data['double_optin'] );
		$reg_list 		= sanitize_text_field( $data['registered_list'] );
		$sub_list 		= sanitize_text_field( $data['subscriber_list'] );
		$unsub_list 	= sanitize_text_field( $data['unsubscriber_list'] );
		$settings  		= $this->settings();

		$settings['apikey'] 				= $apikey;
		$settings['double_optin'] 		= $optin;
		$settings['registered_list'] 		= $reg_list;
		$settings['subscriber_list'] 		= $sub_list;
		$settings['unsubscriber_list'] 	= $unsub_list;
		$this->settings->set_addon_setting( $this->id, $settings );
		$this->settings->save();
		return array(
			'status' => true,
			'message'	=> __( 'MailChimp Settings Saved', 'hammock' )
		);
	}
}

