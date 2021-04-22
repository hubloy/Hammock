<?php
namespace Hammock\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Model\Settings;

class Addon extends Component {

	/**
	 * The addon id
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Setting object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $settings = null;

	/**
	 * Addon Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->settings = new Settings();
		$this->init();
		$this->add_filter( 'hammock_register_addons', 'register' );
		$this->add_filter( 'hammock_register_addon_setting_link', 'settings_link' );
		$this->add_filter( 'hammock_get_addon_' . $this->id . '_active', 'plugin_active' );
		if ( $this->is_enabled() ) {
			$this->add_action( 'hammock_init_addon', 'init_addon' );
		}
		$this->add_filter( 'hammock_addon_' . $this->id . '_settings', 'settings_page' );
		$this->add_filter( 'hammock_addon_' . $this->id . '_update_settings', 'update_settings', 10, 2 );
	}

	/**
	 * Addon init
	 */
	public function init() {

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

		return $addons;
	}


	/**
	 * This is to register nav links for addons that will have settings in the Settings page
	 * The addon must be registered as follows
	 *
	 * array(
	 *          'name'          => __( 'Addon Name', 'hammock' ),
	 *          'description'   => __( 'Addon Description', 'hammock' ),
	 *          'icon'          => 'icon-class',
	 *          'configure'     => true
	 *      );
	 *
	 * The `configure` option is required
	 *
	 * @param array $links - the current list of addon links
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function settings_link( $links ) {

		return $links;
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
	 * @return array
	 */
	public function settings() {
		$settings = $this->settings->get_addon_setting( $this->id );
		return $settings;
	}

	/**
	 * The addon settings page
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function settings_page() {
		return '';
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
		return $response;
	}

	/**
	 * Check if the addon is enabled
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$settings 	= $this->settings();
		$enabled 	=  isset( $settings['enabled'] ) ? $settings['enabled'] : false;
		if ( $enabled  ) {
			return $this->plugin_active();
		}
		return $enabled;
	}

	public function plugin_active() {
		return true;
	}
}

