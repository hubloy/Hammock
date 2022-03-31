<?php
/**
 * Membership list
 *
 * This template can be overridden by copying it to yourtheme/hubloy-membership/membership-list.php.
 *
 * @package HubloyMembership/Templates
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="hubloy-membership-membership-list">

	<?php
		$plans = hubloy-membership_get_plans_to_join();

	if ( ! empty( $plans ) ) {
		foreach ( $plans as $plan ) {
			hubloy-membership_get_template(
				'plans/single-plan-card.php',
				array(
					'plan' => $plan,
				)
			);
		}
	} else {
		esc_html_e( 'No membership plans to subscribe to', 'hubloy-membership' );
	}
	?>
</div>
