<?php
namespace Hammock\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Util;

/**
 * Settings model
 * This holds all application settings
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Settings
	 */
	private static $instance = null;

	/**
	 * General Settings
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $general = array();

	/**
	 * Gateway settings
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $gateways = array();


	/**
	 * Addon settings
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $addons = array();

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
		$option_key = $this->option_key();
		$settings   = Util::get_option( $option_key );
		$this->_import( $settings );
	}

	/**
	 * Import data to option
	 *
	 * @param array $data
	 */
	private function _import( $data ) {
		if ( $data ) {
			foreach ( $data as $key => $value ) {
				if ( $value ) {
					$value = maybe_unserialize( $value );
				}

				if ( null !== $value ) {
					$this->set_field( $key, $value );
				}
			}
		}
	}

	/**
	 * Set field value, bypassing the __set validation.
	 *
	 * Used for loading from db.
	 *
	 * @since  1.0.0
	 *
	 * @param string $field
	 * @param mixed  $value
	 */
	public function set_field( $field, $value ) {
		// Don't deserialize values of "private" fields.
		if ( '_' !== $field[0] ) {

			// Only set values of existing fields, don't create a new field.
			if ( property_exists( $this, $field ) ) {
				$this->$field = $value;
			}
		}
	}

	/**
	 * Plugin option key
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function option_key() {
		return 'hammock_settings';
	}

	/**
	 * Save Settings
	 *
	 * @since  1.0.0
	 */
	public function save() {
		$settings = array(
			'general'  => $this->general,
			'gateways' => $this->gateways,
			'addons'   => $this->addons,
		);
		Util::update_option( $this->option_key(), $settings );
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
	 * Get general settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_general_settings() {
		return wp_parse_args( $this->general, $this->default_general_settings() );
	}

	/**
	 * Get general setting
	 *
	 * @param String $key - the setting key
	 * @param object $default - the default value. Can be a String, Integer  or Boolean
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_general_setting( $key, $default = '' ) {
		$settings = $this->get_general_settings();
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		return $default;
	}



	/**
	 * Set general setting
	 *
	 * @param String $key -  the setting key
	 * @param mixed  $value - the value
	 *
	 * @since 1.0.0
	 */
	public function set_general_setting( $key, $value ) {
		$this->general[ $key ] = $value;
	}

	/**
	 * Get addon setting
	 *
	 * @param String $key - the setting key
	 * @param object $default - the default value. Can be a String, Integer  or Boolean
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_addon_setting( $key, $default = array( 'enabled' => false ) ) {
		$settings = $this->addons;
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		return $default;
	}



	/**
	 * Set addon setting
	 *
	 * @param String $key -  the setting key
	 * @param array  $value - the value
	 *
	 * @since 1.0.0
	 */
	public function set_addon_setting( $key, $value = array() ) {
		$this->addons[ $key ] = $value;
	}


	/**
	 * Get gateway setting
	 *
	 * @param String $key - the setting key
	 * @param object $default - the default value. Can be a String, Integer  or Boolean
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_gateway_setting( $key, $default = array( 'enabled' => false ) ) {
		$settings = $this->gateways;
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		return $default;
	}



	/**
	 * Set gateway setting
	 *
	 * @param String $key -  the setting key
	 * @param array  $value - the value
	 *
	 * @since 1.0.0
	 */
	public function set_gateway_setting( $key, $value = array() ) {
		$this->gateways[ $key ] = $value;
	}


	/**
	 * Default general settings
	 * Set the default settings in case none is configured
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	private function default_general_settings() {
		return array(
			'content_protection'   => 0,
			'admin_toolbar'        => 0,
			'account_verification' => 0,
			'currency'             => 'USD',
			'protection_level'     => 'hide',
			'prefix'               => 'HBM',
			'pages'                => array(),
			'delete_on_uninstall'  => 0,
		);
	}
}

