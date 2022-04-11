<?php
namespace HubloyMembership\Rule\Custom;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Rule;

class Types extends Rule {

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
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
	 * @return Post
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Main rule set up
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->id   = 'custom_types';
		$this->name = __( 'Custom Post Types', 'memberships-by-hubloy' );
	}
}
