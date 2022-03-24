<?php
/**
 * Account subscription plan page
 * renders a users subsscription plan
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/subscription-plan.php.
 *
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hammock-account-membership-plan hammock-account-membership-plan-card hammock-account-membership-plan-<?php echo $plan->id; ?>">
	<h4 class="hammock-account-membership-plan--title"><?php echo $plan->name; ?></h4>
	<span class="hammock-account-membership-plan--details"><?php echo $plan->details; ?></span>
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
					echo sprintf( esc_html__( '%1$s then %2$s %3$s', 'hammock' ), esc_attr( $trial_text ), esc_attr( $normal_price ), esc_attr( $sub_text ) );
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
						echo sprintf( esc_html__( 'then %1$s %2$s', 'hammock' ), esc_attr( $normal_price ), esc_attr( $sub_text ) );
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
			<a class="button hammock-account-membership-plan--price--can-join" href="<?php echo esc_url( hammock_get_account_page_links( 'view-plan', $plan->membership_id ) ); ?>">
				<?php
				if ( $plan->trial_enabled ) {
					esc_html_e( 'Begin Trial', 'hammock' );
				} else {
					esc_html_e( 'Join Membership', 'hammock' );
				}
				?>
			</a>
		<?php else : ?>
			<p class="hammock-account-membership-plan--price--cant-join"><?php echo esc_html( $can_join['message'] ); ?></p>
		<?php endif; ?>
	</p>
	
</div>
