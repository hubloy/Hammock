<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Model\Settings;

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

		$membership_column = array( 'membership' => __( 'Is Member', 'hammock' ) );
		if ( $this->settings->get_general_setting( 'account_verification' ) === 1 ) {
			$membership_column = array(
				'membership' => __( 'Is Member', 'hammock' ),
				'verified'   => __( 'Verified', 'hammock' ),
			);
		}

		$new_columns = $columns_4 + $membership_column + $columns_5;

		return apply_filters( 'hammock_manage_users_columns', $new_columns, $columns );
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
			if ( ! hammock_user_can_subscribe( $user_id ) ) {
				$value = '<span style="font-weight:bold;">' . __( 'No (Not allowed)', 'hammock' ) . '</span>';
			} else {
				if ( hammock_user_is_member( $user_id ) ) {
					$plans = hammock_user_has_plans( $user_id );
					$value = sprintf( __( '%d plans', 'hammock' ), $plans );
				} else {
					$value = __( 'No', 'hammock' );
				}
			}
		} elseif ( 'verified' == $column_name ) {
			if ( is_super_admin( $user_id ) ) {
				$value = '<span style="font-weight:bold;">' . __( 'No (Admin)', 'hammock' ) . '</span>';
			} else {
				$user_activation_status = get_user_meta( $user_id, '_hammock_activation_status', true );
				$value                  = __( 'Not Verified', 'hammock' );
				if ( $user_activation_status ) {
					if ( intval( $user_activation_status ) === 3 ) {
						$value = __( 'Verified', 'hammock' );
					}
				}
			}
		}
		return apply_filters( 'hammock_manage_users_custom_column', $value, $column_name, $user_id );
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

		$actions['hammock_bulk_approve']    = __( 'Approve', 'hammock' );
		$actions['hammock_bulk_disapprove'] = __( 'Disapprove', 'hammock' );
		$actions['hammock_bulk_resend']     = __( 'Resend Verification Email', 'hammock' );

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
			case 'hammock_bulk_approve':
				foreach ( $items as $user_id ) {
					if ( hammock_user_can_subscribe( $user_id ) ) {
						update_user_meta( $user_id, '_hammock_activation_status', 3 );
					}
				}
				$redirect_to = admin_url( 'users.php' );
				$redirect_to = add_query_arg( '_hammock_approved', count( $items ), $redirect_to );
				break;

			case 'hammock_bulk_disapprove':
				foreach ( $items as $user_id ) {
					if ( hammock_user_can_subscribe( $user_id ) ) {
						update_user_meta( $user_id, '_hammock_activation_status', 2 );
					}
				}
				$redirect_to = admin_url( 'users.php' );
				$redirect_to = add_query_arg( '_hammock_disapproved', count( $items ), $redirect_to );
				break;

			case 'hammock_bulk_resend':
				foreach ( $items as $user_id ) {
					if ( hammock_user_can_subscribe( $user_id ) ) {
						// Send mail
						// Find better way to process and queue bulk emails. Maybe a cron
						$user = get_user_by( 'ID', $user_id );
						if ( $user ) {
							$type       = \Hammock\Services\Emails::COMM_TYPE_REGISTRATION_VERIFY;
							$verify_key = wp_generate_password( 20, false );

							update_user_meta( $user_id, '_hammock_activation_status', 2 );
							update_user_meta( $user_id, '_hammock_activation_key', $verify_key );

							$user_object = (object) array(
								'user_login' => $user->user_login,
								'user_id'    => $user->ID,
								'verify_key' => $verify_key,
							);
							// Send verification email
							do_action( 'hammock_send_email_member-' . $type, array(), $user_object, $user->user_email, array(), array() );
						}
					}
				}
				$redirect_to = admin_url( 'users.php' );
				$redirect_to = add_query_arg( '_hammock_resend', count( $items ), $redirect_to );
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
		if ( isset( $_REQUEST['_hammock_approved'] ) ) {
			$user_count = intval( $_REQUEST['_hammock_approved'] );
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo sprintf( __( '%d user accounts approved', 'hammock' ), $user_count ); ?></p>
			</div>
			<?php
		} elseif ( isset( $_REQUEST['_hammock_disapproved'] ) ) {
			$user_count = intval( $_REQUEST['_hammock_disapproved'] );
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo sprintf( __( '%d user accounts disapproved', 'hammock' ), $user_count ); ?></p>
			</div>
			<?php
		} elseif ( isset( $_REQUEST['_hammock_resend'] ) ) {
			$user_count = intval( $_REQUEST['_hammock_resend'] );
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo sprintf( __( '%d user accounts resent emails', 'hammock' ), $user_count ); ?></p>
			</div>
			<?php
		}
	}
}
?>
