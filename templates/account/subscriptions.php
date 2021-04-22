<?php
/**
 * Account subscriptions method page
 * Manage users subscriptions
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/subscriptions.php.
 * 
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !hammock_current_user_can_subscribe() ) {
	_e( 'Subscriptions are not enabled for your account', 'hammock' );
} else {
	if ( $member ) {
		if ( count( $member->get_plan_ids() ) > 0 ) {
			?>
			<table class="hammock-account-subscription hammock-list-table">
				<thead>
					<tr>
						<?php
							foreach ( hammock_view_subscription_list_table_columns() as $key => $value ) {
								?>
								<th class="<?php echo $key; ?>"><?php echo $value; ?></th>
								<?php
							}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $member->get_plans() as $plan ) {
						hammock_get_template( 'account/subscription-plan-list.php', array(
							'plan'		=> $plan,
							'member'	=> $member
						));
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<?php
							foreach ( hammock_view_subscription_list_table_columns() as $key => $value ) {
								?>
								<th class="<?php echo $key; ?>"><?php echo $value; ?></th>
								<?php
							}
						?>
					</tr>
				</tfoot>
				
			</table>
			<?php
		} else {
			printf( __( 'No subscription plans found. Click %shere%s to sign up', 'hammock' ), '<a href="' . hammock_get_page_permalink( 'membership_list' ) . '">', '</a>' );
		}
	} else {
		_e( 'You have no subscriptions in your account', 'hammock' );
	}
}