<?php
namespace Hammock\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin core functions
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Content pages
	 * 
	 * @since 1.0.0
	 * 
	 * @var array
	 */
	private $content = array();

	/**
	 * Setting pages
	 * 
	 * @since 1.0.0
	 * 
	 * @var array
	 */
	private $settings = array();


	/**
	 * Register content sub pages
	 *
	 * @param array $args The page arguments
	 * 
	 * @since 1.0.0
	 */
	public function register_content_sub_page( $args ) {
		$args = $this->parse_args( $args );
		if ( isset( $this->content[ $args['id'] ] ) ) {
			return false;
		}
		$this->content[ $args['id'] ] = $args;
		return $this->content;
	}

	/**
	 * Register setting sub pages
	 *
	 * @param array $args The page arguments
	 * 
	 * @since 1.0.0
	 */
	public function register_setting_sub_page( $args ) {
		$args = $this->parse_args( $args );
		if ( isset( $this->settings[ $args['id'] ] ) ) {
			return false;
		}
		$this->settings[ $args['id'] ] = $args;
		return $this->settings;
	}

	/**
	 * Get the content pages.
	 *
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_content_pages() {
		return $this->content;
	}

	/**
	 * Get the settings pages.
	 *
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_setting_pages() {
		return $this->settings;
	}

	/**
	 * Parse the arguments
	 *
	 * @param array $args The arguments
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	private function parse_args( $args ) {
		$args = wp_parse_args( $args, array(
			'id'       => '',
			'name'     => '',
			'icon'     => '',
			'desc'     => '',
			'class'    => '',
			'callback' => false
		) );

		return $args;
	}
}
