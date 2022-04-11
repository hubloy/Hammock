<?php
/**
 * Admin invoice copy
 *
 * This template can be overridden by copying it to yourtheme/memberships-by-hubloy/emails/admin/invoice.php.
 *
 * @package HubloyMembership/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hubloy_membership_email_header', $heading, $email );
?>
<p><?php esc_html_e( 'Hello,', 'memberships-by-hubloy' ); ?>
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
