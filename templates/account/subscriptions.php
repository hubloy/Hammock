<?php
/**
 * Account subscriptions method page
 * Manage users subscriptions
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/subscriptions.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! hubloy_membership_current_user_can_subscribe() ) {
	esc_html_e( 'Subscriptions are not enabled for your account', 'memberships-by-hubloy' );
} else {
	if ( $member ) {
		if ( count( $member->get_plan_ids() ) > 0 ) {
			?>
			<table class="hubloy_membership-account-subscription hubloy_membership-list-table">
				<thead>
					<tr>
						<?php
						foreach ( hubloy_membership_view_subscription_list_table_columns() as $key => $value ) {
							?>
								<th class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></th>
							<?php
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $member->get_plans() as $plan ) {
						hubloy_membership_get_template(
							'account/subscription-plan-list.php',
							array(
								'plan'   => $plan,
								'member' => $member,
							)
						);
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<?php
						foreach ( hubloy_membership_view_subscription_list_table_columns() as $key => $value ) {
							?>
								<th class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></th>
							<?php
						}
						?>
					</tr>
				</tfoot>
				
			</table>
			<?php
		} else {
			printf( esc_html__( 'No subscription plans found. Click %1$shere%2$s to sign up', 'memberships-by-hubloy' ), '<a href="' . esc_url( hubloy_membership_get_page_permalink( 'membership_list' ) ) . '">', '</a>' );
		}
	} else {
		esc_html_e( 'You have no subscriptions in your account', 'memberships-by-hubloy' );
	}
}
