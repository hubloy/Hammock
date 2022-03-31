<?php
namespace HubloyMembership\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Core\Util;

/**
 * Email settings model
 *
 * @since 1.0.0
 */
class Email {

	/**
	 * Email Settings
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Email
	 */
	private static $instance = null;

	/**
	 * Get the instance
	 *
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Main constructor
	 *
	 * Load and assign options
	 */
	public function __construct() {
		$this->_load();
	}

	/**
	 * Load model
	 *
	 * @since 1.0.0
	 */
	private function _load() {
		$option_key     = $this->option_key();
		$this->settings = Util::get_option( $option_key );
	}

	/**
	 * Plugin option key
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function option_key() {
		return apply_filters( 'hubloy_membership_email_settings_name_key', 'hubloy_membership_email_settings' );
	}

	/**
	 * Save Settings
	 *
	 * @since  1.0.0
	 */
	public function save() {
		Util::update_option( $this->option_key(), $this->settings );
	}

	/**
	 * Reads the options from options table
	 *
	 * @since  1.0.0
	 */
	public function refresh() {
		$this->_load();
	}

	/**
	 * Get setting
	 *
	 * @param String $key - the setting key
	 * @param array  $default - the default value
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_setting( $key, $default = array(
		'recipient',
		'subject',
		'enabled' => false,
	) ) {
		$settings = $this->settings;
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		return $default;
	}



	/**
	 * Set setting
	 *
	 * @param String $key -  the setting key
	 * @param array  $value - the value
	 *
	 * @since 1.0.0
	 */
	public function set_setting( $key, $value ) {
		$this->settings[ $key ] = $value;
	}
}

