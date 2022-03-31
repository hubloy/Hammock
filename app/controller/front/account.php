<?php
namespace HubloyMembership\Controller\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;
use HubloyMembership\Helper\Template;
use HubloyMembership\Services\Members;
use HubloyMembership\Services\Memberships;
use HubloyMembership\Services\Transactions;

/**
 * Account controller
 * This manages front end functions for the account page including the account page template hooks
 *
 * @since 1.0.0
 */
class Account extends Controller {

	/**
	 * The member service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $member_service = null;


	/**
	 * The membership service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $membership_service = null;

	/**
	 * The transaction service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $transaction_service = null;

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

		$this->member_service      = new Members();
		$this->membership_service  = new Memberships();
		$this->transaction_service = new Transactions();
		/**
		 * Account page navigation
		 */
		$this->add_action( 'hubloy_membership_member_account_navigation', 'member_account_navigation' );

		/**
		 * Account page content
		 */
		$this->add_action( 'hubloy_membership_member_account_content', 'member_account_content' );

		// Action called to render various account page content
		$this->add_action( 'hubloy_membership_member_account_dashboard_content', 'member_account_dashboard_content' );
		$this->add_action( 'hubloy_membership_member_account_edit-account_content', 'member_account_edit_content', 10, 2 );
		$this->add_action( 'hubloy_membership_member_account_transactions_content', 'member_account_transactions_content', 10, 2 );
		$this->add_action( 'hubloy_membership_member_account_subscriptions_content', 'member_account_subscriptions_content', 10, 2 );
		$this->add_action( 'hubloy_membership_member_account_view-plan_content', 'member_account_view_plan_content', 10, 2 );
		$this->add_action( 'hubloy_membership_member_account_view-transaction_content', 'member_account_view_transaction_content', 10, 2 );
	}

	/**
	 * Account navigation menu
	 *
	 * @since 1.0.0
	 */
	public function member_account_navigation() {
		Template::get_template( 'account/navigation.php' );
	}

	/**
	 * Account page content
	 *
	 * @since 1.0.0
	 */
	public function member_account_content() {
		global $wp;
		$current_user = Members::user_details( get_current_user_id(), true );
		if ( ! empty( $wp->query_vars ) ) {
			if ( isset( $wp->query_vars['member-logout'] ) ) {
				wp_logout();
				echo "<script>
					window.location.href='" . hubloy_membership_get_account_page_links() . "';
				</script>";
			}

			foreach ( $wp->query_vars as $key => $value ) {
				// Ignore pagename param.
				if ( 'pagename' === $key ) {
					continue;
				}

				if ( has_action( 'hubloy_membership_member_account_' . $key . '_content' ) ) {
					do_action( 'hubloy_membership_member_account_' . $key . '_content', $value, $current_user );
					return;
				}
			}
		}

		do_action( 'hubloy_membership_member_account_dashboard_content', $current_user );
	}

	/**
	 * Member dashboard content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function member_account_dashboard_content( $current_user ) {
		Template::get_template(
			'account/dashboard.php',
			array(
				'current_user' => $current_user,
			)
		);
	}

	/**
	 * Member account edit content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function member_account_edit_content( $value, $current_user ) {
		Template::get_template(
			'account/edit-account.php',
			array(
				'current_user' => $current_user,
			)
		);
	}

	/**
	 * Member account transactions content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function member_account_transactions_content( $value, $current_user ) {
		$member = $this->member_service->get_member_by_user_id( $current_user->id );
		Template::get_template(
			'account/transactions.php',
			array(
				'current_user' => $current_user,
				'member'       => $member,
			)
		);
	}

	/**
	 * Member account subscriptions content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function member_account_subscriptions_content( $value, $current_user ) {
		$member = $this->member_service->get_member_by_user_id( $current_user->id );
		Template::get_template(
			'account/subscriptions.php',
			array(
				'current_user' => $current_user,
				'member'       => $member,
			)
		);
	}

	/**
	 * View plan page
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function member_account_view_plan_content( $value, $current_user ) {
		global $wp;
		$plan_id = $wp->query_vars['view-plan'];
		if ( empty( $plan_id ) ) {
			echo "<script>
				window.location.href='" . hubloy_membership_get_account_page_links() . "';
			</script>";
		}

		$member     = $this->member_service->get_member_by_user_id( $current_user->id );
		$membership = $this->membership_service->get_membership_by_membership_id( $plan_id );
		if ( $membership ) {
			Template::get_template(
				'account/subscription-plan.php',
				array(
					'current_user' => $current_user,
					'member'       => $member,
					'plan'         => $membership,
				)
			);
		} else {
			// Return 404
		}
	}

	/**
	 * View transaction page
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function member_account_view_transaction_content( $value, $current_user ) {
		global $wp;
		$transaction_id = $wp->query_vars['view-transaction'];
		if ( empty( $transaction_id ) ) {
			echo "<script>
				window.location.href='" . hubloy_membership_get_account_page_links( 'transactions' ) . "';
			</script>";
		}
		$member      = $this->member_service->get_member_by_user_id( $current_user->id );
		$transaction = $this->transaction_service->get_invoice( $transaction_id );
		if ( $transaction ) {
			Template::get_template(
				'account/invoice.php',
				array(
					'current_user' => $current_user,
					'member'       => $member,
					'invoice'      => $transaction,
				)
			);
		} else {
			// 404
		}

	}
}

