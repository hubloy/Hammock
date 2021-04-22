<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\View\Backend\Wizard\Header;
use Hammock\View\Backend\Wizard\Footer;

/**
 * Wizard controller
 * Manages the setup wizard
 *
 * @since 1.0.0
 */
class Wizard extends Controller {

	/**
	 * Singletone instance of the controller.
	 *
	 * @since  1.0.0
	 *
	 * @var Shortcodes
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the controller.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Shortcodes
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
		if ( apply_filters( 'hammock_enable_setup_wizard', true ) && current_user_can( 'manage_options' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}


	/**
	 * Add admin menus/screens.
	 * 
	 * @since 1.0.0
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'hammock-setup', '' );
	}


	/**
	 * Show the setup wizard.
	 * 
	 * @since 1.0.0
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'hammock-setup' !== $_GET['page'] ) {
			return;
		}
		add_filter( 'hammock_load_admin_resouces', '__return_false' );
		$view = new Header();
		$view->render();

		$view = new Footer();
		$view->render();

		exit;
	}

	/**
	 * Set the scripts needed for the page
	 * 
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( !empty( $_GET['page'] ) && 'hammock-setup' === $_GET['page'] ) {
			wp_enqueue_script( 'hammock-uikit' );
			wp_enqueue_script( 'hammock-uikit-icons' );
			wp_enqueue_script( 'hammock-tiptip' );
			wp_enqueue_script( 'hammock-sweetalert' );
			// Date picker
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'hammock-jquery-tags' );
			wp_enqueue_script( 'hammock-admin' );

			wp_enqueue_style( 'hammock-uikit' );
			wp_enqueue_style( 'hammock-tiptip' );
			wp_enqueue_style( 'hammock-jquery-ui' );
			wp_enqueue_style( 'hammock-jquery-tags' );
			wp_enqueue_style( 'hammock-admin' );
		}
	}
}
?>