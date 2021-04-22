<?php
namespace Hammock\Addon\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Addon;
use Hammock\Core\Util;

class Woocommerce extends Addon {

	/**
	 * Singletone instance of the addon.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the addon.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Addon init
	 */
	public function init() {
		$this->id = 'woocommerce';
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
		if ( ! isset( $addons['woocommerce'] ) ) {
			$active = $this->plugin_active();
			$addons['woocommerce'] = array(
				'name'        => __( 'WooCommerce', 'hammock' ),
				'description' => $active ? __( 'Sell and activate Memberships via WooCommerce', 'hammock' ) : __( 'Insatall and active WooCommerce to use this addon', 'hammock' ),
				'icon'        => 'dashicons dashicons-cart',
				'configure'   => true,
			);
		}
		return $addons;
	}

	/**
	 * Initialize the addon action
	 *
	 * @since 1.0.0
	 */
	public function init_addon() {
		//Meta box for WooCommerce
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Add protection meta box
	 * 
	 * @since 1.0.0
	 */
	public function add_meta_box() {
		global $post;

		// sanity check
		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		$screen 	= get_current_screen();
		$screens 	= array( 'product' );

		if ( ! $screen || ! in_array( $screen->id, $screens, true ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'page' == $post->post_type && Pages::is_membership_page( $post->ID ) ) {
			return;
		}

		add_meta_box(
			'hammock-content-protection',
			__( 'Memberships', 'hammock' ),
			array( $this, 'render_meta_box' ),
			$screen->id,
			'normal',
			'default'
		);
	}

	public function render_meta_box( $post ) {
		$product = wc_get_product( $post );
	}

	/**
	 * Check if WooCommerce is active
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function plugin_active() {
		return Util::plugin_active( 'woocommerce/woocommerce.php' );
	}
}

