<?php
namespace Hammock\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Services\Memberships;
use Hammock\Services\Members;
use Hammock\Services\Activity;
use Hammock\Services\Sublogs;
use Hammock\Helper\Duration;

/**
 * Member model
 *
 * @since 1.0.0
 */
class Member {

	/**
	 * The database ID
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * The unique member id
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $member_id = '';

	/**
	 * The user id
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * Enabled status
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $enabled = false;

	/**
	 * Date created
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $date_created = '';

	/**
	 * Date updated
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $date_updated = '';

	/**
	 * Membership meta
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $meta = array();

	/**
	 * User info
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $user_info = array();

	/**
	 * The edit url
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public $edit_url = '';

	/**
	 * Member plans
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $plans = array();

	/**
	 * User edit url
	 * This is the link to the WordPress edit url
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $user_edit_url = '';


	/**
	 * The table name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $table_name;


	/**
	 * Members service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $members_service = null;


	/**
	 * Membership service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $membership_service = null;

	/**
	 * The sub log serice
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $sub_log_service = null;

	/**
	 * Initialize the Model
	 *
	 * @since 1.0
	 */
	public function __construct( $id = null ) {
		$this->table_name         = Database::get_table_name( Database::MEMBERS );
		$this->membership_service = new Memberships();
		$this->members_service    = new Members();
		$this->sub_log_service    = new Sublogs();
		if ( is_numeric( $id ) && $id > 0 ) {
			$this->get_one( $id );
		}
	}

