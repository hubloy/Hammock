<?php
namespace Hammock\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Resource helper
 * This handles all necessary plugin resources
 *
 * @since 1.0.0
 */
class Resource {

	/**
	 * Register admin scripts
	 *
	 * @since 1.0.0
	 */
	public static function register_admin_scripts() {
		$code = \Hammock\Helper\Currency::get_membership_currency();

		// UIkit js
		wp_register_script(
			'hammock-uikit',
			HAMMOCK_ASSETS_URL . '/vendor/uikit/js/uikit.min.js',
			array( 'jquery' ),
			HAMMOCK_UIKIT_VERSION,
			true
		);
		wp_register_script(
			'hammock-uikit-icons',
			HAMMOCK_ASSETS_URL . '/vendor/uikit/js/uikit-icons.min.js',
			array( 'jquery' ),
			HAMMOCK_UIKIT_VERSION,
			true
		);

		// Tool tip helper
		wp_register_script(
			'hammock-tiptip',
			HAMMOCK_ASSETS_URL . '/vendor/tiptip/jquery.tipTip.minified.js',
			array( 'jquery' ),
			'1.3',
			true
		);

		// SWAL
		wp_register_script(
			'hammock-sweetalert',
			HAMMOCK_ASSETS_URL . '/vendor/sweetalert/sweetalert2.all.min.js',
			array( 'jquery' ),
			'8.0.1',
			true
		);

		// Jquery chosen
		wp_register_script(
			'hammock-jquery-chosen',
			HAMMOCK_ASSETS_URL . '/vendor/chosen/chosen.jquery.min.js',
			array( 'jquery' ),
			'1.8.7',
			true
		);

		// Jquery tags
		wp_register_script(
			'hammock-jquery-tags',
			HAMMOCK_ASSETS_URL . '/vendor/tags/jquery.tagsinput.min.js',
			array( 'jquery' ),
			'1.3.6',
			true
		);

		// notifications
		wp_register_script(
			'hammock-styled-notifications',
			HAMMOCK_ASSETS_URL . '/vendor/styled-notifications/notifications.js',
			array( 'jquery' ),
			'1.0.1',
			true
		);

		wp_register_script(
			'hammock-admin',
			HAMMOCK_ASSETS_URL . '/js/hammock-admin.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION,
			true
		);
		$vars = apply_filters(
			'hammock_admin_vars',
			array(
				'error'        => __( 'An error occured', 'hammock' ),
				'no_results'   => __( 'Ooops, no results found', 'hammock' ),
				'no_data'      => __( 'Ooops, no data found', 'hammock' ),
				'base_api_url' => rest_url( 'wp/v2/' ),
				'api_url'      => rest_url( HAMMOCK_REST_NAMESPACE ),
				'api_nonce'    => wp_create_nonce( 'wp_rest' ),
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'   => wp_create_nonce( 'hammock_rest_nonce' ),
				'assets_url'   => HAMMOCK_ASSETS_URL,
				'is_multisite' => is_multisite(),
				'active_page'  => 'dashboard',
				'common'       => array(
					'buttons'       => array(
						'save'     => __( 'Save', 'hammock' ),
						'continue' => __( 'Save and Continue', 'hammock' ),
						'update'   => __( 'Update', 'hammock' ),
						'edit'     => __( 'Edit', 'hammock' ),
						'delete'   => __( 'Delete', 'hammock' ),
						'manage'   => __( 'Manage', 'hammock' ),
						'ok'       => __( 'Okay', 'hammock' ),
						'cancel'   => __( 'Cancel', 'hammock' ),
						'back'     => __( 'Back', 'hammock' ),
						'reminder' => __( 'Send Reminder', 'hammock' ),
					),
					'string'        => array(
						'dashboard' => __( 'Dashboard', 'hammock' ),
						'not_found' => __( "Sorry, we couldn't find what you are looking for", 'hammock' ),
						'title'     => __( 'Dashboard', 'hammock' ),
						'search'    => array(
							'users'   => array(
								'select'    => __( 'Search for user', 'hammock' ),
								'not_found' => __( 'User not found', 'hammock' ),
							),
							'members' => array(
								'select'    => __( 'Search for member', 'hammock' ),
								'not_found' => __( 'Member not found', 'hammock' ),
							),
						),
					),
					'status'        => array(
						'enabled'  => __( 'Enabled', 'hammock' ),
						'disabled' => __( 'Disabled', 'hammock' ),
						'status'   => __( 'Status', 'hammock' ),
						'expired'  => __( 'Expired', 'hammock' ),
						'loading'  => __( 'Loading...', 'hammock' ),
						'success'  => __( 'Success', 'hammock' ),
						'error'    => __( 'Error', 'hammock' ),
					),
					'general'       => array(
						'settings'  => __( 'Settings', 'hammock' ),
						'configure' => __( 'Configure', 'hammock' ),
						'filter'    => __( 'Filter', 'hammock' ),
						'actions'   => __( 'Actions', 'hammock' ),
						'all'       => __( 'All', 'hammock' ),
					),
					'urls'          => array(
						'dash_url' => is_multisite() ? network_admin_url( 'admin.php?page=hammock' ) : admin_url( 'admin.php?page=hammock' ),
						'settings' => is_multisite() ? network_admin_url( 'admin.php?page=hammock-settings' ) : admin_url( 'admin.php?page=hammock-settings' ),
					),
					'currency_code' => esc_html( $code ),
				),
				'page_strings' => array(),
				'strings'      => array(),
				'assets'       => array(
					'spinner' => HAMMOCK_ASSETS_URL . '/img/spinner.gif',
				),

			)
		);

		wp_localize_script( 'hammock-admin', 'hammock', $vars );

		wp_register_script(
			'hammock-addons-react',
			HAMMOCK_ASSETS_URL . '/js/react/addon.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-admin-react',
			HAMMOCK_ASSETS_URL . '/js/react/admin.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-comms-react',
			HAMMOCK_ASSETS_URL . '/js/react/comms.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-members-react',
			HAMMOCK_ASSETS_URL . '/js/react/members.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-memberships-react',
			HAMMOCK_ASSETS_URL . '/js/react/memberships.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-settings-react',
			HAMMOCK_ASSETS_URL . '/js/react/settings.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-transactions-react',
			HAMMOCK_ASSETS_URL . '/js/react/transactions.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-coupons-react',
			HAMMOCK_ASSETS_URL . '/js/react/coupons.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-marketing-react',
			HAMMOCK_ASSETS_URL . '/js/react/marketing.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-invites-react',
			HAMMOCK_ASSETS_URL . '/js/react/invites.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-rules-react',
			HAMMOCK_ASSETS_URL . '/js/react/rules.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);

		wp_register_script(
			'hammock-wizard-react',
			HAMMOCK_ASSETS_URL . '/js/react/wizard.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION
		);
	}

	/**
	 * Register admin styles
	 *
	 * @since 1.0.0
	 */
	public static function register_admin_styles() {
		// UIkit css
		wp_register_style(
			'hammock-uikit',
			HAMMOCK_ASSETS_URL . '/vendor/uikit/css/uikit.min.css',
			null,
			HAMMOCK_UIKIT_VERSION
		);

		// tiptip
		wp_register_style(
			'hammock-tiptip',
			HAMMOCK_ASSETS_URL . '/vendor/tiptip/tipTip.css',
			null,
			'1.2'
		);

		// Jquery UI
		wp_register_style(
			'hammock-jquery-ui',
			HAMMOCK_ASSETS_URL . '/vendor/jquery-ui/jquery-ui.min.css',
			null,
			'1.12.1'
		);

		// Jquery chosen
		wp_register_style(
			'hammock-jquery-chosen',
			HAMMOCK_ASSETS_URL . '/vendor/chosen/chosen.min.css',
			null,
			'1.8.7'
		);

		// Jquery tags
		wp_register_style(
			'hammock-jquery-tags',
			HAMMOCK_ASSETS_URL . '/vendor/tags/jquery.tagsinput.min.css',
			null,
			'1.8.7'
		);

		// Notifications
		wp_register_style(
			'hammock-styled-notifications',
			HAMMOCK_ASSETS_URL . '/vendor/styled-notifications/notifications.css',
			null,
			'1.0.1'
		);

		// Admin CSS
		wp_register_style(
			'hammock-admin',
			HAMMOCK_ASSETS_URL . '/css/hammock-admin.min.css',
			null,
			HAMMOCK_VERSION
		);
	}


	/**
	 * Register front styles
	 *
	 * @since 1.0.0
	 */
	public static function register_front_styles() {
		wp_register_style(
			'hammock-front',
			HAMMOCK_ASSETS_URL . '/css/hammock-front.min.css',
			null,
			HAMMOCK_VERSION
		);
	}

	/**
	 * Register front scripts
	 *
	 * @since 1.0.0
	 */
	public static function register_front_scripts() {

		// Swal
		wp_register_script(
			'hammock-sweetalert',
			HAMMOCK_ASSETS_URL . '/vendor/sweetalert/sweetalert2.all.min.js',
			array( 'jquery' ),
			'8.0.1',
			true
		);

		wp_register_script(
			'hammock-front',
			HAMMOCK_ASSETS_URL . '/js/hammock-front.min.js',
			array( 'jquery' ),
			HAMMOCK_VERSION,
			true
		);
		$vars = apply_filters(
			'hammock_front_vars',
			array(
				'error'      => __( 'An error occured', 'hammock' ),
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'no_results' => __( 'Ooops, no results found', 'hammock' ),
				'assets'     => array(
					'spinner' => HAMMOCK_ASSETS_URL . '/img/spinner.gif',
				),
				'common'     => array(
					'buttons' => array(
						'save'   => __( 'Save', 'hammock' ),
						'update' => __( 'Update', 'hammock' ),
						'edit'   => __( 'Edit', 'hammock' ),
						'delete' => __( 'Delete', 'hammock' ),
						'ok'     => __( 'Okay', 'hammock' ),
						'cancel' => __( 'Cancel', 'hammock' ),
						'back'   => __( 'Back', 'hammock' ),
					),
				),
			)
		);

		wp_localize_script( 'hammock-front', 'hammock', $vars );
	}
}

