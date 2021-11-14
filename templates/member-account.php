<?php
/**
 * Member account dashboard
 *
 * This template can be overridden by copying it to yourtheme/hammock/member-account.php.
 *
 * @package Hammock/Templates
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="hammock-member-account">
	<?php
	/**
	 * The account navigation
	 *
	 * @since 1.0.0
	 */
	do_action( 'hammock_member_account_navigation' );

	?>
	<div class="hammock-member-account-content">

		<?php
			/**
			 * The account dashboard content
			 *
			 * @since 1.0.0
			 */
			do_action( 'hammock_member_account_content' );
		?>

	</div>
</div>
