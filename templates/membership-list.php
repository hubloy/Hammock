<?php
/**
 * Membership list
 *
 * This template can be overridden by copying it to yourtheme/hammock/membership-list.php.
 *
 * @package Hammock/Templates
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="hammock-membership-list">

	<?php
		$plans = hammock_get_plans_to_join();

	if ( ! empty( $plans ) ) {
		foreach ( $plans as $plan ) {
			hammock_get_template(
				'plans/single-plan-card.php',
				array(
					'plan' => $plan,
				)
			);
		}
	} else {
		esc_html_e( 'No membership plans to subscribe to', 'hammock' );
	}
	?>
</div>
