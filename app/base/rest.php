<?php
namespace Hammock\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Rest base class
 *
 * @since 1.0.0
 */
class Rest extends Component {

	/**
	 * Initalize Rest
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->add_action( 'hammock_register_rest_route', 'register_rest_route' );
	}

	/**
	 * Register the route
	 *
	 * @since 1.0.0
	 */
	function register_rest_route() {
		$this->set_up_route( HAMMOCK_REST_NAMESPACE );
	}


	/**
	 * Set up the api routes
	 *
	 * @param String $namespace - the parent namespace
	 *
	 * @since 1.0.0
	 */
	function set_up_route( $namespace ) {

	}

	/**
	 * Validate the request
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool|WP_Error
	 */
	function validate_request( $request ) {
		$can_view = apply_filters( 'hammock_default_rest_check', current_user_can( 'manage_options' ), $request );
		if ( ! $can_view ) {
			return new \WP_Error(
				'rest_user_cannot_view',
				__( 'Invalid request, you are not allowed to make this request', 'hammock' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}
}

