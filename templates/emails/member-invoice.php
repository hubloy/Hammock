<?php
/**
 * Member Invoice
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/emails/member-invoice.php.
 *
 * @package HubloyMembership/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_email_header', $heading, $email );
?>
<p><?php printf( esc_html__( 'Hi %s,', 'memberships-by-hubloy' ), esc_html( $object->user_name ) ); ?>
<p><?php esc_html_e( 'A new invoice has been generated', 'memberships-by-hubloy' ); ?>
<?php
	hubloy_membership_get_template(
		'account/transaction/single/view-transaction.php',
		array(
			'invoice' => $object->invoice,
			'member'  => $object->member,
		)
	);
?>
<p><?php printf( esc_html_e( '%sView Invoice%s', 'memberships-by-hubloy' ), '<a href="' . esc_url( hubloy_membership_get_invoice_link( $object->invoice->invoice_id ) ) . '">', '</a>' ); ?>
<?php
do_action( 'hubloy_membership_email_footer', $email );
