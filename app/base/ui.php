<?php
namespace Hammock\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base ui
 * Ui base class
 *
 * @since 1.0.0
 *
 * @package JP
 */
class UI extends Component {

	/**
	 * Base ui directory
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	protected $base = 'default';


	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var UI
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return UI
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new UI();
		}

		return self::$instance;
	}

	/**
	 * Handles ui render
	 *
	 * @param string $file - file relative to the base in the ui path
	 * @param array  $params - params to pass to the ui element
	 * @param bool   $return - set to false to echo and true to return
	 *
	 * @return void|string
	 */
	public function render( $file, $params = array(), $return = false ) {

		if ( array_key_exists( 'this', $params ) ) {
			unset( $params['this'] );
		}

		extract( $params, EXTR_OVERWRITE );

		if ( $return ) {
			ob_start();
		}
		$ui_file       = $file;
		$template_file = join( DIRECTORY_SEPARATOR, array( untrailingslashit( HAMMOCK_PLUGIN_DIR ), 'app', 'ui', $this->base, $ui_file . '.php' ) );
		if ( file_exists( $template_file ) ) {
			include $template_file;
		}

		if ( $return ) {
			return ob_get_clean();
		}

		if ( ! empty( $params ) ) {
			foreach ( $params as $param ) {
				unset( $param );
			}
		}
	}
}

