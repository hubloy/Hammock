<?php
namespace HubloyMembership\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pages Services
 */
class Pages {

	/**
	 * Default plugin pages
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function plugin_pages() {
		return array(
			'membership_list'   => array(
				'title'     => __( 'Membership List', 'hubloy-membership' ),
				'shortcode' => '[hubloy-membership_membership_list]',
				'slug'      => 'memberships',
			),
			'protected_content' => array(
				'title'     => __( 'Protected Content', 'hubloy-membership' ),
				'shortcode' => '[hubloy-membership_protected_content]',
				'slug'      => 'protected-content',
			),
			'account_page'      => array(
				'title'     => __( 'Account', 'hubloy-membership' ),
				'shortcode' => '[hubloy-membership_account_page]',
				'slug'      => 'account',
			),
		);
	}

	/**
	 * Page endpoints
	 * These are all the endpoint urls used in the frontend
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function page_endpoits() {
		return apply_filters(
			'hubloy-membership_page_endpoits',
			array(
				'memberships'       => apply_filters( 'hubloy-membership_membership_endpoint', 'memberships' ),
				'view-plan'         => apply_filters( 'hubloy-membership_membership_plan_endpoint', 'view-plan' ),
				'protected-content' => apply_filters( 'hubloy-membership_protected_content_endpoint', 'protected-content' ),
				'account'           => apply_filters( 'hubloy-membership_account_endpoint', 'account' ),
				'edit-account'      => apply_filters( 'hubloy-membership_edit_account_endpoint', 'edit-account' ),
				'transactions'      => apply_filters( 'hubloy-membership_transactions_endpoint', 'transactions' ),
				'subscriptions'     => apply_filters( 'hubloy-membership_subscriptions_endpoint', 'subscriptions' ),
				'lost-password'     => apply_filters( 'hubloy-membership_lost_password_endpoint', 'lost-password' ),
				'member-logout'     => apply_filters( 'hubloy-membership_member_logout_endpoint', 'member-logout' ),
			)
		);
	}

	/**
	 * Account pae endpoints
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function account_page_endpoits() {
		return apply_filters(
			'hubloy-membership_account_page_endpoits',
			array(
				'edit-account'  => apply_filters( 'hubloy-membership_edit_account_endpoint', 'edit-account' ),
				'transactions'  => apply_filters( 'hubloy-membership_ttransactions_endpoint', 'transactions' ),
				'subscriptions' => apply_filters( 'hubloy-membership_subscriptions_endpoint', 'subscriptions' ),
				'member-logout' => apply_filters( 'hubloy-membership_member_logout_endpoint', 'member-logout' ),
				'view-plan'     => apply_filters( 'hubloy-membership_membership_plan_endpoint', 'view-plan' ),
			)
		);
	}
}

