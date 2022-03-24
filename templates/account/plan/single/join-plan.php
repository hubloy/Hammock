<?php
/**
 * Account join subscription plan page
 * This view is used to join a plan
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/plan/join-plan.php.
 *
 * @package Hammock/Templates/Account/Plan/Single/Join
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hammock-account-membership-plan hammock-account-join-membership-plan-<?php echo esc_attr( $plan->id ); ?>">
	<h4 class="hammock-account-membership-plan--title"><?php echo esc_html( $plan->name ); ?></h4>
	<span class="hammock-account-membership-plan--details"><?php echo esc_html( $plan->details ); ?></span>
	<p class="hammock-account-membership-plan--price">
		
		<?php
		if ( $plan->trial_enabled ) {
			echo esc_html( hammock_format_currency( $plan->trial_price ) );
			?>
				<span class="hammock-account-membership-plan--price-description">
				<?php
					$trial_text   = $plan->get_readable_trial_text();
					$normal_price = hammock_format_currency( $plan->price );
					$sub_text     = $plan->get_readable_type();
					echo sprintf( esc_html__( '%1$s then %2$s %3$s', 'hammock' ), esc_html( $trial_text ), esc_attr( $normal_price ), esc_html( $sub_text ) );
				?>
				</span>
				<?php
		} else {
			if ( $plan->is_recurring() && $plan->signup_price > 0 ) {
				echo esc_html( hammock_format_currency( $plan->signup_price ) );
				?>
					<span class="hammock-account-membership-plan--price-description">
					<?php
						$normal_price = hammock_format_currency( $plan->price );
						$sub_text     = $plan->get_readable_type();
						echo sprintf( esc_html__( 'then %1$s %2$s', 'hammock' ), esc_attr( $normal_price ), esc_html( $sub_text ) );
					?>
					</span>
					<?php
			} else {
				echo hammock_format_currency( $plan->price );
				?>
					<span class="hammock-account-membership-plan--price-description">
					<?php
						echo esc_html( $plan->get_readable_type() );
					?>
					</span>
					<?php
			}
		}
		?>

		<?php
		$can_join = hammock_can_user_join_plan( $plan->id );
		if ( $can_join['status'] ) :
			?>
			<form method="POST" class="hammock-ajax-form">
				<?php wp_nonce_field( 'hammock_membership_plan_' . $plan->id ); ?>
				<input type="hidden" name="action" value="hammock_purchase_plan" />
				<input type="hidden" name="plan_id" value="<?php echo esc_attr( $plan->id ); ?>" />
				<button type="submit" class="button hammock-account-membership-plan--price--can-join">
				<?php
				if ( $plan->trial_enabled ) {
					esc_html_e( 'Begin Trial', 'hammock' );
				} else {
					esc_html_e( 'Start Membership', 'hammock' );
				}
				?>
				</button>
			</form>
		<?php else : ?>
			<p class="hammock-account-membership-plan--price--cant-join"><?php echo esc_html( $can_join['message'] ); ?></p>
		<?php endif; ?>
	</p>
</div>
