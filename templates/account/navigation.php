<?php
/**
 * Account dashboard navigation
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/navigation.php.
 *
 * @package Hammock/Templates/Account
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


do_action( 'hammock_before_member_account_navigation_render' );

?>
<nav class="hammock-member-account-navigation">
	<ul>
		<?php foreach ( hammock_account_member_navigation_labels() as $endpoint => $label ) : ?>
			<li class="<?php echo esc_attr( hammock_account_member_navigation_link_class( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( hammock_get_account_page_links( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php

do_action( 'hammock_after_member_account_navigation_render' );
