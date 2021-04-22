<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Services\Addons;
use Hammock\Model\Membership;

/**
 * Activity
 * Base controller to handle all actions used in logging membership activities within the plugin
 *
 * @since 1.0.0
 */
class Activity extends Controller {

	/**
	 * Activity service 
	 * 
	 * @since 1.0.0
	 * 
	 * @var \Hammock\Services\Activity
	 */
	private $service = null;

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Activity
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Activity
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize controller
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->service = new \Hammock\Services\Activity();
		/**
		 * General action to log activities
		 * This can be called anywhere
		 */
		$this->add_action( 'hammock_log_activity', 'log_activity', 10, 5 );

		//Member
		$this->add_action( 'hammock_members_save_member', 'log_save_member', 10, 2 );

		//Plans
		$this->add_action( 'hammock_member_after_add_plan', 'log_add_plan', 10, 3 );
		$this->add_action( 'hammock_member_plan_update_plan', 'log_update_plan', 10, 3 );
		$this->add_action( 'hammock_member_after_delete_plan', 'log_delete_plan', 10, 2 );
	}

	/**
	 * General function to log activities
	 * 
	 * @param int $ref_id - the reference id
	 * @param string $action - the action
	 * @param string $object_type - the object type
	 * @param string $object_name - the object name
	 * @param int $object_id - the object id
	 */
	public function log_activity( $ref_id, $ref_type, $action, $object_type, $object_name, $object_id ) {
		$this->service->save( array(
			'ref_id'		=> $ref_id,
			'ref_type'		=> $ref_type,
			'action'        => $action,
			'object_type'   => $object_type,
			'object_name'   => $object_name,
			'object_id'     => $object_id
		) );
	}

	/**
	 * Log save Member
	 * 
	 * @param int $member_id - the member id
	 * @param int $user_id - the user id
	 * 
	 * @since 1.0.0
	 */
	public function log_save_member( $member_id, $user_id ) {
		$this->service->log_member( $member_id, __( 'New member saved', 'hammock' ), 'member', 'member', $member_id );
	}

	/**
	 * Log plan added to member
	 * 
	 * @param object $membership - the membership
	 * @param object $new_plan - the new plan
	 * @param object $member - the member
	 * 
	 * @since 1.0.0  
	 */
	public function log_add_plan( $membership, $new_plan, $member ) {
		$this->service->log_member( $member->id, __( 'New plan added', 'hammock' ), 'plan', $membership->name, $new_plan->id );
	}

	/**
	 * Log plan updated to member
	 * 
	 * @param string $old_status - the old status
	 * @param string $status - the new status
	 * @param object $plan - the plan
	 * 
	 * @since 1.0.0
	 */
	public function log_update_plan( $old_status, $status, $plan ) {
		if ( $old_status !== $status ) {
			$this->service->log_member( $plan->member_id, __( 'Plan updated', 'hammock' ), 'plan', $membership->name, $plan->id );
		}
	}

	/**
	 * Log plan deleted
	 * 
	 * @param int $member_id - the member id
	 * @param int $membership_id - the membership id
	 * 
	 * @since 1.0.0
	 */
	public function log_delete_plan( $member_id, $membership_id ) {
		$membership = new Membership( $membership_id, false );
		if ( $membership->id > 0 ) {
			$this->service->log_member( $member_id, __( 'Plan deleted', 'hammock' ), 'plan', $membership->name, 0 );
		}
	}
}
?>