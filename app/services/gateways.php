<?php
namespace HubloyMembership\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Gateways service
 *
 * @since 1.0.0
 */
class Gateways {

	/**
	 * Load gatewats
	 * The return filter is completed in each gateway class and initiated in the `hubloy_membership_init_gateway` hook
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function load_gateways() {
		$gateways = apply_filters( 'hubloy_membership_register_gateways', array() );
		ksort( $gateways );
		return $gateways;
	}

	/**
	 * Gateways drop down
	 * This loads a key value array used in dropdowns. The result is
	 *
	 * array (
	 *      'gateway' => 'Gateway Name'
	 * )
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function list_simple_gateways( $include_select = true ) {
		$gateways = self::load_gateways();
		if ( $include_select ) {
			$drop_down = array(
				'' => __( 'Select Gateway', 'memberships-by-hubloy' ),
			);
		} else {
			$drop_down = array();
		}
		foreach ( $gateways as $key => $value ) {
			$drop_down[ $key ] = $value['name'];
		}
		return apply_filters( 'hubloy_membership_list_simple_gateways', $drop_down );
	}

	/**
	 * List active gateways
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function list_active_gateways() {
		$gateways        = self::load_gateways();
		$active_gateways = array();
		foreach ( $gateways as $key => $value ) {
			$is_active = apply_filters( 'hubloy_membership_gateway_' . $key . '_is_active', false );
			if ( $is_active ) {
				$active_gateways[ $key ] = $value['name'];
			}
		}
		return $active_gateways;
	}

	/**
	 * Check if the selected gate3way exists
	 *
	 * @param string $gateway_id The gateway id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function gateway_exists( $gateway_id ) {
		$gateways = array_keys( self::load_gateways() );
		return in_array( $gateway_id, $gateways, true );
	}
}
