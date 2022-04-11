<?php
/**
 * Account subscription plan page
 * renders a users subsscription plan
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/subscription-plan.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hubloy_membership-account-membership-plan hubloy_membership-account-membership-plan-card hubloy_membership-account-membership-plan-<?php echo $plan->id; ?>">
	<h4 class="hubloy_membership-account-membership-plan--title"><?php echo $plan->name; ?></h4>
	<span class="hubloy_membership-account-membership-plan--details"><?php echo $plan->details; ?></span>
	<p class="hubloy_membership-account-membership-plan--price">
		
		<?php
		if ( $plan->trial_enabled ) {
			echo esc_html( hubloy_membership_format_currency( $plan->trial_price ) );
			?>
				<span class="hubloy_membership-account-membership-plan--price-description">
				<?php
					$trial_text   = $plan->get_readable_trial_text();
					$normal_price = hubloy_membership_format_currency( $plan->price );
					$sub_text     = $plan->get_readable_type();
					echo sprintf( esc_html__( '%1$s then %2$s %3$s', 'memberships-by-hubloy' ), esc_attr( $trial_text ), esc_attr( $normal_price ), esc_attr( $sub_text ) );
				?>
				</span>
				<?php
		} else {
			if ( $plan->is_recurring() && $plan->signup_price > 0 ) {
				echo esc_html( hubloy_membership_format_currency( $plan->signup_price ) );
				?>
					<span class="hubloy_membership-account-membership-plan--price-description">
					<?php
						$normal_price = hubloy_membership_format_currency( $plan->price );
						$sub_text     = $plan->get_readable_type();
						echo sprintf( esc_html__( 'then %1$s %2$s', 'memberships-by-hubloy' ), esc_attr( $normal_price ), esc_attr( $sub_text ) );
					?>
					</span>
					<?php
			} else {
				echo hubloy_membership_format_currency( $plan->price );
				?>
					<span class="hubloy_membership-account-membership-plan--price-description">
					<?php
						echo esc_html( $plan->get_readable_type() );
					?>
					</span>
					<?php
			}
		}
		?>

		<?php
		$can_join = hubloy_membership_can_user_join_plan( $plan->id );
		if ( $can_join['status'] ) :
			?>
			<a class="button hubloy_membership-account-membership-plan--price--can-join" href="<?php echo esc_url( hubloy_membership_get_account_page_links( 'view-plan', $plan->membership_id ) ); ?>">
				<?php
				if ( $plan->trial_enabled ) {
					esc_html_e( 'Begin Trial', 'memberships-by-hubloy' );
				} else {
					esc_html_e( 'Join Membership', 'memberships-by-hubloy' );
				}
				?>
			</a>
		<?php else : ?>
			<p class="hubloy_membership-account-membership-plan--price--cant-join"><?php echo esc_html( $can_join['message'] ); ?></p>
		<?php endif; ?>
	</p>
	
</div>
