<?php
namespace HubloyMembership\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;
use HubloyMembership\Model\Settings;

/**
 * Users controller
 * Handles WordPress user specific actions
 *
 * @since 1.0.0
 */
class Users extends Controller {

	/**
	 * Setting object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $settings = null;

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Controller
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
	 * @return Controller
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize controller
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->settings = new Settings();

		$this->add_filter( 'manage_users_columns', 'manage_users_columns', 10, 1 );
		$this->add_filter( 'manage_users_custom_column', 'manage_users_custom_column', 10, 3 );

		if ( $this->settings->get_general_setting( 'account_verification' ) === 1 ) {
			$this->add_filter( 'bulk_actions-users', 'add_verify_bulk_action', 10, 1 );
			$this->add_filter( 'handle_bulk_actions-users', 'handle_verify_bulk_action', 10, 3 );
			$this->add_action( 'admin_notices', 'handle_verify_bulk_message' );
		}
	}


	/**
	 * Add Membership column after the Role column
	 *
	 * @param array $columns - current columns
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function manage_users_columns( $columns ) {
		$new_columns = array();
		$columns_4   = array_slice( $columns, 0, 5 );
		$columns_5   = array_slice( $columns, 5 );

		$membership_column = array( 'membership' => __( 'Is Member', 'hubloy-membership' ) );
		if ( $this->settings->get_general_setting( 'account_verification' ) === 1 ) {
			$membership_column = array(
				'membership' => __( 'Is Member', 'hubloy-membership' ),
				'verified'   => __( 'Verified', 'hubloy-membership' ),
			);
		}

		$new_columns = $columns_4 + $membership_column + $columns_5;

		return apply_filters( 'hubloy_membership_manage_users_columns', $new_columns, $columns );
	}


	/**
	 * Add Membership column to users list
	 *
	 * @param string $output      Custom column output. Default empty.
	 * @param string $column_name Column name.
	 * @param int    $user_id     ID of the currently-listed user.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function manage_users_custom_column( $value, $column_name, $user_id ) {
		if ( 'membership' == $column_name ) {
			if ( ! hubloy_membership_user_can_subscribe( $user_id ) ) {
				$value = '<span style="font-weight:bold;">' . __( 'No (Not allowed)', 'hubloy-membership' ) . '</span>';
			} else {
				if ( hubloy_membership_user_is_member( $user_id ) ) {
					$plans = hubloy_membership_user_has_plans( $user_id );
					$value = sprintf( __( '%d plans', 'hubloy-membership' ), $plans );
				} else {
					$value = __( 'No', 'hubloy-membership' );
				}
			}
		} elseif ( 'verified' == $column_name ) {
			if ( is_super_admin( $user_id ) ) {
				$value = '<span style="font-weight:bold;">' . __( 'No (Admin)', 'hubloy-membership' ) . '</span>';
			} else {
				$user_activation_status = get_user_meta( $user_id, '_hubloy_membership_activation_status', true );
				$value                  = __( 'Not Verified', 'hubloy-membership' );
				if ( $user_activation_status ) {
					if ( intval( $user_activation_status ) === 3 ) {
						$value = __( 'Verified', 'hubloy-membership' );
					}
				}
			}
		}
		return apply_filters( 'hubloy_membership_manage_users_custom_column', $value, $column_name, $user_id );
	}

	/**
	 * Add bulk action to verify users
	 *
	 * @param array $actions - the action
	 *
	 * @since 1.0.0
	 *
	 * @return $actions
	 */
	public function add_verify_bulk_action( $actions ) {

		$actions['hubloy_membership_bulk_approve']    = __( 'Approve', 'hubloy-membership' );
		$actions['hubloy_membership_bulk_disapprove'] = __( 'Disapprove', 'hubloy-membership' );
		$actions['hubloy_membership_bulk_resend']     = __( 'Resend Verification Email', 'hubloy-membership' );

		return $actions;
	}

	/**
	 * Hande the verify bulk action
	 *
	 * @since 1.1.3
	 *
	 * @param string $redirect_to - the url to redirect to
	 * @param string $doaction - The action being taken
	 * @param array  $items - The items to take the action on
	 *
	 * @return string $redirect_to
	 */
	public function handle_verify_bulk_action( $redirect_to, $doaction, $items ) {

		switch ( $doaction ) {
			case 'hubloy_membership_bulk_approve':
				foreach ( $items as $user_id ) {
					if ( hubloy_membership_user_can_subscribe( $user_id ) ) {
						update_user_meta( $user_id, '_hubloy_membership_activation_status', 3 );
					}
				}
				$redirect_to = admin_url( 'users.php' );
				$redirect_to = add_query_arg( '_hubloy_membership_approved', count( $items ), $redirect_to );
				break;

			case 'hubloy_membership_bulk_disapprove':
				foreach ( $items as $user_id ) {
					if ( hubloy_membership_user_can_subscribe( $user_id ) ) {
						update_user_meta( $user_id, '_hubloy_membership_activation_status', 2 );
					}
				}
				$redirect_to = admin_url( 'users.php' );
				$redirect_to = add_query_arg( '_hubloy_membership_disapproved', count( $items ), $redirect_to );
				break;

			case 'hubloy_membership_bulk_resend':
				foreach ( $items as $user_id ) {
					if ( hubloy_membership_user_can_subscribe( $user_id ) ) {
						// Send mail
						// Find better way to process and queue bulk emails. Maybe a cron
						$user = get_user_by( 'ID', $user_id );
						if ( $user ) {
							$verify_key = wp_generate_password( 20, false );

							update_user_meta( $user_id, '_hubloy_membership_activation_status', 2 );
							update_user_meta( $user_id, '_hubloy_membership_activation_key', $verify_key );

							do_action( 'hubloy_member_verification', $user->username, $user, $verify_key );
						}
					}
				}
				$redirect_to = admin_url( 'users.php' );
				$redirect_to = add_query_arg( '_hubloy_membership_resend', count( $items ), $redirect_to );
				break;
		}

		return $redirect_to;
	}

	/**
	 * Handle bulk message for approval status change
	 *
	 * @since 1.1.3
	 */
	public function handle_verify_bulk_message() {
		if ( isset( $_REQUEST['_hubloy_membership_approved'] ) ) {
			$user_count = intval( $_REQUEST['_hubloy_membership_approved'] );
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo sprintf( __( '%d user accounts approved', 'hubloy-membership' ), $user_count ); ?></p>
			</div>
			<?php
		} elseif ( isset( $_REQUEST['_hubloy_membership_disapproved'] ) ) {
			$user_count = intval( $_REQUEST['_hubloy_membership_disapproved'] );
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo sprintf( __( '%d user accounts disapproved', 'hubloy-membership' ), $user_count ); ?></p>
			</div>
			<?php
		} elseif ( isset( $_REQUEST['_hubloy_membership_resend'] ) ) {
			$user_count = intval( $_REQUEST['_hubloy_membership_resend'] );
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo sprintf( __( '%d user accounts resent emails', 'hubloy-membership' ), $user_count ); ?></p>
			</div>
			<?php
		}
	}
}
?>
