<?php
/**
 * Plugin Name:         Hammock
 * Plugin URI:          https://www.hammock-membership.com
 * Description:         Manage access to your WordPress site like a pro
 * Version:             1.0.0
 * Author:              Hubloy
 * Author URI:          https://www.hubloy.com
 * License:             GPLv2
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         hammock
 * Domain Path:         /languages/
 * 
 * @package Hammock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Hammock' ) ) :

	/**
	 * Main plugin class
	 *
	 * Main entry point of the plugin
	 * Definess variables and constants needed to run the plugin and loads
	 * the main plugin class
	 *
	 * @since 1.0.0
	 */
	final class Hammock {

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
			// Define plugin constants
			$this->define_constants();

			// Define autoloader
			$this->auto_load();

			// Initiate plugin
			\Hammock\Base\Plugin::instance();

			$this->query 	= new \Hammock\Core\Query();
			$this->api		= new \Hammock\Core\Api();

			if ( defined ( 'WP_CLI' ) && WP_CLI ) {
				//Load wp_cli
			}

			do_action( 'hammock_loaded' );
		}


		/**
		 * Define plugin constants
		 * Global constants used within the plugin if they are not already defined
		 *
		 * @since 1.0.0
		 */
		protected function define_constants() {
			$upload_dir = wp_upload_dir();
			$this->define( 'HAMMOCK_MENU_LOCATION', '55.5' );
			$this->define( 'HAMMOCK_REST_NAMESPACE', 'hammock/v1/' );
			$this->define( 'HAMMOCK_VERSION', $this->version );
			$this->define( 'HAMMOCK_UIKIT_VERSION', '3.2.6' );
			$this->define( 'HAMMOCK_DEBUG', true );
			$this->define( 'HAMMOCK_PLUGIN_FILE', __FILE__ );
			$this->define( 'HAMMOCK_PLUGIN', plugin_basename( __FILE__ ) );
			$this->define( 'HAMMOCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'HAMMOCK_PLUGIN_BASE_DIR', dirname( __FILE__ ) );
			$this->define( 'HAMMOCK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'HAMMOCK_LANG_DIR', HAMMOCK_PLUGIN_DIR . '/languages' );
			$this->define( 'HAMMOCK_TEMPLATE_DIR', HAMMOCK_PLUGIN_DIR . '/templates/' );
			$this->define( 'HAMMOCK_FUNCTIONS_DIR', HAMMOCK_PLUGIN_DIR . '/app/functions/' );
			$this->define( 'HAMMOCK_LIB_DIR', HAMMOCK_PLUGIN_DIR . '/lib/' );
			$this->define( 'HAMMOCK_LOCALE_DIR', HAMMOCK_PLUGIN_DIR . '/app/i18n' );
			$this->define( 'HAMMOCK_ASSETS_URL', HAMMOCK_PLUGIN_URL . 'assets' );
			$this->define( 'HAMMOCK_LOG_DIR', $upload_dir['basedir'] . '/hammock-logs/' );
			$this->define( 'HAMMOCK_LOG_URL', $upload_dir['baseurl'] . '/hammock-logs/' );
		}


		/**
		 * Define constant helper if not already set
		 *
		 * @param  string      $name
		 * @param  string|bool $value
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
		 * Set up the class loader
		 *
		 * @param $class
		 */
		public function _autoload( $class ) {
			$base_path = __DIR__ . DIRECTORY_SEPARATOR;
			$pools     = explode( '\\', $class );

			if ( $pools[0] != 'Hammock' ) {
				return;
			}

			$pools[0] = 'App';

			// build the path
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
	 * @return Hammock
	 */
	function hammock() {
		return Hammock::instance();
	}

	hammock();

endif;

