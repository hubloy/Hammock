<?php
/**
 * Account join subscription plan page
 * This view is used to show an existing plan to a user
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/plan/view-plan.php.
 * 
 * @package Hammock/Templates/Account/Plan/Single/View
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//Handle payments etc
$gateways = hammock_list_active_gateways();

if ( $subscription->is_active() ) {
	echo 'Active';
} else {
	//Check pending invoices
	echo 'Needs paying';
}
?>