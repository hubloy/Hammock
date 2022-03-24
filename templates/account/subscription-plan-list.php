<?php
/**
 * Account subscription plan list page
 * renders a users subscription plan
 * This view is used to list plans
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/subscription-plan-list.php.
 *
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="hammock-account-membership-plan hammock-account-membership-plan-<?php echo esc_attr( $plan->id ); ?>">
	<?php
		hammock_get_template(
			'account/plan/list/view-plan.php',
			array(
				'plan'   => $plan,
				'member' => $member,
			)
		);
		?>
</tr>
