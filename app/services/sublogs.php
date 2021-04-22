<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;

/**
 * Subscription  Logs Class
 * This manages the subscription Logs
 * Logs when a user goes no trial or when a user joins a membership
 * This is also used to prevent a user from joining trial more than once
 * 
 * @since 1.0.0
 */
class Sublogs {

	/**
	 * The table name
	 *
	 * @var string
	 */
	private $table_name;


	/**
	 * Main service constructor
	 *
	 * Sets up the service
	 */
	public function __construct() {
		$this->table_name = Database::get_table_name( Database::LOGS );
	}

	/**
	 * Save Log
	 * 
	 * @param int $member_id - the member id
	 * @param string $user_email - the user email
	 * @param int $trial - if current action is for trial. Only accepts 1 or 0
	 * @param int $membership_id - the membership id
	 * @param int $user_id - the user id
	 * 
	 * @since 1.0.0
	 * 
	 * @return int|bool
	 */
	public function save_log( $member_id, $user_email, $trial, $membership_id, $user_id ) {
		global $wpdb;

		$result = $wpdb->insert(
			$this->table_name,
			array(
				'member_id'		=> $member_id,
				'user_email'	=> $user_email,
				'trial'			=> $trial,
				'user_id'		=> $user_id,
				'membership_id'	=> $membership_id,
				'date_created'	=> date_i18n( 'Y-m-d H:i:s' ),
			)
		);

		if ( ! $result ) {
			return false;
		}
		return $id = (int) $wpdb->insert_id;
	}

	/**
	 * Check if the current member is eligible for a trial on a membership
	 * 
	 * @param \Hammock\Model\Member $member - the member object
	 * @param int $membership_id - the membership id
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function can_trial( $member, $membership_id ) {
		global $wpdb;
		$user_info 	= $member->user_info;
		$sql 		= "SELECT `id` FROM {$this->table_name} WHERE ( `member_id` = %d OR `user_email` = %s OR `user_id` = %d ) AND `membership_id` = %d AND `trial` = 1";
		$result    	= $wpdb->get_var( $wpdb->prepare( $sql, $member->id, $user_info['email'], $user_info['id'], $membership_id ) );
		return $result;
	}

	/**
	 * Check if the current member has already subscribed to the current membership
	 * This is used to avoid repeating members special prices or coupons
	 * This will not check if a member has had a trial,trial will not be 
	 * counted as a subscription
	 * 
	 * @param \Hammock\Model\Member $member - the member object
	 * @param int $membership_id - the membership id
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function has_subscribed( $member, $membership_id ) {
		global $wpdb;
		$user_info 	= $member->user_info;
		$sql 		= "SELECT `id` FROM {$this->table_name} WHERE ( `member_id` = %d OR `user_email` = %s OR `user_id` = %d ) AND `membership_id` = %d AND `trial` = 0";
		$result    	= $wpdb->get_var( $wpdb->prepare( $sql, $member->id, $user_info['email'], $user_info['id'], $membership_id ) );
		return $result;
	}
}
?>