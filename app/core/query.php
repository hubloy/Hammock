<?php
namespace HubloyMembership\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Query class
 * Handles frontend queries to parse the url endpoints
 *
 * @since 1.0.0
 */
class Query {

	/**
	 * Query vars to add to wp.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * Constructor for the query class. Hooks in methods.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_action( 'parse_request', array( $this, 'parse_request' ), 0 );
		}
		$this->init_query_vars();
	}

	/**
	 * Add endpoints for query vars.
	 *
	 * @since 1.0.0
	 */
	public function add_endpoints() {
		$mask = $this->get_endpoints_mask();

		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, $mask );
			}
		}
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Query vars.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}
		return $vars;
	}


	/**
	 * Parse the request and look for query vars - endpoints may not be supported.
	 *
	 * @since 1.0.0
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported.
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) );
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}


	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		// Query vars to add to WP.
		$this->query_vars = \HubloyMembership\Services\Pages::page_endpoits();
	}


	/**
	 * Endpoint mask describing the places the endpoint should be added.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_endpoints_mask() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_on_front        = get_option( 'page_on_front' );
			$membership_page_id   = hubloy-membership_page_id( 'membership_list' );
			$protected_page_id    = hubloy-membership_page_id( 'protected_content' );
			$registration_page_id = hubloy-membership_page_id( 'registration' );
			$thank_you_page_id    = hubloy-membership_page_id( 'thank_you_page' );
			$account_page_id      = hubloy-membership_page_id( 'account_page' );

			$page_ids = array( $membership_page_id, $protected_page_id, $registration_page_id, $thank_you_page_id, $account_page_id );

			if ( in_array( $page_on_front, $page_ids, true ) ) {
				return EP_ROOT | EP_PAGES;
			}
		}

		return EP_PAGES;
	}

	/**
	 * Get query vars.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return apply_filters( 'hubloy-membership_get_query_vars', $this->query_vars );
	}


	/**
	 * Get query current active query var.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_current_endpoint() {
		global $wp;

		foreach ( $this->get_query_vars() as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}
		return '';
	}
}

