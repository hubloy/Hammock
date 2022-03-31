<?php
/**
 * Account join subscription plan page
 * This view is used to show an existing plan to a user
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/plan/view-plan.php.
 *
 * @package HubloyMembership/Templates/Account/Plan/Single/View
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h4 class="hubloy_membership-account-membership-plan--title"><?php echo esc_html( $subscription->name ); ?></h4>
<span class="hubloy_membership-account-membership-plan--details"><?php echo esc_html( $subscription->details ); ?></span>
<?php
if ( $subscription->is_active() ) {
	?>
		<h5><?php esc_html_e( 'Subscription Active', 'hubloy_membership' ); ?></h5>
		<form method="POST" class="hubloy_membership-ajax-form">
			<?php wp_nonce_field( 'hubloy_membership_membership_plan_' . $subscription->id ); ?>
			<input type="hidden" name="action" value="hubloy_membership_deactivate_plan" />
			<input type="hidden" name="plan_id" value="<?php echo esc_attr( $subscription->id ); ?>" />
			<?php
			if ( has_action( 'hubloy_membership_account_view_account_active_plan' ) ) {
				/**
				 * Active plan action.
				 * Used to hook or override actions in this section. Some gateways might have an option to pause.
				 * 
				 * @param \HubloyMembership\Model\Plan $subscription The subscription plan
				 * 
				 * @since 1.0.0
				 */
				do_action( 'hubloy_membership_account_view_account_active_plan', $subscription );
			} else {
				?>
				<button type="submit" class="button hubloy_membership-account-membership-plan--price--can-join">
					<?php esc_html_e( 'Cancel Subscription', 'hubloy_membership' ); ?>
				</button>
				<?php
			}
			?>
		</form>
	<?php
} else {
	?>
	<h5><?php esc_html_e( 'Subscription Not Active', 'hubloy_membership' ); ?></h5>
	<form method="POST" class="hubloy_membership-ajax-form">
		<?php wp_nonce_field( 'hubloy_membership_membership_plan_' . $subscription->id ); ?>
		<input type="hidden" name="action" value="hubloy_membership_activate_plan" />
		<input type="hidden" name="plan_id" value="<?php echo esc_attr( $subscription->id ); ?>" />
		<?php do_action( 'hubloy_membership_account_view_inactive_plan', $subscription ); ?>
		<button type="submit" class="button hubloy_membership-account-membership-plan--price--can-join">
			<?php
			if ( $subscription->gateway_subscription_id ) {
				esc_html_e( 'Reactivate Subscription', 'hubloy_membership' );
			} else {
				esc_html_e( 'Activate Subscription', 'hubloy_membership' );
			}
			?>
		</button>
	</form>
	<?php
}

