<?php
namespace HubloyMembership\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;

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
		$this->add_ajax_action( 'hubloy_membership_rule_items', 'rule_items' );
		$this->add_filter( 'hubloy_membership_rule_type_name', 'get_rule_name' );
		$this->add_filter( 'hubloy_membership_rule_title_name', 'get_rule_title', 10, 2 );
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
			__( 'Protection Rules', 'hubloy-membership' ),
			__( 'Protection Rules', 'hubloy-membership' ),
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
			$vars['common']['string']['title'] = __( 'Membership Rules', 'hubloy-membership' );
			$vars['active_page']               = 'rules';
			$vars['strings']                   = $this->get_strings();
			$vars['page_strings']              = array(
				'types' => $this->get_rules(),
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
			$this->strings = include HUBMEMB_LOCALE_DIR . '/site/rules.php';
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
		return apply_filters( 'hubloy_membership_protection_rules', array() );
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hubloy_membership-rules-react' );
	}

	/**
	 * Render view
	 *
	 * @return string
	 */
	public function render() {

		?>
		<div id="hubloy_membership-rules-container"></div>
		<?php
	}

	/**
	 * Get Rule items
	 *
	 * @since 1.0.0
	 */
	public function rule_items() {
		$this->verify_nonce( 'hubloy_membership_rule_items', 'GET' );
		$type     = sanitize_text_field( $_GET['type'] );
		$term     = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';
		$service  = new \HubloyMembership\Services\Rules();
		$response = $service->search_rule_items( $type, $term );
		wp_send_json_success( $response );
	}

	/**
	 * Get rule type
	 */
	public function get_rule_name( $type ) {
		$service = new \HubloyMembership\Services\Rules();
		$rule    = $service->get_rule_by_type( $type );
		if ( ! $rule ) {
			return $type;
		}
		return $rule->get_name();
	}

	/**
	 * Get rule title
	 */
	public function get_rule_title( $type, $id ) {
		$service = new \HubloyMembership\Services\Rules();
		$rule    = $service->get_rule_by_type( $type );
		if ( ! $rule ) {
			return $type;
		}
		return $rule->get_protected_item_name( $id, true );
	}
}
