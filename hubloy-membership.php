<?php
/**
 * Plugin Name:         HubloyMembership
 * Plugin URI:          https://www.hubloymembership.com
 * Description:         Manage access to your WordPress site like a pro
 * Version:             1.0.0
 * Author:              Hubloy
 * Author URI:          https://www.hubloy.com
 * License:             GPLv2
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         hubloy-membership
 * Domain Path:         /languages/
 *
 * @package HubloyMembership
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'HubloyMembership' ) ) :

	/**
	 * Main plugin class
	 *
	 * Main entry point of the plugin
	 * Definess variables and constants needed to run the plugin and loads
	 * the main plugin class
	 *
	 * @since 1.0.0
	 */
	final class HubloyMembership {

		/**
		 * Current plugin version.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * The single instance of the class
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		protected static $_instance = null;

		/**
		 * The query object
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		private $query = null;


		/**
		 * The api object
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		private $api = null;

		/**
		 * Get the instance
		 *
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Main plugin constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// Define plugin constants.
			$this->define_constants();

			// Define autoloader.
			$this->auto_load();

			// Initiate plugin.
			\HubloyMembership\Base\Plugin::instance();

			$this->query = new \HubloyMembership\Core\Query();
			$this->api   = new \HubloyMembership\Core\Api();

			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				// Load wp_cli
			}

			do_action( 'hubloy-membership_loaded' );
		}


		/**
		 * Define plugin constants
		 * Global constants used within the plugin if they are not already defined
		 *
		 * @since 1.0.0
		 */
		protected function define_constants() {
			$upload_dir = wp_upload_dir();
			$this->define( 'HUBMEMB_MENU_LOCATION', '55.5' );
			$this->define( 'HUBMEMB_REST_NAMESPACE', 'hubloy-membership/v1/' );
			$this->define( 'HUBMEMB_VERSION', $this->version );
			$this->define( 'HUBMEMB_UIKIT_VERSION', '3.2.6' );
			$this->define( 'HUBMEMB_DEBUG', true );
			$this->define( 'HUBMEMB_PLUGIN_FILE', __FILE__ );
			$this->define( 'HUBMEMB_PLUGIN', plugin_basename( __FILE__ ) );
			$this->define( 'HUBMEMB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'HUBMEMB_PLUGIN_BASE_DIR', dirname( __FILE__ ) );
			$this->define( 'HUBMEMB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'HUBMEMB_LANG_DIR', HUBMEMB_PLUGIN_DIR . '/languages' );
			$this->define( 'HUBMEMB_TEMPLATE_DIR', HUBMEMB_PLUGIN_DIR . '/templates/' );
			$this->define( 'HUBMEMB_FUNCTIONS_DIR', HUBMEMB_PLUGIN_DIR . '/app/functions/' );
			$this->define( 'HUBMEMB_LIB_DIR', HUBMEMB_PLUGIN_DIR . '/lib/' );
			$this->define( 'HUBMEMB_LOCALE_DIR', HUBMEMB_PLUGIN_DIR . '/app/i18n' );
			$this->define( 'HUBMEMB_ASSETS_URL', HUBMEMB_PLUGIN_URL . 'assets' );
			$this->define( 'HUBMEMB_LOG_DIR', $upload_dir['basedir'] . '/hubloy-membership-logs/' );
			$this->define( 'HUBMEMB_LOG_URL', $upload_dir['baseurl'] . '/hubloy-membership-logs/' );
		}


		/**
		 * Define constant helper if not already set
		 *
		 * @param string      $name The name.
		 * @param string|bool $value The value.
		 *
		 * @since 1.0.0
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}


		/**
		 * Load plugin
		 *
		 * @since 1.0.0
		 */
		private function auto_load() {
			spl_autoload_register( array( &$this, '_autoload' ) );
		}


		/**
		 * Set up the class loader.
		 *
		 * @param string $class The class name.
		 */
		public function _autoload( $class ) {
			$base_path = __DIR__ . DIRECTORY_SEPARATOR;
			$pools     = explode( '\\', $class );

			if ( 'HubloyMembership' !== $pools[0] ) {
				return;
			}

			$pools[0] = 'App';

			// build the path.
			$path = implode( DIRECTORY_SEPARATOR, $pools );
			$path = $base_path . strtolower( str_replace( '_', '-', $path ) ) . '.php';
			if ( file_exists( $path ) ) {
				include_once $path;
			}
		}

		/**
		 * Get query
		 *
		 * @since 1.0.0
		 *
		 * @return object
		 */
		public function get_query() {
			return $this->query;
		}


		/**
		 * Get api
		 *
		 * @since 1.0.0
		 *
		 * @return object
		 */
		public function get_api() {
			return $this->api;
		}
	}

	/**
	 * Global function
	 *
	 * @since 1.0.0
	 *
	 * @return HubloyMembership
	 */
	function hubloy-membership() {
		return HubloyMembership::instance();
	}

	hubloy-membership();

endif;

