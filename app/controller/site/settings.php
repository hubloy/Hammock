<?php
namespace HubloyMembership\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;
use HubloyMembership\Core\Admin;
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
	 * The admin core object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $admin = null;

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
		$this->admin = new Admin();
		// Ajax to create missing pages
		$this->add_ajax_action( 'hubloy_membership_settings_create_page', 'create_page' );
		$this->add_filter( 'hubloy_membership_admin_register_setting_sub_page', 'register_setting_page' );
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
			__( 'Settings', 'memberships-by-hubloy' ),
			__( 'Settings', 'memberships-by-hubloy' ),
			$this->_cap,
			$this->_page_id,
			array( $this, 'render' )
		);
	}


	/**
	 * Get the setting sub-pages
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function setting_pages() {
		return apply_filters( 'hubloy_membership_admin_register_setting_sub_page', $this->admin->get_setting_pages() );
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
	public function admin_js_vars( $vars ) {
		if ( $this->is_page( 'settings' ) ) {
			$vars['common']['string']['title'] = __( 'Settings', 'memberships-by-hubloy' );
			$vars['active_page']               = 'settings';
			$vars['strings']                   = $this->get_strings();
			$vars['page_strings']              = array(
				'tabs' => array(
					'general'  => __( 'General', 'memberships-by-hubloy' ),
					'gateways' => __( 'Gateways', 'memberships-by-hubloy' ),
				),

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
			$this->strings = include HUBMEMB_LOCALE_DIR . '/site/settings.php';
		}
		return $this->strings;
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hubloy_membership-settings-react' );
	}

	/**
	 * Render view
	 *
	 * @return String
	 */
	public function render() {

		?>
		<div id="hubloy_membership-settings-container"></div>
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
	public function create_page() {
		$this->verify_nonce();
		$page_id = sanitize_text_field( $_POST['page_id'] );
	}

	/**
	 * Register setting sub page
	 *
	 * @param array $args
	 *
	 * @since 1.0.0
	 */
	public function register_setting_page( $args ) {
		return $this->admin->register_setting_sub_page( $args );
	}
}

