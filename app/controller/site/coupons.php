<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Model\Settings;
use Hammock\Services\Codes;

/**
 * Coupons controller
 *
 * Manages all coupons
 *
 * @since 1.0.0
 */
class Coupons extends Controller {

	/**
	 * Page id
	 * Used to create the sub pages
	 *
	 * @var string
	 */
	private $_page_id = '';


	/**
	 * Cap
	 * Current page cap
	 *
	 * @var string
	 */
	private $_cap = '';

	/**
	 * Plugin Menu slug.
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	const MENU_SLUG = 'coupons';

	/**
	 * String translations
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $strings = array();

	/**
	 * Setting object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $setting = null;

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Coupons
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
	 * @return Coupons
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Initialize controller
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$settings      = Settings::instance();
		$this->setting = $settings->get_addon_setting( 'coupons' );
	}

	/**
	 * Create the menu page
	 *
	 * @param string $slug - the parent menu slug
	 * @param string $cap - the menu capabilities
	 *
	 * @since 1.0.0
	 */
	public function menu_page( $slug, $cap ) {
		$this->_page_id = $slug . '-' . self::MENU_SLUG;
		$this->_cap     = $cap;
		$enabled        = isset( $this->setting['enabled'] ) ? $this->setting['enabled'] : false;
		if ( $enabled ) {
			add_submenu_page(
				$slug,
				__( 'Coupons', 'hammock' ),
				__( 'Coupons', 'hammock' ),
				$this->_cap,
				$this->_page_id,
				array( $this, 'render' )
			);
		}
	}

	/**
	 * Set up admin js variables
	 *
	 * @param array $vars
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function admin_js_vars( $vars ) {
		if ( $this->is_page( 'coupons' ) ) {
			$vars['common']['string']['title'] = __( 'Coupons', 'hammock' );
			$vars['active_page']               = 'coupons';
			$vars['strings']                   = $this->get_strings();
			$vars['page_strings']              = array(
				'status' => Codes::get_code_statuses(),
				'types'  => Codes::get_code_amount_types(),
			);
		}
		return $vars;
	}

	/**
	 * Get the strings
	 * This sets the strings if not defined
	 *
	 * @since 1.0.0
	 */
	private function get_strings() {
		if ( empty( $this->strings ) ) {
			$this->strings = include HAMMOCK_LOCALE_DIR . '/site/codes.php';
		}
		return $this->strings;
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hammock-coupons-react' );
	}

	/**
	 * Render view
	 *
	 * @return String
	 */
	public function render() {

		?>
		<div id="hammock-coupons-container"></div>
		<?php
	}
}
?>
