<?php
namespace HubloyMembership\Addon\Mailchimp;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Addon;

class Mailchimp extends Addon {

	/**
	 * The API instance
	 *
	 * @since 1.0.0
	 *
	 * @var API
	 */
	private $api = null;

	/**
	 * Singletone instance of the addon.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the addon.
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
	 * Addon init
	 */
	public function init() {
		$this->id = 'mailchimp';
	}

	/**
	 * Register addon
	 * Register a key value pair of addons
	 *
	 * @param array $addons - the current list of addons
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register( $addons ) {
		if ( ! isset( $addons['mailchimp'] ) ) {
			$addons['mailchimp'] = array(
				'name'        => __( 'MailChimp Integration', 'memberships-by-hubloy' ),
				'description' => __( 'MailChimp integration.', 'memberships-by-hubloy' ),
				'icon'        => 'dashicons dashicons-email',
				'url'         => admin_url( 'admin.php?page=memberships-by-hubloy-marketing' ),
			);
		}
		return $addons;
	}

	/**
	 * Initialize the addon action
	 *
	 * @since 1.0.0
	 */
	public function init_addon() {
		$this->add_action( 'hubloy_member_registered', 'user_registered' );
		$this->add_action( 'hubloy_membership_plan_record_payment', 'user_subscribed', 10, 2 );
		$this->add_action( 'hubloy_membership_plan_canceled', 'user_unsubscribed' );
	}

	/**
	 * Addon settings
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function settings_page() {
		$view       = new \HubloyMembership\View\Backend\Addons\Mailchimp();
		$settings   = $this->settings();
		$view->data = array(
			'settings' => $settings,
		);
		return $view->render( true );
	}


	/**
	 * Update addon settings
	 *
	 * @param array $response
	 * @param array $data - the post data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_settings( $response = array(), $data ) {
		$apikey     = sanitize_text_field( $data['apikey'] );
		$enabled    = isset( $data['enabled'] ) ? 1 : 0;
		$optin      = isset( $data['double_optin'] ) ? 1 : 0;
		$reg_list   = sanitize_text_field( $data['registered_list'] );
		$sub_list   = sanitize_text_field( $data['subscriber_list'] );
		$unsub_list = sanitize_text_field( $data['unsubscriber_list'] );
		$settings   = $this->settings();

		$settings['enabled']           = $enabled;
		$settings['apikey']            = $apikey;
		$settings['double_optin']      = $optin;
		$settings['registered_list']   = $reg_list;
		$settings['subscriber_list']   = $sub_list;
		$settings['unsubscriber_list'] = $unsub_list;
		$this->settings->set_addon_setting( $this->id, $settings );
		$this->settings->save();
		return array(
			'status'  => true,
			'message' => __( 'MailChimp Settings Saved', 'memberships-by-hubloy' ),
		);
	}


	/**
	 * Used to handle custom addon actions
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function addon_action( $response = array(), $data ) {
		$action = sanitize_text_field( $data['action'] );
		switch ( $action ) {
			case 'check_status':
				$apikey      = sanitize_text_field( $data['apikey'] );
				$exploded    = explode( '-', $apikey );
				$data_center = end( $exploded );
				$this->api   = new Api( $apikey, $data_center );
				$lists       = $this->get_lists();
				if ( is_wp_error( $lists ) ) {
					return array(
						'error'   => true,
						'message' => sprintf( __( 'Error: %s', 'memberships-by-hubloy' ), $lists->get_error_message() ),
					);
				}
				// Set the api key
				$settings                 = $this->settings();
				$settings['apikey']       = $apikey;
				$settings['valid']        = true;
				$settings['double_optin'] = true;
				$this->settings->set_addon_setting( $this->id, $settings );
				$this->settings->save();

				return array(
					'success' => true,
					'message' => __( 'Valid API key', 'memberships-by-hubloy' ),
					'lists'   => $lists,
				);
			break;
			case 'get_lists':
				$this->configure_api();
				$lists = $this->get_lists();
				if ( is_wp_error( $lists ) ) {
					return array(
						'error'   => true,
						'message' => sprintf( __( 'Error: %s', 'memberships-by-hubloy' ), $lists->get_error_message() ),
					);
				} else {
					return array(
						'success' => true,
						'lists'   => $lists,
					);
				}
				break;
		}
		return array(
			'success' => true,
			'message' => __( 'Action executed', 'memberships-by-hubloy' ),
		);
	}

	/**
	 * Configure the API.
	 * 
	 * @since 1.1.1
	 */
	private function configure_api() {
		$settings    = $this->settings();
		$apikey      = $settings['apikey'];
		$exploded    = explode( '-', $apikey );
		$data_center = end( $exploded );
		$this->api   = new Api( $apikey, $data_center );
	}

