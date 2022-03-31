<?php
namespace HubloyMembership\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;
use HubloyMembership\Shortcode\Account;
use HubloyMembership\Shortcode\Memberships;
use HubloyMembership\Shortcode\Restricted;
use HubloyMembership\Shortcode\Single;

/**
 * Shortcodes controller
 *
 * Holds all shortcodes used within the plugin
 *
 * @since 1.0.0
 */
class Shortcodes extends Controller {

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

		add_shortcode( 'hubloy_membership_membership_list', array( $this, 'membership_list' ) );
		add_shortcode( 'hubloy_membership_protected_content', array( $this, 'protected_content' ) );
		add_shortcode( 'hubloy_membership_account_page', array( $this, 'account_page' ) );
	}

	/**
	 * Membership list content shortcode
	 *
	 * @param array $atts - user defined attributes
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function membership_list( $atts ) {
		$output = Memberships::instance();
		return $output->render( $atts );
	}

	/**
	 * Protected content shortcode
	 *
	 * @param array $atts - user defined attributes
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function protected_content( $atts ) {
		$output = Restricted::instance();
		return $output->render( $atts );
	}


	/**
	 * Member account page shortcode
	 *
	 * @param array $atts - user defined attributes
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function account_page( $atts ) {
		$output = Account::instance();
		return $output->render( $atts );
	}
}

