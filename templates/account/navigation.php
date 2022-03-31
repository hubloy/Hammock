<?php
/**
 * Account dashboard navigation
 *
 * This template can be overridden by copying it to yourtheme/hubloy-membership/account/navigation.php.
 *
 * @package HubloyMembership/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


do_action( 'hubloy-membership_before_member_account_navigation_render' );

?>
<nav class="hubloy-membership-member-account-navigation">
	<ul>
		<?php foreach ( hubloy-membership_account_member_navigation_labels() as $endpoint => $label ) : ?>
			<li class="<?php echo esc_attr( hubloy-membership_account_member_navigation_link_class( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( hubloy-membership_get_account_page_links( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php

do_action( 'hubloy-membership_after_member_account_navigation_render' );
