<?php
namespace HubloyMembership\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;
use HubloyMembership\Services\Addons;

/**
 * Addon controller
 * Manages all addons
 *
 * @since 1.0.0
 */
class Addon extends Controller {

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
	const MENU_SLUG = 'addons';

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Addon
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
	 * @return Addon
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
		$this->load_addons();
		$this->init_addons();

		$this->add_ajax_action( 'hubloy_membership_addon_settings', 'addon_settings' );
	}

	/**
	 * Load addons
	 * This loads all addon classes
	 *
	 * @since 1.0.0
	 */
	public function load_addons() {
		\HubloyMembership\Addon\Category\Category::instance();

		do_action( 'hubloy_membership_load_addons' );
	}

	/**
	 * Initializes the addons
	 *
	 * @since 1.0.0
	 */
	private function init_addons() {
		// Action called by addon to initiate it
		do_action( 'hubloy_membership_init_addon' );
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
			__( 'Addons', 'hubloy-membership' ),
			__( 'Addons', 'hubloy-membership' ),
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
		if ( $this->is_page( 'addons' ) ) {
			$vars['common']['string']['title'] = __( 'Addons', 'hubloy-membership' );
			$vars['active_page']               = 'addon';
		}
		return $vars;
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hubloy_membership-addons-react' );
	}

	/**
	 * Render view
	 *
	 * @return String
	 */
	public function render() {
		?>
		<div id="hubloy_membership-addon-container"></div>
		<?php
	}

	/**
	 * Get addon settings
	 *
	 * @since 1.0.0
	 */
	function addon_settings() {
		$this->verify_nonce();

		$addon_id = sanitize_text_field( $_POST['id'] );

		$view = apply_filters( 'hubloy_membership_addon_' . $addon_id . '_settings', __( 'Not implemented', 'hubloy-membership' ) );
		wp_send_json_success(
			array(
				'view' => $view,
			)
		);
	}
}
?>
