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
				echo hammock_format_currency( $plan->trial_price );
				?>
				<span class="hammock-account-membership-plan--price-description">
					<?php
						$trial_text	 	= $plan->get_readable_trial_text();
						$normal_price 	= hammock_format_currency( $plan->price );
						$sub_text 		= $plan->get_readable_type();
						echo sprintf( __( '%s then %s %s', 'hammock' ), $trial_text, $normal_price, $sub_text );
					?>
				</span>
				<?php
			} else {
				if ( $plan->is_recurring() && $plan->signup_price > 0 ) {
					echo hammock_format_currency( $plan->signup_price );
					?>
					<span class="hammock-account-membership-plan--price-description">
						<?php
							$normal_price 	= hammock_format_currency( $plan->price );
							$sub_text 		= $plan->get_readable_type();
							echo sprintf( __( 'then %s %s', 'hammock' ), $normal_price, $sub_text );
						?>
					</span>
					<?php
				} else {
					echo hammock_format_currency( $plan->price );
					?>
					<span class="hammock-account-membership-plan--price-description">
						<?php
							echo $plan->get_readable_type();
						?>
					</span>
					<?php
				}
				
			}
		?>

		<?php 
		$can_join = hammock_can_user_join_plan( $plan->id );
		if ( $can_join['status'] ) : ?>
			<a class="button hammock-account-membership-plan--price--can-join" href="<?php echo esc_url( hammock_get_account_page_links( 'view-plan', $plan->membership_id ) ); ?>">
				<?php
					if ( $plan->trial_enabled ) {
						_e( 'Begin Trial', 'hammock' );
					} else {
						_e( 'Join Membership', 'hammock' );
					}
				?>
			</a>
		<?php else :?>
			<p class="hammock-account-membership-plan--price--cant-join"><?php echo $can_join['message']; ?></p>
		<?php endif; ?>
	</p>
	
</div>