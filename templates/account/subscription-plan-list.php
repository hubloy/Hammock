<?php
/**
 * Account subscription plan list page
 * renders a users subscription plan
 * This view is used to list plans
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/subscription-plan-list.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="hubloy_membership-account-membership-plan hubloy_membership-account-membership-plan-<?php echo esc_attr( $plan->id ); ?>">
	<?php
		hubloy_membership_get_template(
			'account/plan/list/view-plan.php',
			array(
				'plan'   => $plan,
				'member' => $member,
			)
		);
		?>
</tr>
