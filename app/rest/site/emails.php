<?php
namespace Hammock\Rest\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Rest;
use Hammock\Model\Settings;

/**
 * Emails rest endpoint
 *
 * @since 1.0.0
 */
class Emails extends Rest {

	const BASE_API_ROUTE = '/emails/';

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
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set up the api routes
	 *
	 * @param string $namespace - the parent namespace
	 *
	 * @since 1.0.0
	 */
	public function set_up_route( $namespace ) {

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'list',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_senders' ),
				'permission_callback' => array( $this, 'validate_request' ),
			)
		);


		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'get',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_sender' ),
				'permission_callback' => array( $this, 'validate_request' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'type'              => 'string',
						'description'       => __( 'The email id', 'hammock' ),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			self::BASE_API_ROUTE . 'update',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_sender' ),
					'permission_callback' => array( $this, 'validate_request' ),
				),
			)
		);

	}


	/**
	 * List emails
	 * 
	 * @param WP_REST_Request $request - the rest request
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function list_senders( $request ) {
		$emails = \Hammock\Services\Emails::get_email_senders();
		return $emails;
	}

	/**
	 * Get Sender by id
	 * 
	 * @param WP_REST_Request $request - the rest request
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_sender( $request ) {
		$id  		= $request['id'];
		$response 	= apply_filters( 'hammock_email_sender_' . $id . '_get_setting_form', array(), $request );
		return $response;
	}

	/**
	 * Update email sender
	 * 
	 * @param WP_REST_Request $request - the rest request
	 * 
	 * @since 1.0.0
	 * 
	 * @return mixed
	 */
	public function update_sender( $request ) {
		$id 	= $request['id'];
		do_action( 'hammock_email_sender_' . $id . '_update_settings', $request );
		return array(
			'status'   => true,
			'message'  => __( 'Settings updated', 'hammock' )
		);
	}

}

