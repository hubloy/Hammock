<?php
/**
 * Account payment method
 *
 * This template can be overridden by copying it to yourtheme/hubloy-membership/account/transaction/single/payment-method.php.
 *
 * @package HubloyMembership/Templates/Account/Transaction/Single/Pay
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="hubloy-membership-payment_method hubloy-membership-payment_method_<?php echo esc_attr( $gateway_id ); ?>">
	<input id="payment_method_<?php echo esc_attr( $gateway_id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway_id ); ?>" <?php checked( $invoice->gateway, $gateway_id ); ?> />

	<label for="payment_method_<?php echo esc_attr( $gateway_id ); ?>">
		<?php echo esc_html( $gateway_name ) ?> <?php echo apply_filters( 'hubloy-membership_payment_method_icon_' . $gateway_id, '' ); ?>
	</label>
	<?php do_action( 'hubloy-membership_payment_method_fields_' . $gateway_id, $invoice ); ?>
</li>
