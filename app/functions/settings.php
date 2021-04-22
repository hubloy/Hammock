<?php
/**
 * General Settings
 * These functions can be used within themes or external resources
 * 
 * @package Hammock/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Get page id based on the page id. The id is the key that includes:
 * 
 * 	membership_list, protected_content, registration, thank_you_page, account_page 
 * 
 * @param string $page_key - the page key
 * 
 * @since 1.0.0
 * 
 * @return int
 */
function hammock_page_id( $page_key ) {
	$settings 	= \Hammock\Model\Settings::instance();
	$pages 		= $settings->get_general_setting( 'pages' );
	$page_id	= 0;
	if ( is_array( $pages ) ) {
		if ( isset( $pages[$page_key] ) ) {
			$page_id = $pages[$page_key];
		}
	}
	return apply_filters( 'hammock_get_page_id', $page_id, $page_id );
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
function hammock_format_currency( $price, $cude_position = 'left' ) {
	$code 	= hammock_get_currency_symbol();
	$price	= hammock_format_price( $price );
	if ( $cude_position === 'left' ) {
		$output =  $code . '' . $price;
	} else {
		$output =  $price . ' ' . $code;
	}
	return apply_filters( 'hammock_format_currency', $output, $code, $price );
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
function hammock_format_price( $price ) {
	$price	= \Hammock\Helper\Currency::format_price( $price );
	return apply_filters( 'hammock_format_price', $price );
}



/**
 * Get the currency symbol
 * 
 * @since 1.0.0
 * 
 * @return string
 */
function hammock_get_currency_symbol( ) {
	$code = \Hammock\Helper\Currency::get_membership_currency();
	return apply_filters( 'hammock_get_currency_symbol', $code );
}
?>