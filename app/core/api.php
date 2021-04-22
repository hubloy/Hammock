<?php
namespace Hammock\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handle remote api requests
 * 
 * @since 1.0.0
 */
class Api {

	/**
	 * Constructor for the query class. Hooks in methods.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoint' ), 0 );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		add_action( 'parse_request', array( $this, 'handle_requests' ), 0 );
	}

	/**
	 * Add api endpoint
	 * 
	 * @since 1.0.0
	 */
	public function add_endpoint() {
		add_rewrite_endpoint( 'hm-api', EP_ALL );
	}


	/**
	 * Add new query vars.
	 * 
	 * @param array $vars - query vars.
	 * 
	 * @since 1.0.0
	 * 
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'hm-api';
		return $vars;
	}

	/**
	 * Handle requests
	 * 
	 * @since 1.0.0
	 */
	public function handle_requests() {
		global $wp;

		if ( ! empty( $_GET['hm-api'] ) ) {
			$wp->query_vars['hm-api'] = sanitize_key( wp_unslash( $_GET['hm-api'] ) ); 
		}

		if ( ! empty( $wp->query_vars['hm-api'] ) ) {
			ob_start();
			
			$api_request = strtolower( sanitize_text_field( $wp->query_vars['hm-api'] ) );

			/**
			 * Generic action
			 * 
			 * @param string $api_request - the api request
			 * 
			 * @since 1.0.0
			 */
			do_action( 'hammock_api_request', $api_request );

			/**
			 * Specific api action
			 * 
			 * @since 1.0.0
			 */
			do_action( 'hammock_api_' . $api_request );


			ob_end_clean();
			die( '-1' );
		}
	}
}
?>