	/**
	 * Get lists
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_lists( $refresh = false ) {
		$response = $this->api->get_lists();
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$lists  = array(
			'' => __( 'Select List', 'memberships-by-hubloy' ),
		);
		$_lists = $response->lists;
		$total  = $response->total_items;
		if ( is_array( $_lists ) ) {
			foreach ( $_lists as $list ) {
				$list                 = (array) $list;
				$lists[ $list['id'] ] = $list['name'];
			}
		}
		return $lists;
	}

	/**
	 * Action called when a user has finished registration.
	 * We try and add them to a list based on marketing settings.
	 * 
	 * @param WP_User $user The current user
	 * 
	 * @since 1.1.0
	 */
	public function user_registered( $user ) {
		$settings    = $this->settings();
		if ( $settings['registered_list'] ) {
			$email  = $user->user_email;
			$list   = $settings['registered_list'];
			$this->add_to_list( $list, $email );
		}
	}

	/**
	 * User subscribed action.
	 * 
	 * @param \HubloyMembership\Model\Plan $plan The current plan.
	 * @param \HubloyMembership\Model\Invoice $invoice The current invoice
	 * 
	 * @since 1.1.1
	 */
	public function user_subscribed( $plan, $invoice ) {
		$settings = $this->settings();
		$member   = $plan->get_member();
		$email    = $member->get_user_info( 'email' );
		if ( $settings['subscriber_list'] ) {
			$list   = $settings['subscriber_list'];
			$this->add_to_list( $list, $email, $settings['registered_list'] );
		} else {
			$this->configure_api();
			if ( $settings['registered_list'] && $settings['valid'] ) {
				$this->api->delete_email( $settings['registered_list'], $email );
			}
		}
	}

	/**
	 * User unsubscribed action.
	 * 
	 * @param \HubloyMembership\Model\Plan $plan The current plan.
	 * 
	 * @since 1.1.1
	 */
	public function user_unsubscribed( $plan ) {
		$settings = $this->settings();
		$member   = $plan->get_member();
		$email    = $member->get_user_info( 'email' );
		if ( $settings['unsubscriber_list'] ) {
			$list   = $settings['unsubscriber_list'];
			$this->add_to_list( $list, $email, $settings['subscriber_list'] );
		} else {
			$this->configure_api();
			if ( $settings['subscriber_list'] && $settings['valid'] ) {
				$this->api->delete_email( $settings['subscriber_list'], $email );
			}
		}
	}

	/**
	 * Add a user to a list
	 * 
	 * @param int $list_id The list id to subscribe to
	 * @param string $email The email address
	 * @param int $old_list (optional) The old list to remove from.
	 * 
	 * @since 1.1.1
	 */
	public function add_to_list( $list_id, $email, $old_list = false ) {
		$settings    = $this->settings();
		if ( $settings['valid'] ) {
			$member = $this->get_member( $email, $list );
			if ( ! $member ) {
				$this->configure_api();
				if ( $old_list ) {
					$this->api->delete_email( $old_list, $email );
				}
				try {
					$this->api->subscribe( $list_id, array(
						'email_address' => $email,
						'status'        => $settings['double_optin'] ? 'pending' : 'subscribed',
					) );
				} catch( \Exception $e ) {
				}
			}
		}
	}

	/**
	 * Get member if already exists
	 * 
	 * @param string $email The current email
	 * @param int $list_id The list id to subscribe to
	 * 
	 * @since 1.1.0
	 * 
	 * @return bool
	 */
	private function get_member( $email, $list_id ) {
		$settings    = $this->settings();
		if ( $settings['valid'] ) {
			$this->configure_api();
			try {
				$member_info = $this->api->check_email( $list_id, $email);
				// Mailchimp returns WP error if can't find member on a list
				if ( is_wp_error( $member_info ) && 404 === (int)$member_info->get_error_code() ) {
					return false;
				}
				return $member_info;
			} catch( \Exception $e ) {
				return false;
			}
		} else {
			return false;
		}
	}
}

