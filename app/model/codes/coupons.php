<?php
namespace HubloyMembership\Model\Codes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Model\Codes;
use HubloyMembership\Model\Usage;

/**
 * Coupon codes
 *
 * @since 1.0.0
 */
class Coupons extends Codes {

	/**
	 * The usage model
	 * 
	 * @since 1.1.0
	 * 
	 * @var object
	 */
	private $usage = null;

	/**
	 * Initialize model
	 *
	 * @since 1.0.0
	 */
	protected function init() {
		$this->usage     = new Usage();
		$this->code_type = 'coupons';
	}

	/**
	 * Get usage per email.
	 * 
	 * @param string $email The user email.
	 * 
	 * @since 1.1.0
	 * 
	 * @return int
	 */
	public function get_usage( $email ) {
		$this->usage->get_one( $this->id, $this->code_type, $email );
		return $this->usage->get_usage();
	}

	/**
	 * Record coupon usage
	 * 
	 * @param string $email The user email
	 * 
	 * @since 1.0.0
	 */
	public function record_usage( $email ) {
		$this->usage->get_one( $this->id, $this->code_type, $email );
		$this->usage->register_usage();
		$this->usage->save();
	}
}
