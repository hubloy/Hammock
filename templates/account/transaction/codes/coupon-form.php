<?php
/**
 * Coupon form
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/account/transaction/codes/coupon-form.php.
 *
 * @package HubloyMembership/Templates/Account/Transaction/Codes/CouponForm
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="coupon">
	<td>
		<p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'memberships-by-hubloy' ); ?></p>
		<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'memberships-by-hubloy' ); ?>" id="coupon_code" value="" />
	</td>
	<td>
		<button class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'memberships-by-hubloy' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
	</td>
</tr>