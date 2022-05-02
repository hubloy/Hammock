<?php
namespace HubloyMembership\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Database class
 * This handles table created and table name handling
 *
 * @since 1.0.0
 */
class Database {

	/**
	 * Table key constants
	 * These represent the table names
	 */
	const MEMBERSHIP       = 'membership';
	const MEMBERSHIP_RULES = 'membership_rules';
	const META             = 'meta';
	const MEMBERS          = 'members';
	const PLANS            = 'plans';
	const INVOICE          = 'invoice';
	const CODES            = 'codes';
	const ACTIVITY         = 'activity';
	const LOGS             = 'log';
	const USAGE            = 'usage';


	/**
	 * Current tables
	 */
	private static $tables = array();

	/**
	 * Get all the used table names
	 *
	 * @since 1.0
	 * @return array
	 */
	private static function table_names( $db = false ) {
		if ( ! $db ) {
			global $wpdb;
			$db = $wpdb;
		}

		return array(
			self::MEMBERSHIP       => $db->prefix . 'hubloy_m_membership',
			self::MEMBERSHIP_RULES => $db->prefix . 'hubloy_m_membership_rules',
			self::META             => $db->prefix . 'hubloy_m_meta',
			self::MEMBERS          => $db->prefix . 'hubloy_m_members',
			self::PLANS            => $db->prefix . 'hubloy_m_plans',
			self::INVOICE          => $db->prefix . 'hubloy_m_invoice',
			self::CODES            => $db->prefix . 'hubloy_m_codes',
			self::ACTIVITY         => $db->prefix . 'hubloy_m_activity',
			self::LOGS             => $db->prefix . 'hubloy_m_subscription_log',
			self::USAGE            => $db->prefix . 'hubloy_m_usage',
		);
	}


	/**
	 * Get Table Name
	 *
	 * @since 1.0
	 * @param string $name - the name of the table
	 *
	 * @return string/bool
	 */
	public static function get_table_name( $name ) {
		if ( empty( self::$tables ) ) {
			self::$tables = self::table_names();
		}
		return isset( self::$tables[ $name ] ) ? self::$tables[ $name ] : false;
	}

