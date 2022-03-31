<?php
/**
 * Account subscription plan page
 * renders a users subscription plan
 * This view is used to join a plan or to edit an existing plan
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/subscription-plan.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_before_account_subscription_plan', $plan );

if ( hubloy_membership_current_user_has_plan( $plan->id ) ) {
	$subscription = hubloy_membership_get_member_plan_subscription( get_current_user_id(), $plan->id );
	hubloy_membership_get_template(
		'account/plan/single/view-plan.php',
		array(
			'plan'         => $plan,
			'subscription' => $subscription,
		)
	);
} else {
	hubloy_membership_get_template(
		'account/plan/single/join-plan.php',
		array(
			'plan' => $plan,
		)
	);
}

do_action( 'hubloy_membership_after_account_subscription_plan', $plan );


