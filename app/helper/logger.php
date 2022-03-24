<?php
namespace Hammock\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Logger
 * Handles all logs
 *
 * @since 1.0.0
 */
class Logger {

	/**
	 * If set to true or false it will override the WP_DEBUG value
	 * If set to null it will check the WP_DEBUG value
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $enabled = null;

	/**
	 * The single instance of the class
	 *
	 * @since 1.0.0
	 */
	protected static $_instance = null;


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
	 * Set up log directory
	 *
	 * @since 1.0.0
	 */
	public static function init_directory() {
		if ( ! is_dir( HAMMOCK_LOG_DIR ) ) {
			wp_mkdir_p( HAMMOCK_LOG_DIR );
		}

		$file_helper = new File();
		$file_helper->create_directory( HAMMOCK_LOG_DIR );
	}

	/**
	 * Resets all debug-output flags.
	 *
	 * @since  1.0.0
	 */
	public function reset() {
		$this->enabled = null;
	}

	/**
	 * Force-Enable debugging.
	 *
	 * @since  1.0.0
	 */
	public function enable() {
		$this->enabled = true;
	}

	/**
	 * Force-Disable debugging.
	 *
	 * @since  1.0.0
	 */
	public function disable() {
		$this->enabled = false;
	}

	/**
	 * Returns the debugging status. False means no debug output is made.
	 *
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$enabled = $this->enabled;
		$is_ajax = false;
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$is_ajax = true; }
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			$is_ajax = true; }

		if ( null === $enabled ) {
			if ( $is_ajax ) {
				$enabled = false;
			} else {
				$enabled = $this->wp_debug();
			}
		}

		return $enabled;
	}

	/**
	 * Do logging
	 *
	 * @param mixed <dynamic> Each param will be dumped
	 */
	public function log( $message ) {
		if ( $this->is_enabled() ) {
			$log_time = date( "Y-m-d\tH:i:s\t" );
			$log_file = date( 'Y-m-d' );
			$log_file = trailingslashit( HAMMOCK_LOG_DIR ) . $log_file . '_hammock.log';
			foreach ( func_get_args() as $param ) {
				if ( is_scalar( $param ) ) {
					$dump = $param;
				} else {
					$dump = var_export( $param, true );
				}
				error_log( $log_time . $dump . "\n", 3, $log_file );
			}
		}
	}

	/**
	 * Check if default debug is enabled
	 *
	 * @return bool
	 */
	public function wp_debug() {
		return ( defined( 'WP_DEBUG' ) && WP_DEBUG === true );
	}
}

