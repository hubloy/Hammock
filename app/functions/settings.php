<?php
/**
 * General Settings
 * These functions can be used within themes or external resources
 *
 * @package HubloyMembership/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Get page id based on the page id. The id is the key that includes:
 *
 *  membership_list, protected_content, registration, thank_you_page, account_page
 *
 * @param string $page_key - the page key
 *
 * @since 1.0.0
 *
 * @return int
 */
function hubloy-membership_page_id( $page_key ) {
	$settings = \HubloyMembership\Model\Settings::instance();
	$pages    = $settings->get_general_setting( 'pages' );
	$page_id  = 0;
	if ( is_array( $pages ) ) {
		if ( isset( $pages[ $page_key ] ) ) {
			$page_id = $pages[ $page_key ];
		}
	}
	return apply_filters( 'hubloy-membership_get_page_id', $page_id, $page_id );
}

/**
 * Format the currency
 * This returns the amount and the currency depending on the position defined
 *
 * @param double $price - the price
 * @param string $cude_position - the code position
 *
 * @since 1.0.0
 *
 * @return string
 */
function hubloy-membership_format_currency( $price, $cude_position = 'left' ) {
	$code  = hubloy-membership_get_currency_symbol();
	$price = hubloy-membership_format_price( $price );
	if ( 'left' === $cude_position ) {
		$output = $code . '' . $price;
	} else {
		$output = $price . ' ' . $code;
	}
	return apply_filters( 'hubloy-membership_format_currency', $output, $code, $price );
}


/**
 * Format the price
 *
 * @param double $price - the price
 *
 * @since 1.0.0
 *
 * @return string
 */
function hubloy-membership_format_price( $price ) {
	return \HubloyMembership\Helper\Currency::format_price( $price );
}



/**
 * Get the currency symbol
 *
 * @since 1.0.0
 *
 * @return string
 */
function hubloy-membership_get_currency_symbol() {
	return \HubloyMembership\Helper\Currency::get_membership_currency();
}

