<?php
namespace Hammock\Controller\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Model\Membership;
use Hammock\Model\Plan;
use Hammock\Services\Members;
use Hammock\Services\Memberships;
use Hammock\Services\Transactions;

/**
 * Signup controller
 * This manages front end functions for the signup process
 *
 * @since 1.0.0
 */
class Signup extends Controller {

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
	 * Transaction service
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

		$this->add_ajax_action( 'hammock_purchase_plan', 'purchase_plan', true, true );
		$this->add_ajax_action( 'hammock_deactivate_plan', 'deactivate_plan' );
		$this->add_ajax_action( 'hammock_activate_plan', 'activate_plan' );
	}


	/**
	 * First step in purchasing a plan
	 *
	 * @since 1.0.0
	 */
	public function purchase_plan() {
		$plan_id = absint( sanitize_text_field( $_POST['plan_id'] ) );
		$this->verify_nonce( 'hammock_membership_plan_' . $plan_id );

		$membership = new Membership( $plan_id );
		if ( $membership->id > 0 ) {

			$can_join = hammock_can_user_join_plan( $plan_id );
			if ( ! $can_join['status'] ) {
				wp_send_json_error( $can_join['message'] );
			}

			$user_id = get_current_user_id();

			$member = $this->member_service->get_member_by_user_id( $user_id );
			if ( ! $member ) {
				$response = $this->member_service->save_member( $user_id );
				if ( $response['status'] ) {
					$member_id = $response['id'];
					$member    = $this->member_service->get_member_by_id( $member_id );
				}
			}

			if ( ! $member || $member->id <= 0 ) {
				wp_send_json_error( __( 'Error getting member profile. Please try again', 'hammock' ) );
			}

			$plan = $member->add_plan( $membership );
			if ( $plan ) {

				// Create transaction
				$due_date = date_i18n( 'Y-m-d H:i:s', strtotime( 'now' ) );

				if ( $plan->has_trial() ) {
					// After trial
					$due_date = $plan->end_date;
				}

				/**
				 * Filter to modify the due date on auto-generate plans
				 *
				 * @param string $due_date - the due date. Defaults to now
				 * @param object $member - the member object
				 * @param object $membership - the membership object
				 * @param object $plan - the plan
				 *
				 * @since 1.0.0
				 *
				 * @return $due_date
				 */
				$due_date = apply_filters( 'hammock_new_plan_invoice_due_date', $due_date, $member, $membership, $plan );

				/**
				 * Save transaction
				 * This will save a transaction and send an email to the user
				 */
				$invoice_id = $this->transaction_service->save_transaction( '', Transactions::STATUS_PENDING, $member, $plan, $membership->get_price(), $due_date );

				if ( $invoice_id ) {
					wp_send_json_success(
						array(
							'message' => __( 'Plan joined. Proceeding to payments', 'hammock' ),
							'url'     => hammock_get_invoice_link( $invoice_id ),
						)
					);
				}
			}
		}
		wp_send_json_error( __( 'Error adding plan to your account. Please try again', 'hammock' ) );
	}

	/**
	 * Deactivate plan
	 * 
	 * @since 1.0.0
	 */
	public function deactivate_plan() {
		$plan_id = absint( sanitize_text_field( $_POST['plan_id'] ) );
		$this->verify_nonce( 'hammock_membership_plan_' . $plan_id );

		$plan = $this->get_user_plan( $plan_id );
		if ( $plan ) {

			/**
			 * Action called before plan is deactivated
			 * 
			 * @param \Hammock\Model\Plan $plan The subscription plan
			 * 
			 * @since 1.0.0
			 */
			do_action( 'hammock_account_before_deactivate_plan', $plan );

			$plan->cancel();

			wp_send_json_success(
				array(
					'message' => __( 'Plan canceled', 'hammock' ),
					'reload'  => true,
				)
			);
			
		}
		wp_send_json_error( __( 'Plan is not linked to your account', 'hammock' ) );
	}

	/**
	 * Activate or purchase plan
	 * 
	 * @since 1.0.0
	 */
	public function activate_plan() {
		$plan_id = absint( sanitize_text_field( $_POST['plan_id'] ) );
		$this->verify_nonce( 'hammock_membership_plan_' . $plan_id );

		$plan = $this->get_user_plan( $plan_id );
		if ( $plan ) {
			$plan->set_pending();
			$invoice_id = $this->transaction_service->get_pending_invoice( $plan );
			if ( $invoice_id ) {
				wp_send_json_success(
					array(
						'message' => __( 'Plan joined. Proceeding to payments', 'hammock' ),
						'url'     => hammock_get_invoice_link( $invoice_id ),
					)
				);
			}
		}
		wp_send_json_error( __( 'Plan is not linked to your account', 'hammock' ) );
	}

	/**
	 * Get user plan
	 * 
	 * @param int $plan_id The plan id
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool|object
	 */
	private function get_user_plan( $plan_id ) {
		$user_id = get_current_user_id();
		$member  = $this->member_service->get_member_by_user_id( $user_id );
		if ( ! $member || ! $member->exists() ) {
			return false;
		}
		$plan = Plan::get_plan( $member->id, $plan_id );
		if ( ! $plan || ! $plan->exists() ) {
			return false;
		}
		return $plan;
	}
}

