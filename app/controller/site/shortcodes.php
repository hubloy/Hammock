<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Shortcode\Account;
use Hammock\Shortcode\Memberships;
use Hammock\Shortcode\Restricted;
use Hammock\Shortcode\Single;

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

		add_shortcode( 'hammock_membership_list', array( $this, 'membership_list' ) );
		add_shortcode( 'hammock_protected_content', array( $this, 'protected_content' ) );
		add_shortcode( 'hammock_account_page', array( $this, 'account_page' ) );
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
?>