	/**
	 * Checks if the member exists
	 * This validates the id is greater than 0
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function exists() {
		return $this->id > 0;
	}

	/**
	 * Get one by id
	 * Checks the database and sets all values in the model
	 *
	 * @param string $id - the member id
	 *
	 * @since 1.0.0
	 */
	public function get_one( $id ) {
		global $wpdb;
		$sql  = "SELECT `date_created`, `date_updated`, `member_id`, `user_id`, `enabled` FROM {$this->table_name} WHERE `id` = %d";
		$item = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );
		if ( $item ) {
			$date_format         = get_option( 'date_format' );
			$members             = new Members();
			$this->id            = $id;
			$this->date_created  = date_i18n( $date_format, strtotime( $item->date_created ) );
			$this->date_updated  = ! empty( $item->date_updated ) ? date_i18n( $date_format, strtotime( $item->date_updated ) ) : '';
			$this->member_id     = $item->member_id;
			$this->user_id       = $item->user_id;
			$this->enabled       = ( $item->enabled == 1 );
			$this->meta          = Meta::get_all( $id, 'member' );
			$this->user_info     = Members::user_details( $this->user_id );
			$this->user_edit_url = get_edit_user_link( $this->user_id );
			$this->plans         = $members->get_member_plan_ids( $id );
			$this->edit_url      = $this->edit_url();
		}
	}

	/**
	 * Get user memberships
	 * In case a user has multiple memberships, this is used to return a list of all those memberships
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_plan_ids() {
		return $this->plans;
	}

	/**
	 * Get member plans
	 * This returns a Plan array of the member plans
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_plans() {
		$plans     = array();
		$plans_ids = $this->get_plan_ids();
		foreach ( $plans_ids as $plan_id ) {
			$plan = new Plan( $plan_id->id );
			if ( $plan->id > 0 ) {
				$plans[] = $plan;
			}
		}
		return $plans;
	}

	/**
	 * Drop a membership
	 *
	 * @param int $old_membership_id - the old membership id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function drop_plan( $old_membership_id ) {
		$old_plan = Plan::get_plan( $this->id, $old_membership_id );
		if ( $old_plan ) {
			// Delete
			do_action( 'hammock_member_before_remove_old_plan', $old_plan, $old_membership_id, $this );

			$old_plan->delete();

			do_action( 'hammock_member_after_remove_old_plan', $old_membership_id, $this );

			return true;
		}
		return false;
	}

	/**
	 * drop all member plans
	 *
	 * @since 1.0.0
	 */
	public function drop_all_plans() {
		$plans = $this->get_plan_ids();

		do_action( 'hammock_member_before_drop_all_plans', $plans, $this );

		foreach ( $plans as $plan_id ) {
			$plan = new Plan( $plan_id );
			if ( $plan->id > 0 ) {
				$plan->delete();
			}
		}

		do_action( 'hammock_member_after_drop_all_plans', $plans );
	}


	/**
	 * Delete member
	 * This removes all member plans and meta
	 *
	 * @since 1.0.0
	 */
	public function delete() {
		global $wpdb;

		do_action( 'hammock_member_before_delete_member', $this );

		$this->drop_all_plans();

		Meta::remove_all( $this->id, 'member' );

		$activity = new Activity();
		$activity->delete_activities( $this->id, 'member' );

		$sql = "DELETE FROM {$this->table_name} WHERE `id` = %d";
		$wpdb->query( $wpdb->prepare( $sql, $this->id ) );

		do_action( 'hammock_member_after_delete_member', $this->id );

		$this->id = 0;
	}

	/**
	 * Add a membership
	 * This adds a membership to the current users membership.
	 * The main membership will still be active
	 *
	 * @param int|object $new_membership_id - the new membership id or the membership
	 * @param array      $args - any extra arguments to replace in the plan. This is usefule if a plan is manually added
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function add_plan( $new_membership_id, $args = array() ) {
		if ( is_int( $new_membership_id ) ) {
			$membership = new Membership( $new_membership_id );
		} else {
			$membership = $new_membership_id;
		}

		if ( $membership->id > 0 ) {
			do_action( 'hammock_member_before_add_plan', $membership, $this );
			$old_plan = Plan::get_plan( $this->id, $membership->id );
			if ( ! $old_plan ) {
				$plan_id                 = wp_generate_password( 8, false );
				$new_plan                = new Plan();
				$new_plan->member_id     = $this->id;
				$new_plan->plan_id       = 'HM-' . $plan_id;
				$new_plan->membership_id = $membership->id;
				$new_plan->enabled       = apply_filters( 'hammock_new_plan_enabled', true, $membership );
				$new_plan->status        = apply_filters( 'hammock_new_plan_status', Members::STATUS_PENDING, $membership );
				if ( isset( $args['status'] ) ) {
					if ( $args['status'] == Members::STATUS_TRIAL ) {
						// Set the trial period
						$new_plan->set_trial( $membership );
					} else {
						if ( $args['status'] == Members::STATUS_ACTIVE ) {
							$new_plan->set_active_membership( $membership );
						}
					}
				} else {
					if ( $membership->trial_enabled ) {
						$new_plan->set_trial( $membership );
					}
				}

				if ( isset( $args['end'] ) ) {
					$new_plan->start_date = date_i18n( 'Y-m-d H:i:s' );
					$new_plan->end_date   = $args['end'];
					$new_plan->status     = Members::STATUS_ACTIVE;
				}

				if ( isset( $args['start'] ) ) {
					$new_plan->start_date = $args['start'];
					$new_plan->status     = Members::STATUS_ACTIVE;
				}

				if ( ! empty( $args ) ) {
					foreach ( $args as $key => $value ) {
						if ( property_exists( $new_plan, $key ) ) {
							$new_plan->$key = $value;
						}
					}
				}

				if ( ! $membership->trial_enabled ) {
					if ( $membership->price <= 0 && $membership->signup_price <= 0 ) {
						$new_plan->set_active_membership( $membership );
					}
				}

				$new_plan->save();
				
				$user_email = $this->get_user_info( 'email' );
				$this->sub_log_service->save_log( $this->id, $user_email, $membership->trial_enabled, $membership->id, $this->user_id );

				do_action( 'hammock_member_after_add_plan', $membership, $new_plan, $this );
				return $new_plan;
			} else {
				if ( isset( $args['status'] ) ) {
					if ( $args['status'] == Members::STATUS_TRIAL ) {
						// Set the trial period
						$old_plan->set_trial( $membership );
					} else {
						if ( $args['status'] == Members::STATUS_ACTIVE ) {
							$old_plan->set_active_membership( $membership );
						}
					}
				}

				if ( isset( $args['end'] ) ) {
					$old_plan->start_date = date_i18n( 'Y-m-d H:i:s' );
					$old_plan->end_date   = $args['end'];
					$old_plan->status     = Members::STATUS_ACTIVE;
				}

				if ( isset( $args['start'] ) ) {
					$old_plan->start_date = $args['start'];
					$old_plan->status     = Members::STATUS_ACTIVE;
				}

				if ( ! empty( $args ) ) {
					foreach ( $args as $key => $value ) {
						if ( property_exists( $old_plan, $key ) ) {
							$old_plan->$key = $value;
						}
					}
				}
				$old_plan->save();
				do_action( 'hammock_member_after_update_plan', $membership, $old_plan, $this );
				return $old_plan;
			}
		}
		return false;
	}

	/**
	 * Move membership
	 * Incase a user is moving memberships
	 *
	 * @param int $old_membership_id - the old membership id
	 * @param int $new_membership_id - the new membership id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function move_plan( $old_membership_id, $new_membership_id ) {

		do_action( 'hammock_member_before_move_plan', $old_membership_id, $new_membership_id, $this );
		$this->drop_plan( $old_membership_id );
		do_action( 'hammock_member_after_move_plan', $old_membership_id, $new_membership_id, $this );

		return $this->add_plan( $new_membership_id );
	}

	/**
	 * Member edit url
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function edit_url() {
		return admin_url( 'admin.php?page=hammock-members#/member/' . $this->id );
	}

	/**
	 * Checks if the current member has access to trial on a membership
	 * This verifies that a member has not joined a subscription before
	 * If a member is new and has not yet paid for the subscription, this returns true
	 *
	 * @param int $membership_id - The membership id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function can_trial( $membership_id ) {
		if ( hammock_user_can_subscribe( $this->user_id ) ) {
			$can_trial = $this->sub_log_service->can_trial( $this, $membership_id );
			return $can_trial;
		}
		return false;
	}

	/**
	 * Checks if the current member has subscribed before
	 * This will check the logs to see if the subscription existed before for the current member
	 *
	 * @param int $membership_id - The membership id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_subscribed_before( $membership_id ) {
		if ( hammock_user_can_subscribe( $this->user_id ) ) {
			$has_subscribed = $this->sub_log_service->has_subscribed( $this, $membership_id );
			return $has_subscribed;
		}
		return false;
	}

	/**
	 * Refresh meta
	 * Loads meta from the database for the member
	 *
	 * @since 1.0.0
	 */
	public function refresh_meta() {
		$this->meta = Meta::get_all( $this->id, 'member' );
	}

	/**
	 * Get meta value from key
	 *
	 * @param string $meta_key - the meta key
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_meta_value( $meta_key ) {
		if ( isset( $this->meta[ $meta_key ] ) ) {
			return $this->meta[ $meta_key ]['meta_value'];
		}
		return false;
	}

	/**
	 * Get member user info value
	 */
	public function get_user_info( $key = '' ) {
		if ( isset( $this->user_info[ $key ] ) ) {
			return $this->user_info[ $key ];
		}
		return '';
	}

	/**
	 * Render values to readable strings
	 *
	 * @since 1.0.0
	 */
	public function to_html() {
		return apply_filters(
			'hammock_member_to_html',
			array(
				'id'            => $this->id,
				'date_created'  => $this->date_created,
				'date_updated'  => $this->date_updated,
				'member_id'     => $this->member_id,
				'edit_url'      => $this->edit_url,
				'user_id'       => $this->user_id,
				'user_edit_url' => $this->user_edit_url,
				'user_info'     => $this->user_info,
				'enabled'       => $this->enabled,
				'status'        => $this->enabled ? __( 'Enabled', 'hammock' ) : __( 'Disabled', 'hammock' ),
				'plans'         => count( $this->plans ),
			),
			$this
		);
	}
}

