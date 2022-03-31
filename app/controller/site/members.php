<?php
namespace HubloyMembership\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;

/**
 * Members controller
 * Manages all members
 *
 * @since 1.0.0
 */
class Members extends Controller {

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
	const MENU_SLUG = 'members';

	/**
	 * String translations
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $strings = array();

	/**
	 * Singletone instance of the controller.
	 *
	 * @since  1.0.0
	 *
	 * @var Members
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
	 * @return Members
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
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
			__( 'Members', 'hubloy-membership' ),
			__( 'Members', 'hubloy-membership' ),
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
		if ( $this->is_page( 'members' ) ) {
			$service                           = new \HubloyMembership\Services\Memberships();
			$vars['common']['string']['title'] = __( 'Members', 'hubloy-membership' );
			$vars['active_page']               = 'members';
			$vars['strings']                   = $this->get_strings();
			$vars['page_strings']              = array(
				'type'     => $service->list_simple_memberships(),
				'gateways' => \HubloyMembership\Services\Gateways::list_simple_gateways(),
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
			$this->strings = include HUBMEMB_LOCALE_DIR . '/site/members.php';
		}
		return $this->strings;
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hubloy_membership-members-react' );
	}

	/**
	 * Render view
	 *
	 * @return String
	 */
	public function render() {

		?>
		<div id="hubloy_membership-members-container"></div>
		<?php
	}
}
?>
