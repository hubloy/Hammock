<?php
/**
 * Account dashboard navigation
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/account/navigation.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


do_action( 'hubloy_membership_before_member_account_navigation_render' );

?>
<nav class="hubloy_membership-member-account-navigation">
	<ul>
		<?php foreach ( hubloy_membership_account_member_navigation_labels() as $endpoint => $label ) : ?>
			<li class="<?php echo esc_attr( hubloy_membership_account_member_navigation_link_class( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( hubloy_membership_get_account_page_links( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php

do_action( 'hubloy_membership_after_member_account_navigation_render' );
