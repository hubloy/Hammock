<?php
namespace Hammock\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Core\Admin;

/**
 * Base controller class
 * All controllers must extend this class
 *
 * @since 1.0.0
 *
 * @package JP
 */
class Controller extends Component {

	/**
	 * Plugin Menu slug.
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	const MENU_SLUG = 'hammock';

	/**
	 * If is base controller
	 * Set to true removed the call to register menu
	 *
	 * @since 1.0.0
	 */
	protected $is_base = false;

	/**
	 * If is a sub page
	 * Always defaults to true
	 * 
	 * @since 1.0.0
	 */
	protected $is_sub_page = true;

	/**
	 * Set to true if content page
	 * 
	 * @since 1.0.0
	 * 
	 * @var bool
	 */
	protected $content_page = false;

	/**
	 * The admin core object
	 * 
	 * @since 1.0.0
	 * 
	 * @var object
	 */
	private $admin = null;

	/**
	 * Initalize Object Hooks
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->admin = new Admin();
		$this->init_variables();
		if ( ! $this->is_base ) {
			if ( $this->is_sub_page ) {
				$this->register_page();
			} else {
				$this->add_action( 'hammock_admin_menu_page', 'menu_page', 10, 2 );
				$this->add_action( 'hammock_network_admin_menu_page', 'network_menu_page', 10, 2 );
			}
		}
		$this->add_action( 'hammock_plugin_admin_setup', 'setup' );
		$this->add_action( 'hammock_controller_scripts', 'controller_scripts' );
		$this->init();
	}


	public function init_variables() {
		add_filter( 'hammock_front_vars', array( $this, 'admin_front_vars' ) );
		if ( is_admin() || is_network_admin() ) {
			add_filter( 'hammock_admin_vars', array( $this, 'admin_js_vars' ) );
		}
	}


	/**
	 * Controller menu page
	 *
	 * @param string $slug - the parent menu slug
	 * @param string $cap - the menu capabilities
	 *
	 * @since 1.0.0
	 */
	public function menu_page( $slug, $cap ) {

	}

	/**
	 * Controller network menu page
	 *
	 * @param string $slug - the parent menu slug
	 * @param string $cap - the menu capabilities
	 *
	 * @since 1.0.0
	 */
	public function network_menu_page( $slug, $cap ) {

	}

	/**
	 * Get admin pages
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_pages() {
		return $this->content_page ? $this->admin->get_content_pages() : $this->admin->get_setting_pages(); 
	}

	/**
	 * Register a page
	 * 
	 * @since 1.0.0
	 */
	public function register_page() {

	}

	/**
	 * Register the content page
	 * 
	 * @param array $args
	 * 
	 * @since 1.0.0
	 */
	public function register_content_page( $args ) {
		$this->admin->register_content_sub_page( $args );
	}

	/**
	 * Register setting sub page
	 * 
	 * @param array $args
	 * 
	 * @since 1.0.0
	 */
	public function register_setting_page( $args ) {
		$this->admin->register_setting_sub_page( $args );
	}


	/**
	 * Initialize controller
	 *
	 * @since 1.0.0
	 */
	public function init() {

	}

	/**
	 * Set up controller after menu is created
	 *
	 * @since 1.0.0
	 */
	public function setup() {

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
		return $vars;
	}

	/**
	 * Set up front js variables
	 *
	 * @param array $vars
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function admin_front_vars( $vars ) {
		return $vars;
	}


	/**
	 * Verify nonce.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $action - The action name to verify nonce.
	 * @param  string $request_method - POST or GET
	 * @param  string $nonce_field - The nonce field name
	 * @param  bool   $json - return json or boolean on error
	 *
	 * @return boolean True if verified, false otherwise.
	 */
	public function verify_nonce( $action = 'hammock_rest_nonce', $request_method = 'POST', $nonce_field = '_wpnonce', $json = true ) {
		switch ( $request_method ) {
			case 'GET':
				$request_fields = $_GET;
				break;

			case 'REQUEST':
			case 'any':
				$request_fields = $_REQUEST;
				break;

			case 'POST':
			default:
				$request_fields = $_POST;
				break;
		}

		if ( empty( $action ) ) {
			$action = ! empty( $request_fields['action'] ) ? $request_fields['action'] : '';
		}

		if ( ! empty( $request_fields[ $nonce_field ] )
			&& wp_verify_nonce( $request_fields[ $nonce_field ], $action )
		) {
			return apply_filters(
				'hammock_base_controller_verify_nonce',
				true,
				$action,
				$request_method,
				$nonce_field,
				$this
			);
		} else {
			if ( $json ) {
				wp_send_json_error( __( 'Invalid request, you are not allowed to make this request', 'hammock' ) );
			} else {
				return false;
			}
		}
	}


	/**
	 * Get field from request parameters.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $id - The field ID
	 * @param  mixed  $default - The default value of the field.
	 * @param  string $request_method - POST or GET
	 * @param  bool   $sanitize - sanitize field
	 *
	 * @return mixed The value of the request field sanitized.
	 */
	public function get_request_field( $id, $default = '', $request_method = 'POST', $sanitize = true ) {
		$value          = $default;
		$request_fields = null;

		switch ( $request_method ) {
			case 'GET':
				$request_fields = $_GET;
				break;

			case 'REQUEST':
				$request_fields = $_REQUEST;
				break;

			default:
			case 'POST':
				$request_fields = $_POST;
				break;

		}

		if ( isset( $request_fields[ $id ] ) ) {
			$value = $request_fields[ $id ];
		}

		return apply_filters(
			'hammock_base_controller_get_request_field',
			$value,
			$id,
			$default
		);
	}


	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {

	}

	/**
	 * Render view
	 * By default it will return the base view
	 *
	 * @return String
	 */
	public function render() {
		?>
		<div id="hammock-admin-container"></div>
		<?php
	}

	/**
	 * Check if current page is the page id
	 *
	 * @param string $page_id
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function is_page( $page_id ) {
		if ( isset( $_GET['page'] ) ) {
			$page = sanitize_text_field( $_GET['page'] );
			if ( $page === self::MENU_SLUG ) {
				return true;
			} else {
				return $page == self::MENU_SLUG . '-' . $page_id;
			}
		}
		return false;
	}
}
?>
