<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;

/**
 * Settings controller
 * Manages all settings
 *
 * @since 1.0.0
 */
class Settings extends Controller {

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
	const MENU_SLUG = 'settings';

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * String translations
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $strings = array();

	/**
	 * Returns singleton instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Plugin
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
		// Ajax to create missing pages
		$this->add_ajax_action( 'hammock_settings_create_page', 'create_page' );
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
		add_submenu_page(
			$slug,
			__( 'Settings', 'hammock' ),
			__( 'Settings', 'hammock' ),
			$this->_cap,
			$this->_page_id,
			array( $this, 'render' )
		);
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
		if ( $this->is_page( 'settings' ) ) {
			$vars['common']['string']['title'] = __( 'Settings', 'hammock' );
			$vars['active_page']               = 'settings';
			$vars['strings']                   = $this->get_strings();
			$vars['page_strings']              = array(
				'tabs' => array(
					'general'  => __( 'General', 'hammock' ),
					'gateways' => __( 'Gateways', 'hammock' ),
				)
				
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
			$this->strings = include HAMMOCK_LOCALE_DIR . '/site/settings.php';
		}
		return $this->strings;
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hammock-settings-react' );
	}

	/**
	 * Render view
	 *
	 * @return String
	 */
	public function render() {

		?>
		<div id="hammock-settings-container"></div>
		<?php
	}

	/**
	 * Create page
	 * This creates any missing pages
	 *
	 * @since 1.0.0
	 *
	 * @return application/json
	 */
	function create_page() {
		$this->verify_nonce();
		$page_id = sanitize_text_field( $_POST['page_id'] );
	}
}
?>
