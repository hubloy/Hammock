<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Dashboard strings
 */
return array(
	'members'	=> array(
		'title'	=> __( 'Members', 'hammock' ),
		'none'	=> __( 'No Members Found', 'hammock' ),
		'url'	=> admin_url( 'admin.php?page=hammock-members' )
	),
	'memberships'	=> array(
		'title'	=> __( 'Memberships', 'hammock' ),
		'none'	=> __( 'No Memberships Found', 'hammock' ),
		'url'	=> admin_url( 'admin.php?page=hammock-memberships' )
	),
	'management'	=> array(
		'title'	=> __( 'Site Management', 'hammock' ),
		'types'	=> apply_filters( 'hammock_dashboard_links',  array(
			'memberships' => array(
				'name'	=> __( 'Add Membership', 'hammock' ),
				'url'	=> admin_url( 'admin.php?page=hammock-memberships#hammock-add-membership' )
			),
			'addons' => array(
				'name'	=> __( 'View Addons', 'hammock' ),
				'url'	=> admin_url( 'admin.php?page=hammock-addons' )
			)
		) )
	),
	'stats'	=> array(
		'title'	=> __( 'Stats Overview', 'hammock' )
	)
);