	/**
	 * Create tables
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;

		$wpdb->hide_errors();

		$max_index_length = 191;
		$charset_collate  = $wpdb->get_charset_collate();

		// Membership table
		$table_name = self::get_table_name( self::MEMBERSHIP );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`membership_id` VARCHAR($max_index_length) NULL,
				`name` varchar($max_index_length) NOT NULL,
				`details` LONGTEXT NULL,
				`enabled` tinyint(1) NOT NULL DEFAULT '0',
                `trial_enabled` tinyint(1) NOT NULL DEFAULT '0',
				`limit_spaces` tinyint(1) NOT NULL DEFAULT '0',
				`type` varchar($max_index_length) NOT NULL,
				`duration` varchar($max_index_length) default NULL,
				`price` double(10,2) NOT NULL DEFAULT '0.00',
				`signup_price` double(10,2) NOT NULL DEFAULT '0.00',
                `trial_price` double(10,2) NOT NULL DEFAULT '0.00',
                `trial_period` int(11) NOT NULL DEFAULT '0',
                `trial_duration` varchar($max_index_length) default NULL,
                `total_available` int(11) NOT NULL DEFAULT '0',
                `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`date_updated` datetime DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `hubloy_m_membership_id` (`membership_id`),
				KEY `hubloy_m_membership_name` (`name`),
				KEY `hubloy_m_membership_enabled` (`enabled`),
                KEY `hubloy_m_membership_trial_enabled` (`trial_enabled`),
				KEY `hubloy_m_membership_limit_spaces` (`limit_spaces`),
				KEY `hubloy_m_membership_type` (`type`),
                KEY `hubloy_m_membership_duration` (`duration`),
                KEY `hubloy_m_membership_price` (`price`),
                KEY `hubloy_m_membership_trial_price` (`trial_price`),
                KEY `hubloy_m_membership_total_available` (`total_available`),
                KEY `hubloy_m_membership_date_created` (`date_created`)
			) $charset_collate;";
			dbDelta( $sql );
		}

		// Membership rules.
		// Time duration is in milliseconds
		$table_name = self::get_table_name( self::MEMBERSHIP_RULES );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`rule_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`memberships` LONGTEXT NULL,
                `object_type` VARCHAR($max_index_length) NOT NULL,
				`object_id` bigint(20) unsigned default NULL,
				`custom_rule` LONGTEXT NULL,
				`status` VARCHAR(10) NULL,
				`time_limit` tinyint(1) NOT NULL DEFAULT '0',
				`time_duration` bigint(20) NOT NULL DEFAULT '0',
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				`date_updated` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`rule_id`),
				KEY `rule_object_type` (`object_type`($max_index_length)),
				KEY `rule_type_id` (`object_id` ASC, `object_type` ASC),
                KEY `rule_type_membership` (`object_id` ASC, `object_type` ASC))
				$charset_collate;";
			dbDelta( $sql );
		}

		// General meta table used for core tables
		$table_name = self::get_table_name( self::META );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`object_id` bigint(20) unsigned NOT NULL,
                `meta_type` VARCHAR($max_index_length) default NULL,
				`meta_key` VARCHAR($max_index_length) default NULL,
				`meta_value` LONGTEXT NULL,
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				`date_updated` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`meta_id`),
				KEY `meta_key` (`meta_key`($max_index_length)),
				KEY `meta_object_id` (`object_id` ASC ),
                KEY `meta_key_type` (`object_id` ASC, `meta_type` ASC, `meta_key` ASC),
				KEY `meta_key_object` (`object_id` ASC, `meta_key` ASC))
				$charset_collate;";
			dbDelta( $sql );
		}

		// Members
		// User can have more than one membership
		$table_name = self::get_table_name( self::MEMBERS );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`member_id` VARCHAR($max_index_length) NULL,
				`user_id` bigint(20) unsigned NOT NULL,
				`enabled` tinyint(1) NOT NULL DEFAULT '0',
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				`date_updated` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`id`),
				KEY `member_member_id` (`member_id` ASC),
				KEY `member_enabled` (`enabled` ASC),
				KEY `member_user_id` (`user_id` ASC))
				$charset_collate;";
			dbDelta( $sql );
		}

		$table_name = self::get_table_name( self::PLANS );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`plan_id` VARCHAR($max_index_length) NULL,
				`member_id` bigint(20) unsigned NOT NULL,
				`membership_id` bigint(20) unsigned NOT NULL DEFAULT '0',
				`enabled` tinyint(1) NOT NULL DEFAULT '0',
				`status` varchar($max_index_length) NOT NULL,
				`gateway` varchar($max_index_length) DEFAULT NULL,
				`gateway_subscription_id` varchar($max_index_length) DEFAULT NULL,
				`start_date` datetime DEFAULT NULL,
				`end_date` datetime DEFAULT NULL,
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				`date_updated` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`id`),
				UNIQUE KEY `unique_plan_id` (`plan_id` ASC),
				KEY `plan_member_id` (`member_id` ASC),
				KEY `plan_membership` (`membership_id` ASC ),
				KEY `plan_gateway_subscription_id` (`gateway_subscription_id` ASC ),
                KEY `plan_member_membership` (`member_id` ASC, `membership_id` ASC ),
				KEY `plan_member_membership_plan` (`member_id` ASC, `membership_id` ASC, `plan_id` ASC ))
				$charset_collate;";
			dbDelta( $sql );
		}

		// Invoice
		$table_name = self::get_table_name( self::INVOICE );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`gateway` VARCHAR($max_index_length) NULL,
				`method` VARCHAR(100) NULL,
				`status` VARCHAR(10) NULL,
				`member_id` bigint(20) unsigned NULL,
				`plan_id` bigint(20) unsigned NULL,
				`invoice_id` VARCHAR($max_index_length) NULL,
				`amount` DOUBLE(10,2) NULL,
				`tax_rate` DOUBLE(10,2) NULL,
				`gateway_identifier` VARCHAR($max_index_length) NULL,
				`notes` LONGTEXT NULL,
				`custom_data` LONGTEXT NULL,
				`user_id` bigint(20) unsigned NULL,
				`due_date` datetime DEFAULT NULL,
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				`last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`id`),
				KEY `invoice_gateway` (`gateway`($max_index_length)),
				KEY `invoice_status` (`status`),
				KEY `invoice_sub` (`member_id` ),
				KEY `invoice_plan_id` (`plan_id` ),
				KEY `invoice_invoice` (`invoice_id`),
				KEY `invoice_gateway_identifier` (`gateway_identifier`),
				KEY `invoice_due_date` (`due_date`))
				$charset_collate;";
			dbDelta( $sql );
		}

		// Codes
		// Coupon and invites
		$table_name = self::get_table_name( self::CODES );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`code_type` VARCHAR($max_index_length) NULL,
				`code` VARCHAR($max_index_length) NULL,
				`status` enum('enabled','disabled','expired','canceled') DEFAULT 'disabled',
				`amount` DOUBLE(10,2) NULL,
				`amount_type` enum('percentage','number') DEFAULT 'number',
				`custom_data` LONGTEXT NULL,
				`author_id` bigint(20) unsigned NULL,
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				`last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`id`),
				KEY `code_code` (`code`($max_index_length)),
				KEY `code_code_type` (`code_type`($max_index_length)),
				KEY `code_code_type_code` (`code` ASC, `code_type` ASC ),
				KEY `code_status` (`status`),
				KEY `code_amount` (`amount` ),
				KEY `code_code_type_amount` (`code` ASC, `code_type` ASC, `amount` ASC ))
				$charset_collate;";
			dbDelta( $sql );
		}

		// Activity logs
		$table_name = self::get_table_name( self::ACTIVITY );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`ref_id` int(11) NOT NULL DEFAULT '0',
				`ref_type` varchar($max_index_length) NOT NULL,
				`caps` varchar(70) NOT NULL DEFAULT 'guest',
				`action` varchar($max_index_length) NOT NULL,
				`object_type` varchar($max_index_length) NOT NULL,
				`object_name` varchar(255) NOT NULL,
				`object_id` int(11) NOT NULL DEFAULT '0',
				`user_id` int(11) NOT NULL DEFAULT '0',
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`log_id`),
				KEY `action` (`action`($max_index_length)),
				KEY `ref_type` (`ref_type`($max_index_length)),
				KEY `ref_id` (`ref_id`),
				KEY `ref_id_type` (`ref_id` ASC, `ref_type` ASC),
				KEY `log_date_created` (`date_created`),
				KEY `object_id` (`object_id`),
				KEY `log_ref_id_object_id_type` (`ref_id` ASC, `object_id` ASC, `object_type` ASC),
				KEY `log_ref_id_object_id_ref_type` (`ref_id` ASC, `object_id` ASC, `ref_type` ASC, `object_type` ASC),
				KEY `log_object_id_type` (`object_id` ASC, `object_type` ASC),
				KEY `user_id` (`user_id`),
				KEY `logger_date` (`object_id` ASC, `date_created` ASC))
				$charset_collate;";
			dbDelta( $sql );
		}

		// Keeps logs of subscriptions. This is used to verify and prevent members from joining the wrong memberships
		$table_name = self::get_table_name( self::LOGS );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`member_id` int(11) NOT NULL DEFAULT '0',
				`user_email` varchar(255) NOT NULL,
				`trial` tinyint(1) NOT NULL DEFAULT '0',
				`membership_id` int(11) NOT NULL DEFAULT '0',
				`user_id` int(11) NOT NULL DEFAULT '0',
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`id`),
				KEY `member_log` (`member_id` ASC, `membership_id` ASC, `user_id` ASC),
				KEY `log_member_id` (`member_id`),
				KEY `log_membership_id` (`membership_id`))
				$charset_collate;";
			dbDelta( $sql );
		}

		// Usage tracking for specific models that have restrictions.
		$table_name = self::get_table_name( self::USAGE );
		if ( $table_name ) {
			$sql = "CREATE TABLE {$table_name} (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`object_id` int(11) NOT NULL DEFAULT '0',
				`object_type` varchar($max_index_length) NOT NULL,
				`item_ref` varchar($max_index_length) NOT NULL,
				`total_usage` bigint(20) unsigned NULL,
				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY (`id`),
				KEY `object_type` (`object_type`($max_index_length)),
				KEY `item_ref` (`item_ref`($max_index_length)),
				KEY `ref_date_created` (`date_created`),
				KEY `object_id` (`object_id`),
				KEY `item_ref_object_id_type` (`item_ref` ASC, `object_id` ASC, `object_type` ASC),
				KEY `item_object_id_type` (`object_id` ASC, `object_type` ASC))
				$charset_collate;";
			dbDelta( $sql );
		}
	}
}

