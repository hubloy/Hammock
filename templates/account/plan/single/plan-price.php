<?php
/**
 * Account plan price
 * This view is used to show the plan price
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/plan/plan-price.php.
 *
 * @package Hammock/Templates/Account/Plan/Single
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$membership = $plan->get_memebership();
if ( $plan->has_trial() ) {
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
	if ( $membership->is_recurring() && $membership->signup_price > 0 ) {
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
		echo hammock_format_currency( $membership->price );
		?>
		<span class="hammock-account-membership-plan--price-description">
		<?php
			echo esc_html( $membership->get_readable_type() );
		?>
		</span>
		<?php
	}
}
