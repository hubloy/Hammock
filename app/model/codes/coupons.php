<?php
namespace Hammock\Model\Codes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Model\Codes;

/**
 * Coupon codes
 *
 * @since 1.0.0
 */
class Coupons extends Codes {

	/**
	 * Initialize model
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->code_type = 'coupons';
	}
}


