<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Rules strings
 */
return array(
	'dashboard' => array(
		'add_new'        => array(
			'button' => __( 'Create Rule', 'hammock' ),
		),
		'table'          => array(
			'id'      => __( 'ID', 'hammock' ),
			'desc'    => __( 'Description', 'hammock' ),
			'status'  => __( 'Status', 'hammock' ),
			'type'    => __( 'Type', 'hammock' ),
			'date'    => __( 'Date Created', 'hammock' ),
		),
	),
);
