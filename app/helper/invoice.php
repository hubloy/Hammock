<?php
namespace HubloyMembership\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Invoice helper
 *
 * @since 1.0.0
 */
class Invoice {

	/**
	 * Generate invoice number based on the id
	 *
	 * @param int $id - the id
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function generate_invoice_number( $id ) {
		return sprintf( "%'.05d\n", $id );
	}
}

