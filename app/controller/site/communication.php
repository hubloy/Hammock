<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Helper\Template;

/**
 * Communication controller
 * Manages all email communications
 *
 * @since 1.0.0
 */
class Communication extends Controller {

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
	const MENU_SLUG = 'comms';

	/**
	 * String translations
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $strings = array();

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Communication
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
	 * @return Communication
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
		$this->load_comms();

		$this->add_action( 'hammock_email_header', 'email_header' );
		$this->add_action( 'hammock_email_footer', 'email_footer' );

		$this->add_ajax_action( 'hammock_email_copy_theme', 'copy_theme' );
		$this->add_ajax_action( 'hammock_email_delete_theme', 'delete_theme' );
	}

	/**
	 * Load comms classes
	 * 
	 * @since 1.0.0
	 */
	private function load_comms() {
		//Load admin email types first
		\Hammock\Emails\Admin\Invoice::instance();
		\Hammock\Emails\Admin\Signup::instance();

		//Load user type emails
		\Hammock\Emails\Member\Account::instance();
		\Hammock\Emails\Member\Invoice::instance();
		\Hammock\Emails\Member\Registration::instance();
		\Hammock\Emails\Member\Reset::instance();
		\Hammock\Emails\Member\Verify::instance();

		\Hammock\Emails\Member\Membership\Cancelled::instance();
		\Hammock\Emails\Member\Membership\Renew::instance();
		\Hammock\Emails\Member\Membership\Signup::instance();
		\Hammock\Emails\Member\Membership\Trial::instance();

		\Hammock\Emails\Member\Membership\Payment\Due::instance();
		\Hammock\Emails\Member\Membership\Payment\Failed::instance();
		\Hammock\Emails\Member\Membership\Payment\Overdue::instance();

		\Hammock\Emails\Member\Membership\Status\After::instance();
		\Hammock\Emails\Member\Membership\Status\Before::instance();
		\Hammock\Emails\Member\Membership\Status\Finished::instance();

		do_action( 'hammock_load_comms' );
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
			__( 'Communication', 'hammock' ),
			__( 'Communication', 'hammock' ),
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
		if ( $this->is_page( 'comms' ) ) {
			$vars['common']['string']['title'] = __( 'Communication', 'hammock' );
			$vars['active_page']               = 'comms';
			$vars['strings']                   = $this->get_strings();
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
			$this->strings = include HAMMOCK_LOCALE_DIR . '/site/comms.php';
		}
		return $this->strings;
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hammock-comms-react' );
	}

	/**
	 * Render view
	 *
	 * @return String
	 */
	public function render() {

		?>
		<div id="hammock-comms-container"></div>
		<?php
	}


	/**
	 * Get the email header.
	 *
	 * @param mixed $email_heading Heading for the email.
	 * 
	 * @since 1.0.0
	 */
	public function email_header( $email_heading = '' ) {
		Template::get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 * 
	 * @since 1.0.0
	 */
	public function email_footer() {
		Template::get_template( 'emails/email-footer.php' );
	}

	/**
	 * Copy theme ajax action
	 * 
	 * @since 1.0.0
	 * 
	 * @return application/json
	 */
	public function copy_theme() {
		$this->verify_nonce( 'hammock_email_copy_theme' );
		$id = sanitize_text_field( $_POST['id'] );

		do_action( 'hammock_email_copy_theme_' . $id );

		wp_send_json_error( __( "Action not implemented", "hammock" ) );
	}

	
	/**
	 * Delete theme ajax action
	 * 
	 * @since 1.0.0
	 * 
	 * @return application/json
	 */
	public function delete_theme() {
		$this->verify_nonce( 'hammock_email_delete_theme' );
		$id = sanitize_text_field( $_POST['id'] );

		do_action( 'hammock_email_delete_theme_' . $id );

		wp_send_json_error( __( "Action not implemented", "hammock" ) );
	}
}
?>
