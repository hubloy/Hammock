<?php
/**
 * Membership plans
 * These functions can be used within themes or external resources
 *
 * @package HubloyMembership/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * This lists the plans a member can join
 *
 * @since 1.0.0
 *
 * @return array
 */
function hubloy-membership_get_plans_to_join() {
	// Get current user id. If its 0 it means not logged in
	$current_user_id = get_current_user_id();

	$service = new \HubloyMembership\Services\Memberships();

	return $service->list_signup_memberships( $current_user_id );
}

/**
 * Checks if a user can join the current plan
 * checks if a plan is set to use an invite code
 *
 * @param int $plan_id - the plan id
 *
 * @since 1.0.0
 *
 * @return array(
 *      status => bool
 *      message => reason
 * )
 */
function hubloy-membership_can_user_join_plan( $plan_id ) {
	if ( is_user_logged_in() ) {
		if ( ! hubloy-membership_current_user_can_subscribe() ) {
			return array(
				'status'  => false,
				'message' => __( 'Restricted Subscription' ),
			);
		} else {
			// Check if user is already subscribed
			$has_plan = hubloy-membership_current_user_has_plan( $plan_id );
			if ( $has_plan ) {
				return array(
					'status'  => false,
					'message' => __( 'Already Subscribed' ),
				);
			}
		}
	}
	return array(
		'status'  => true,
		'message' => '',
	);
}

/**
 * Check if current user has a plan
 *
 * @param int $plan_id - the plan id
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hubloy-membership_current_user_has_plan( $plan_id ) {
	$member_service = new \HubloyMembership\Services\Members();
	$user_id        = get_current_user_id();
	$member         = $member_service->get_member_by_user_id( $user_id );
	if ( $member && $member->id > 0 ) {
		$plan_ids = $member->get_plan_ids();
		foreach ( $plan_ids as $plan ) {
			if ( $plan->id === $plan_id ) {
				return true;
			}
		}
	}
	return false;
}


/**
 * Get plan by id
 *
 * @param int $plan_id - the plan id
 *
 * @return object|bool
 */
function hubloy-membership_get_plan_by_id( $plan_id ) {
	$service = new \HubloyMembership\Services\Memberships();
	return $service->get_membership_by_membership_id( $plan_id );
}

/**
 * Get a subscription of a users plan
 * This gets a users subscription of a plan
 *
 * @param int $user_id - the user id
 * @param int $plan_id - the plan id
 *
 * @since 1.0.0
 *
 * @return object|bool
 */
function hubloy-membership_get_member_plan_subscription( $user_id, $plan_id ) {
	$member_service = new \HubloyMembership\Services\Members();
	$member         = $member_service->get_member_by_user_id( $user_id );
	if ( $member && $member->exists() ) {
		$plan = \HubloyMembership\Model\Plan::get_plan( $member->id, $plan_id );
		return $plan;
	}
	return false;
}

