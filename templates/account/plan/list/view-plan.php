<?php
/**
 * Account join subscription plan page
 * This view is used as a row in the plan table
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/plan/list/view-plan.php.
 *
 * @package Hammock/Templates/Account/Plan/List/View
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$membership = $plan->get_memebership();
?>
<td class="hammock-account-membership-plan--title"><?php echo esc_html( $membership->name ); ?></td>
<td class="hammock-account-membership-plan--status"><?php echo esc_html( $plan->status_detail ); ?></td>
<td class="hammock-account-membership-plan--price">
	
	<?php
	if ( $membership->trial_enabled && $member->can_trial( $membership->id ) ) {
		echo esc_html( hammock_format_currency( $membership->trial_price ) );
		?>
			<span class="hammock-account-membership-plan--price-description">
			<?php
				$trial_text   = $membership->get_readable_trial_text();
				$normal_price = hammock_format_currency( $membership->price );
				$sub_text     = $membership->get_readable_type();
				echo sprintf( esc_html__( '%1$s then %2$s %3$s', 'hammock' ), esc_html( $trial_text ), esc_attr( $normal_price ), esc_html( $sub_text ) );
			?>
			</span>
			<?php
	} else {
		if ( ! $member->has_subscribed_before( $membership->id ) && $membership->is_recurring() && $membership->signup_price > 0 ) {
			echo esc_html( hammock_format_currency( $membership->signup_price ) );
			?>
				<span class="hammock-account-membership-plan--price-description">
				<?php
					$normal_price = hammock_format_currency( $membership->price );
					$sub_text     = $membership->get_readable_type();
					echo sprintf( esc_html__( 'then %1$s %2$s', 'hammock' ), esc_attr( $normal_price ), esc_html( $sub_text ) );
				?>
				</span>
				<?php
		} else {
			echo esc_html( hammock_format_currency( $membership->price ) );
			?>
			<span class="hammock-account-membership-plan--price-description">
			<?php
				echo esc_html( $membership->get_readable_type() );
			?>
			</span>
			<?php
		}
	}
	?>

	
</td>
<td class="hammock-account-membership-plan--payment">
	<a class="hammock-account-membership-plan--price--can-join" href="<?php echo esc_url( hammock_get_account_page_links( 'view-plan', $membership->membership_id ) ); ?>">
		<?php
		if ( ! $plan->is_active() ) {
			esc_html_e( 'Inactive', 'hammock' );
		} else {
			esc_html_e( 'Active', 'hammock' );
		}
		?>
	</a>
</td>
