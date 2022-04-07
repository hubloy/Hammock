<?php
namespace HubloyMembership\Core;

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
		$code = \HubloyMembership\Helper\Currency::get_membership_currency();

		// UIkit js
		wp_register_script(
			'hubloy_membership-uikit',
			HUBMEMB_ASSETS_URL . '/vendor/uikit/js/uikit.min.js',
			array( 'jquery' ),
			HUBMEMB_UIKIT_VERSION,
			true
		);
		wp_register_script(
			'hubloy_membership-uikit-icons',
			HUBMEMB_ASSETS_URL . '/vendor/uikit/js/uikit-icons.min.js',
			array( 'jquery' ),
			HUBMEMB_UIKIT_VERSION,
			true
		);

		// Tool tip helper
		wp_register_script(
			'hubloy_membership-tiptip',
			HUBMEMB_ASSETS_URL . '/vendor/tiptip/jquery.tipTip.minified.js',
			array( 'jquery' ),
			'1.3',
			true
		);

		// SWAL
		wp_register_script(
			'hubloy_membership-sweetalert',
			HUBMEMB_ASSETS_URL . '/vendor/sweetalert/sweetalert2.all.min.js',
			array( 'jquery' ),
			'8.0.1',
			true
		);

		// Jquery tags
		wp_register_script(
			'hubloy_membership-jquery-tags',
			HUBMEMB_ASSETS_URL . '/vendor/tags/jquery.tagsinput.min.js',
			array( 'jquery' ),
			'1.3.6',
			true
		);

		// notifications
		wp_register_script(
			'hubloy_membership-styled-notifications',
			HUBMEMB_ASSETS_URL . '/vendor/styled-notifications/notifications.js',
			array( 'jquery' ),
			'1.0.1',
			true
		);

		// Select 2
		wp_register_script(
			'hubloy_membership-select2',
			HUBMEMB_ASSETS_URL . '/vendor/select2/js/select2.min.js',
			array( 'jquery' ),
			'4.1.0',
			true
		);

		wp_register_script(
			'hubloy_membership-admin',
			HUBMEMB_ASSETS_URL . '/js/hubloy-membership-admin.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION,
			true
		);
		$vars = apply_filters(
			'hubloy_membership_admin_vars',
			array(
				'error'        => __( 'An error occured', 'hubloy-membership' ),
				'no_results'   => __( 'Ooops, no results found', 'hubloy-membership' ),
				'no_data'      => __( 'Ooops, no data found', 'hubloy-membership' ),
				'base_api_url' => rest_url( 'wp/v2/' ),
				'api_url'      => rest_url( HUBMEMB_REST_NAMESPACE ),
				'api_nonce'    => wp_create_nonce( 'wp_rest' ),
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'   => wp_create_nonce( 'hubloy_membership_rest_nonce' ),
				'assets_url'   => HUBMEMB_ASSETS_URL,
				'is_multisite' => is_multisite(),
				'active_page'  => 'dashboard',
				'common'       => array(
					'buttons'       => array(
						'save'     => __( 'Save', 'hubloy-membership' ),
						'continue' => __( 'Save and Continue', 'hubloy-membership' ),
						'update'   => __( 'Update', 'hubloy-membership' ),
						'edit'     => __( 'Edit', 'hubloy-membership' ),
						'delete'   => __( 'Delete', 'hubloy-membership' ),
						'manage'   => __( 'Manage', 'hubloy-membership' ),
						'ok'       => __( 'Okay', 'hubloy-membership' ),
						'cancel'   => __( 'Cancel', 'hubloy-membership' ),
						'back'     => __( 'Back', 'hubloy-membership' ),
						'reminder' => __( 'Send Reminder', 'hubloy-membership' ),
					),
					'string'        => array(
						'dashboard' => __( 'Dashboard', 'hubloy-membership' ),
						'not_found' => __( "Sorry, we couldn't find what you are looking for", 'hubloy-membership' ),
						'title'     => __( 'Dashboard', 'hubloy-membership' ),
						'search'    => array(
							'users'   => array(
								'select'    => __( 'Search for user', 'hubloy-membership' ),
								'not_found' => __( 'User not found', 'hubloy-membership' ),
							),
							'members' => array(
								'select'    => __( 'Search for member', 'hubloy-membership' ),
								'not_found' => __( 'Member not found', 'hubloy-membership' ),
							),
						),
					),
					'status'        => array(
						'enabled'  => __( 'Enabled', 'hubloy-membership' ),
						'disabled' => __( 'Disabled', 'hubloy-membership' ),
						'status'   => __( 'Status', 'hubloy-membership' ),
						'expired'  => __( 'Expired', 'hubloy-membership' ),
						'loading'  => __( 'Loading...', 'hubloy-membership' ),
						'success'  => __( 'Success', 'hubloy-membership' ),
						'error'    => __( 'Error', 'hubloy-membership' ),
					),
					'general'       => array(
						'settings'  => __( 'Settings', 'hubloy-membership' ),
						'configure' => __( 'Configure', 'hubloy-membership' ),
						'filter'    => __( 'Filter', 'hubloy-membership' ),
						'select'    => __( 'Select', 'hubloy-membership' ),
						'actions'   => __( 'Actions', 'hubloy-membership' ),
						'all'       => __( 'All', 'hubloy-membership' ),
					),
					'urls'          => array(
						'dash_url' => is_multisite() ? network_admin_url( 'admin.php?page=hubloy_membership' ) : admin_url( 'admin.php?page=hubloy_membership' ),
						'settings' => is_multisite() ? network_admin_url( 'admin.php?page=hubloy_membership-settings' ) : admin_url( 'admin.php?page=hubloy_membership-settings' ),
					),
					'currency_code' => esc_html( $code ),
				),
				'page_strings' => array(),
				'strings'      => array(),
				'assets'       => array(
					'spinner' => HUBMEMB_ASSETS_URL . '/img/spinner.gif',
				),

			)
		);

		wp_localize_script( 'hubloy_membership-admin', 'hubloy_membership', $vars );

		wp_register_script(
			'hubloy_membership-addons-react',
			HUBMEMB_ASSETS_URL . '/js/react/addon.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
		);

		wp_register_script(
			'hubloy_membership-admin-react',
			HUBMEMB_ASSETS_URL . '/js/react/admin.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
		);

		wp_register_script(
			'hubloy_membership-comms-react',
			HUBMEMB_ASSETS_URL . '/js/react/comms.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
		);

		wp_register_script(
			'hubloy_membership-members-react',
			HUBMEMB_ASSETS_URL . '/js/react/members.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
		);

		wp_register_script(
			'hubloy_membership-memberships-react',
			HUBMEMB_ASSETS_URL . '/js/react/memberships.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
		);

		wp_register_script(
			'hubloy_membership-settings-react',
			HUBMEMB_ASSETS_URL . '/js/react/settings.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
		);

		wp_register_script(
			'hubloy_membership-transactions-react',
			HUBMEMB_ASSETS_URL . '/js/react/transactions.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
		);

		wp_register_script(
			'hubloy_membership-rules-react',
			HUBMEMB_ASSETS_URL . '/js/react/rules.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
		);

		wp_register_script(
			'hubloy_membership-wizard-react',
			HUBMEMB_ASSETS_URL . '/js/react/wizard.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION
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
			'hubloy_membership-uikit',
			HUBMEMB_ASSETS_URL . '/vendor/uikit/css/uikit.min.css',
			null,
			HUBMEMB_UIKIT_VERSION
		);

		// tiptip
		wp_register_style(
			'hubloy_membership-tiptip',
			HUBMEMB_ASSETS_URL . '/vendor/tiptip/tipTip.css',
			null,
			'1.2'
		);

		// Jquery UI
		wp_register_style(
			'hubloy_membership-jquery-ui',
			HUBMEMB_ASSETS_URL . '/vendor/jquery-ui/jquery-ui.min.css',
			null,
			'1.12.1'
		);

		// Jquery tags
		wp_register_style(
			'hubloy_membership-jquery-tags',
			HUBMEMB_ASSETS_URL . '/vendor/tags/jquery.tagsinput.min.css',
			null,
			'1.8.7'
		);

		// Notifications
		wp_register_style(
			'hubloy_membership-styled-notifications',
			HUBMEMB_ASSETS_URL . '/vendor/styled-notifications/notifications.css',
			null,
			'1.0.1'
		);

		// Select 2
		wp_register_style(
			'hubloy_membership-select2',
			HUBMEMB_ASSETS_URL . '/vendor/select2/css/select2.min.css',
			null,
			'4.1.0'
		);

		// Admin CSS
		wp_register_style(
			'hubloy_membership-admin',
			HUBMEMB_ASSETS_URL . '/css/hubloy-membership-admin.min.css',
			null,
			HUBMEMB_VERSION
		);
	}


	/**
	 * Register front styles
	 *
	 * @since 1.0.0
	 */
	public static function register_front_styles() {
		wp_register_style(
			'hubloy_membership-front',
			HUBMEMB_ASSETS_URL . '/css/hubloy-membership-front.min.css',
			null,
			HUBMEMB_VERSION
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
			'hubloy_membership-sweetalert',
			HUBMEMB_ASSETS_URL . '/vendor/sweetalert/sweetalert2.all.min.js',
			array( 'jquery' ),
			'11.4.6',
			true
		);

		wp_register_script(
			'hubloy_membership-front',
			HUBMEMB_ASSETS_URL . '/js/hubloy-membership-front.min.js',
			array( 'jquery' ),
			HUBMEMB_VERSION,
			true
		);
		$vars = apply_filters(
			'hubloy_membership_front_vars',
			array(
				'error'      => __( 'An error occured', 'hubloy-membership' ),
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'no_results' => __( 'Ooops, no results found', 'hubloy-membership' ),
				'assets'     => array(
					'spinner' => HUBMEMB_ASSETS_URL . '/img/spinner.gif',
				),
				'common'     => array(
					'buttons' => array(
						'save'   => __( 'Save', 'hubloy-membership' ),
						'update' => __( 'Update', 'hubloy-membership' ),
						'edit'   => __( 'Edit', 'hubloy-membership' ),
						'delete' => __( 'Delete', 'hubloy-membership' ),
						'ok'     => __( 'Okay', 'hubloy-membership' ),
						'cancel' => __( 'Cancel', 'hubloy-membership' ),
						'back'   => __( 'Back', 'hubloy-membership' ),
					),
				),
			)
		);

		wp_localize_script( 'hubloy_membership-front', 'hubloy_membership', $vars );
	}
}

