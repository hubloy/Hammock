<?php
/**
 * Member account dashboard
 *
 * This template can be overridden by copying it to yourtheme/hubloy_membership/member-account.php.
 *
 * @package HubloyMembership/Templates
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="hubloy_membership-member-account">
	<?php
	/**
	 * The account navigation
	 *
	 * @since 1.0.0
	 */
	do_action( 'hubloy_membership_member_account_navigation' );

	?>
	<div class="hubloy_membership-member-account-content">

		<?php
			/**
			 * The account dashboard content
			 *
			 * @since 1.0.0
			 */
			do_action( 'hubloy_membership_member_account_content' );
		?>

	</div>
</div>
