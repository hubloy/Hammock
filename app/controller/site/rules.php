<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;

/**
 * Rules controller
 * Manages all membership rules
 *
 * @since 1.0.0
 */
class Rules extends Controller {

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
	const MENU_SLUG = 'rules';


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
	 * @var Plugin
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
		$this->add_ajax_action( 'hammock_update_rule', 'update_rule' );
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
			__( 'Protection Rules', 'hammock' ),
			__( 'Protection Rules', 'hammock' ),
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
		if ( $this->is_page( 'rules' ) ) {
			$vars['common']['string']['title'] = __( 'Membership Rules', 'hammock' );
			$vars['active_page']               = 'rules';
			$vars['strings']                   = $this->get_strings();
			$vars['page_strings']              = array(
				'types' => $this->get_rules()
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
			$this->strings = include HAMMOCK_LOCALE_DIR . '/site/rules.php';
		}
		return $this->strings;
	}

	/**
	 * Get rules.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_rules() {
		return apply_filters( 'hammock_protection_rules', array() );
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hammock-rules-react' );
	}

	/**
	 * Render view
	 *
	 * @return string
	 */
	public function render() {

		?>
		<div id="hammock-rules-container"></div>
		<?php
	}

	/**
	 * Update rule
	 * 
	 * @since 1.0.0
	 */
	public function update_rule() {
		$this->verify_nonce();

		$id       = sanitize_text_field( $_POST['id'] );
		$item     = sanitize_text_field( $_POST['item'] );
		$selected = array_map( 'absint', $_POST['selected'] );
	}
}