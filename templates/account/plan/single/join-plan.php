<?php
/**
 * Account join subscription plan page
 * This view is used to join a plan
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/plan/join-plan.php.
 *
 * @package HubloyMembership/Templates/Account/Plan/Single/Join
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hubloy_membership-account-membership-plan hubloy_membership-account-join-membership-plan-<?php echo esc_attr( $plan->id ); ?>">
	<h4 class="hubloy_membership-account-membership-plan--title"><?php echo esc_html( $plan->name ); ?></h4>
	<span class="hubloy_membership-account-membership-plan--details"><?php echo esc_html( $plan->details ); ?></span>
	<p class="hubloy_membership-account-membership-plan--price">
		
		<?php
			hubloy_membership_get_template(
				'account/plan/single/membership-price.php',
				array(
					'plan' => $plan,
				)
			);
		?>
		<?php
		$can_join = hubloy_membership_can_user_join_plan( $plan->id );
		if ( $can_join['status'] ) :
			?>
			<form method="POST" class="hubloy_membership-ajax-form">
				<?php wp_nonce_field( 'hubloy_membership_membership_plan_' . $plan->id ); ?>
				<input type="hidden" name="action" value="hubloy_membership_purchase_plan" />
				<input type="hidden" name="plan_id" value="<?php echo esc_attr( $plan->id ); ?>" />
				<button type="submit" class="button hubloy_membership-account-membership-plan--price--can-join">
				<?php
				if ( $plan->trial_enabled ) {
					esc_html_e( 'Begin Trial', 'hubloy_membership' );
				} else {
					esc_html_e( 'Start Membership', 'hubloy_membership' );
				}
				?>
				</button>
			</form>
		<?php else : ?>
			<p class="hubloy_membership-account-membership-plan--price--cant-join"><?php echo esc_html( $can_join['message'] ); ?></p>
		<?php endif; ?>
	</p>
</div>
