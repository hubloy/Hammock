<?php
namespace Hammock\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Model\Member;
use Hammock\Model\Membership;
use Hammock\Model\Plan;
use Hammock\Model\Meta;

/**
 * Members service
 *
 * @since 1.0.0
 */
class Members {

	/**
	 * The table name
	 *
	 * @var string
	 */
	private $table_name;


	/**
	 * The plans table name
	 *
	 * @var string
	 */
	private $plans_table_name;


	/**
	 * The meta object
	 *
	 * @var object
	 */
	private $meta;

	/**
	 * Membership Relationship Status constants.
	 * Created is the first status that means the member has not selected a membership
	 *
	 * NO ACCESS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_CREATED = 'created';

	/**
	 * Membership Relationship Status constants.
	 * Pending is the first status that means the member did not confirm his
	 * intention to complete his payment/registration.
	 *
	 * NO ACCESS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_PENDING = 'pending';

	/**
	 * Membership Relationship Status constants.
	 * This status has a much higher value than PENDING, because it means that
	 * the member already made a payment, but the subscription is not yet
	 * activated because the start date was not reached.
	 *
	 * NO ACCESS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_WAITING = 'waiting';

	/**
	 * Membership Relationship Status constants.
	 *
	 * FULL ACCESS TO MEMBERSHIP CONTENTS.
	 *
	 * @since  1.0.0
	 */
	const STATUS_ACTIVE = 'active';

	/**
	 * Membership Relationship Status constants.
	 *
	 * FULL ACCESS TO MEMBERSHIP CONTENTS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_TRIAL = 'trial';

	/**
	 * Membership Relationship Status constants.
	 * User cancelled his subscription but the end date of the current payment
	 * period is not reached yet. The user has full access to the membership
	 * contents until the end date is reached.
	 *
	 * FULL ACCESS TO MEMBERSHIP CONTENTS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_CANCELED = 'canceled';

	/**
	 * Membership Relationship Status constants.
	 *
	 * NO ACCESS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_TRIAL_EXPIRED = 'trial_expired';

	/**
	 * Membership Relationship Status constants.
	 * End-Date reached. The subscription is available for renewal for a few
	 * more days.
	 *
	 * NO ACCESS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_EXPIRED = 'expired';

	/**
	 * Membership Relationship Status constants.
	 * Deactivated means, that we're completely done with this subscription.
	 * It's not displayed for renewal and the member can be set to inactive now.
	 *
	 * NO ACCESS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_DEACTIVATED = 'deactivated';

	/**
	 * Membership Relationship Status constants.
	 * Paused means that its on hold
	 *
	 * NO ACCESS.
	 *
	 * @since  1.0.0
	 * 
	 */
	const STATUS_PAUSED = 'paused';

	/**
	 * List of roles
	 * 
	 * @since 1.0.0
	 * 
	 * @var array
	 */
	private $roles = array();

	/**
	 * Transaction service
	 * 
	 * @since 1.0.0
	 * 
	 * @var object
	 */
	private $transaction_service = null;


	/**
	 * Main service constructor
	 *
	 * Sets up the service
	 */
	public function __construct() {
		$this->table_name      		= Database::get_table_name( Database::MEMBERS );
		$this->plans_table_name		= Database::get_table_name( Database::PLANS );
		$this->meta 				= new Meta();
		$this->transaction_service 	= new Transactions();
	}


	/**
	 * Return existing status types and names.
	 *
	 * @since  1.0.0
	 * 
	 * @param bool $change - set to true if used to change
	 *
	 * @return array{
	 *     Return array of ( $type => name );
	 *     @type string $type The status type.
	 *     @type string $name The status name.
	 * }
	 */
	public static function get_status_types( $change = false ) {
		if ( $change ) {
			$status_types = array(
				self::STATUS_PENDING 		=> __( 'Pending', 'hammock' ),
				self::STATUS_ACTIVE 		=> __( 'Active', 'hammock' ),
				self::STATUS_PAUSED 		=> __( 'Paused', 'hammock' ),
				self::STATUS_EXPIRED 		=> __( 'Expired', 'hammock' ),
				self::STATUS_DEACTIVATED 	=> __( 'Deactivated', 'hammock' ),
				self::STATUS_CANCELED 		=> __( 'Canceled', 'hammock' ),
			);
		} else {
			$status_types = array(
				self::STATUS_CREATED		=> __( 'Created', 'hammock' ),
				self::STATUS_PENDING 		=> __( 'Pending', 'hammock' ),
				self::STATUS_ACTIVE 		=> __( 'Active', 'hammock' ),
				self::STATUS_PAUSED 		=> __( 'Paused', 'hammock' ),
				self::STATUS_TRIAL 			=> __( 'Trial', 'hammock' ),
				self::STATUS_TRIAL_EXPIRED 	=> __( 'Trial Expired', 'hammock' ),
				self::STATUS_EXPIRED 		=> __( 'Expired', 'hammock' ),
				self::STATUS_DEACTIVATED 	=> __( 'Deactivated', 'hammock' ),
				self::STATUS_CANCELED 		=> __( 'Canceled', 'hammock' ),
				self::STATUS_WAITING 		=> __( 'Not yet active', 'hammock' ),
			);
		}

		return apply_filters(
			'hammock_member_subscription_status_type',
			$status_types
		);
	}

	/**
	 * Get Subscription status
	 *
	 * @param  string $status - the subscription status
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_status( $status ) {
		$types 	= self::get_status_types();
		$return = isset( $types[ $status ] ) ? $types[ $status ] : __( 'N/A', 'hammock' );
		return apply_filters( 'hammock_member_subscription_get_status', $return, $status );
	}


	/**
	 * Count all Members
	 * 
	 * @param array $args - search arguments
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function count_members( $args = array() ) {
		global $wpdb;
		$where = $this->generate_where( $args );
		$sql   = "SELECT count(m.`id`) FROM {$this->table_name} m LEFT JOIN {$this->plans_table_name} p ON(p.`member_id` = m.`id`) $where";
		$total = $wpdb->get_var( $sql );
		return $total;
	}

	/**
	 * Get member plan membership ids
	 * 
	 * @param int $member_id - the member id
	 * 
	 * @since 1.0.0
	 * 
	 * @return string|bool
	 */
	public function get_member_plan_membership_ids( $member_id ) {
		global $wpdb;
		$sql 		= "SELECT GROUP_CONCAT(`membership_id`) FROM {$this->plans_table_name} WHERE `member_id` = %d";
		$results    = $wpdb->get_var( $wpdb->prepare( $sql, $member_id ) );
		if ( ! empty( $results ) ) {
			return $results;
		} else {
			return false;
		}
	}

	/**
	 * List members
	 * 
	 * @param int $per_page - items per page
	 * @param int $page - current page
	 * @param array $args - search arguments
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function list_members( $per_page, $page = 0, $args = array() ) {
		global $wpdb;
		$members 	= array();
		$page   	= $per_page * $page;
		$where 		= $this->generate_where( $args );
		$sql        = "SELECT m.`id` FROM {$this->table_name} m LEFT JOIN {$this->plans_table_name} p ON(p.`member_id` = m.`id`) $where ORDER BY m.`id` DESC LIMIT %d, %d";
		$results    = $wpdb->get_results( $wpdb->prepare( $sql, $page, $per_page ) );

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$members[] = new Member( $result->id );
			}
		}

		return $members;
	}

	/**
	 * Count member plans
	 * 
	 * @param int $member_id - the member id
	 * 
	 * @since 1.0.0
	 * 
	 * @return int
	 */
	public function count_member_plans( $member_id ) {
		global $wpdb;
		$sql   = "SELECT count(`id`) FROM {$this->plans_table_name} WHERE `member_id` = %d";
		$total = $wpdb->get_var( $wpdb->prepare( $sql, $member_id ) );
		return $total;
	}

	/**
	 * List Member plans
	 * 
	 * @param int $member_id - the member id
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function list_member_plans( $member_id ) {
		global $wpdb;
		$plans		= array();
		$sql   		= "SELECT `id` FROM {$this->plans_table_name} WHERE `member_id` = %d";
		$results 	= $wpdb->get_results( $wpdb->prepare( $sql, $member_id ) );
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$plan 		= new Plan( $result->id );
				$plans[] 	= $plan->to_html();
			}
		}
		return $plans;
	}

	/**
	 * Get member plan ids
	 * 
	 * @param int $member_id - the member id
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_member_plan_ids( $member_id ) {
		global $wpdb;
		$sql   		= "SELECT `id` FROM {$this->plans_table_name} WHERE `member_id` = %d";
		$results 	= $wpdb->get_results( $wpdb->prepare( $sql, $member_id ) );
		return $results;
	}

	/**
	 * List html representation of members
	 * 
	 * @param int $per_page - items per page
	 * @param int $page - current page
	 * @param array $args - search arguments
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function list_html_members( $per_page, $page = 0, $args = array() ) {
		global $wpdb;
		$members 	= array();
		$page   	= $per_page * $page;
		$where 		= $this->generate_where( $args );
		$sql        = "SELECT m.`id` FROM {$this->table_name} m LEFT JOIN {$this->plans_table_name} p ON(p.`member_id` = m.`id`) $where ORDER BY m.`id` DESC LIMIT %d, %d";
		$results    = $wpdb->get_results( $wpdb->prepare( $sql, $page, $per_page ) );

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$member		= new Member( $result->id );
				$members[] 	= $member->to_html();
			}
		}
		return $members;
	}

	/**
	 * Generate the where query
	 * This is used to query plans
	 * 
	 * @param array $args - the arguments
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	private function generate_where( $args ) {
		$where = "";
		if ( !empty( !$args ) ) {
			if ( isset( $args['membership'] ) ) {
				$where = $wpdb->prepare( "WHERE p.`membership_id` = %d", $args['membership'] );
			}
			if ( isset( $args['status'] ) ) {
				if ( !empty( $where ) ) {
					$where .= $wpdb->prepare( " AND p.`status` = %s", $args['status'] );
				} else {
					$where = $wpdb->prepare( "WHERE p.`status` = %s", $args['status'] );
				}
			}
			if ( isset( $args['gateway'] ) ) {
				if ( !empty( $where ) ) {
					$where .= $wpdb->prepare( " AND p.`gateway` = %s", $args['gateway'] );
				} else {
					$where = $wpdb->prepare( "WHERE p.`gateway` = %s", $args['gateway'] );
				}
			}
			if ( isset( $args['start_date'] ) && isset( $args['end_date'] ) ) {
				$start_date = date( 'Y-m-d H:i:s', strtotime( $args['start_date'] ) );
				$end_date 	= date( 'Y-m-d H:i:s', strtotime( $args['end_date'] ) );
				if ( !empty( $where ) ) {
					$where .= $wpdb->prepare( " AND p.`start_date` >= %s AND p.`end_date` <= %s", $start_date, $end_date );
				} else {
					$where = $wpdb->prepare( "WHERE p.`start_date` >= %s p.`end_date` <= %s", $start_date, $end_date );
				}
			} else if ( isset( $args['start_date'] ) ) {
				$start_date = date( 'Y-m-d H:i:s', strtotime( $args['start_date'] ) );
				if ( !empty( $where ) ) {
					$where .= $wpdb->prepare( " AND p.`start_date` >= %s", $start_date );
				} else {
					$where = $wpdb->prepare( "WHERE p.`start_date` >= %s", $start_date );
				}
			} else if ( isset( $args['end_date'] ) ) {
				$end_date 	= date( 'Y-m-d H:i:s', strtotime( $args['end_date'] ) );
				if ( !empty( $where ) ) {
					$where .= $wpdb->prepare( " AND p.`end_date` <= %s", $end_date );
				} else {
					$where = $wpdb->prepare( "WHERE p.`end_date` <= %s", $end_date );
				}
			}
		}
		return apply_filters( 'hammock_members_generate_where', $where, $args );
	}
	

	/**
	 * Get member by id
	 * 
	 * @param int $id - the id
	 * 
	 * @since 1.0.0
	 * 
	 * @return object
	 */
	public function get_member_by_id( $id ) {
		return new Member( $id );
	}

	/**
	 * Get member by user id
	 * 
	 * @param int $user_id - the user id
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool|object
	 */
	public function get_member_by_user_id( $user_id ) {
		global $wpdb;
		$sql 	= "SELECT `id` FROM {$this->table_name} WHERE `user_id` = %d";
		$item 	= $wpdb->get_row( $wpdb->prepare( $sql, $user_id ) );
		if ( $item ) {
			$member = new Member( $item->id );
			if ( $member->id > 0 ) {
				return $member;
			}
		}
		return false;
	}
	

	/**
	 * Count members in a membership
	 *
	 * @param int $membership_id - the membership id
	 *
	 * @since 1.0.0
	 *
	 * @return array/int
	 */
	public function count_membership_members( $membership_id, $group = false ) {
		global $wpdb;
		if ( $group ) {
			$sql     = "SELECT COUNT(id) as total, `status` FROM {$this->plans_table_name} WHERE `membership_id` = %d GROUP BY `status`";
			$results = $wpdb->get_results( $wpdb->prepare( $sql, $membership_id ) );
			return $results;
		} else {
			$sql     = "SELECT COUNT(id) FROM {$this->plans_table_name} WHERE `membership_id` = %d";
			$results = $wpdb->get_var( $wpdb->prepare( $sql, $membership_id ) );
			return $results;
		}
	}

	/**
	 * Get user details
	 *
	 * @param int $member_id - the member id
	 * @param bool $to_object - set to true to return an object 
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function user_details( $member_id, $to_object = false ) {
		$user = get_userdata( $member_id );
		if ( $user && ! is_wp_error( $user ) ) {
			$name = $user->user_login;
			if ( !empty( $user->last_name ) && !empty( $user->first_name ) ) {
				/**
				 * Filter to allow changing name order
				 * Some countries prefer last name before the first name
				 * 
				 * @since 1.0.0
				 */
				$name = apply_filters( 'jengparess_full_user_names', $user->first_name .  " " . $user->last_name, $user );
			} else if ( !empty( $user->last_name )) {
				$name = $user->last_name;
			} else if ( !empty( $user->first_name ) ) {
				$name = $user->first_name;
			} else if ( !empty( $user->display_name ) ) {
				$name = trim( $user->display_name );
			}
			$return = array(
				'id'       	=> $user->ID,
				'email'    	=> $user->user_email,
				'username' 	=> $user->user_login,
				'name'     	=> $name,
				'picture'	=> get_avatar_url( $user->ID, array( 'size' => 256 ) )
			);
		} else {
			$return = array(
				'id'		=> false,
				'name'		=> __( 'N/A', 'hammock' ),
				'email'		=> __( 'N/A', 'hammock' ),
				'username'	=> __( 'N/A', 'hammock' ),
				'picture'	=> ''
			);
		}
		return $to_object ? (object) $return : $return;
	}

	/**
	 * Get the member user ids
	 * This returns the WordPress user ids of the members
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_member_user_ids() {
		global $wpdb;
		$members 	= array();
		$sql        = "SELECT GROUP_CONCAT( `user_id` ) FROM {$this->table_name}";
		$results    = $wpdb->get_var( $sql );
		if ( $results ) {
			$ids = explode( ',', $results );
			if ( is_array( $ids ) && !empty( $ids ) ) {
				return array_map( 'intval', $ids );
			}
		}
		return array();
	}

	/**
	 * Get all roles in the site
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_roles() {
		if ( empty( $this->roles ) ) {
			global $wp_roles;
			$exclude = apply_filters(
				'hammock_roles_list_exclude',
				array( 'administrator' )
			);

			$all_roles = $wp_roles->roles;
			$all_roles = apply_filters( 'editable_roles', $all_roles );
			foreach ( $all_roles as $key => $role ) {
				if ( in_array( $key, $exclude ) ) { continue; }
				$this->roles[$key] = $role['name'];
			}
		}
		return $this->roles;
	}

	/**
	 * List non members
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function search_members( $search = '', $include = false ) {
		$members 	= array();
		$args 		= array(
			'role'		=> 'Subscriber',
		);

		if ( ! empty( $search ) ) {
			$args['search'] = '*' . $search . '*';
		}

		$member_ids = $this->get_member_user_ids();
		if ( !empty( $member_ids ) ) {
			if ( $include ) {
				$args['include'] = $member_ids;
			} else {
				$args['exclude'] = $member_ids;
			}
			
		}

		$args 	= apply_filters( 'hammock_list_non_members', $args, $search );
		$query 	= new \WP_User_Query( $args );
		$users 	= $query->get_results();
		foreach ( $users as $user ) {
			$name = $user->user_login;
			if ( !empty( $user->last_name ) && !empty( $user->first_name ) ) {
				/**
				 * Filter to allow changing name order
				 * Some countries prefer last name before the first name
				 * 
				 * @since 1.0.0
				 */
				$name = apply_filters( 'jengparess_full_user_names', $user->first_name .  " " . $user->last_name, $user );
			} else if ( !empty( $user->last_name )) {
				$name = $user->last_name;
			} else if ( !empty( $user->first_name ) ) {
				$name = $user->first_name;
			} else if ( !empty( $user->display_name ) ) {
				$name = trim( $user->display_name );
			}
			$members[$user->ID] = array(
				'id' 	=> $user->ID,
				'name' 	=> $name,
			);
		}
		return $members;
	}

	/**
	 * Save new user
	 * 
	 * @param string $email - the email
	 * @param string $firstname - users firstname
	 * @param string $lastname - users lastname
	 * @param string $$username - the username
	 * @param string $password - password
	 * @param bool $save_member - set to true to save member
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function save_new_user( $email, $firstname = '', $lastname = '', $username = '', $password = false, $save_member = true ) {
		if ( !$password ) {
			$password = wp_generate_password();
		}
		if ( ! is_email( $email ) ) {
			return array(
				'status' 	=> false,
				'message'	=> __( 'The email address is not valid.', 'hammock' )
			);
		} else {
			if ( email_exists( $email ) ) {
				return array(
					'status' 	=> false,
					'message'	=> __( 'An account with the email address exists.', 'hammock' )
				);
			} else {
				if ( !empty( $username ) ) {
					if ( username_exists( $username ) ) {
						return array(
							'status' 	=> false,
							'message'	=> sprintf( __( 'User with username %s exists', 'hammock' ), $username )
						);
					}
				} else {
					$email_name = explode( '@', $email );
					if ( ! username_exists( $email_name[0] ) ) {
						$username = $email_name[0];
					} else {
						$username = uniqid( $email_name[0], false );
					}
					$username 	= wp_slash( $username );
				}
				$email 	= wp_slash( $email );

				$user_data = array(
					'user_login' => $username,
					'user_pass'  => $password,
					'user_email' => $email,
					'first_name' => ! empty( $firstname ) ? $firstname : '',
					'last_name'  => ! empty( $lastname )  ? $lastname  : '',
					'role'       => 'subscriber',
				);

				$user_id = wp_insert_user( $user_data );
				if ( !is_wp_error( $user_id ) ) {
					update_user_option( $user_id, 'default_password_nag', true, true );
					if ( $save_member ) {
						return $this->save_member( $user_id );
					} else {
						return array(
							'status' 	=> true,
							'message'	=> __( 'User saved', 'hammock' ),
							'user_id'	=> $user_id
						);
					}
				} else {
					return array(
						'status' 	=> false,
						'message'	=> sprintf( __( 'Error saving user %s', 'hammock' ), $user_id->get_error_message() )
					);
				}
			}
		}
	}

	/**
	 * Save member with no membership
	 * 
	 * @param int $user_id - the user id
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function save_member( $user_id ) {
		global $wpdb;
		$member_id 	= wp_generate_password( 8, false );
		$result 	= $wpdb->insert(
			$this->table_name,
			array(
				'member_id'		=> strtoupper( $member_id ),
				'user_id'		=> $user_id,
				'enabled'		=> true,
				'date_created'	=> date_i18n( 'Y-m-d H:i:s' ),
			)
		);

		if ( ! $result ) {
			return array(
				'status' 	=> false,
				'message'	=> __( 'Error saving member', 'hammock' )
			);
		} else {
			$id = (int) $wpdb->insert_id;

			/**
			 * Action called when member is saved
			 * 
			 * @param int $id - the member id
			 * @param int $user_id - the user id
			 */
			do_action( 'hammock_members_save_member', $id, $user_id );

			return array(
				'status' 	=> true,
				'message'	=> __( 'Member saved', 'hammock' ),
				'id'		=> $id,
				'user_id'	=> $user_id
			);
		}
	}

	/**
	 * Admin set member plan
	 * This assigns a member to a plan with no gateway
	 * 
	 * @param int $membership_id - the membership id
	 * @param int $member_id - the member id
	 * @param string $access - the access type
	 * @param string $start_date - optional start date depending on access
	 * @param string $end_date - optional end date depending on the access
	 * @param bool $enable_trial - enable trial and resume billing after
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function admin_set_plan( $membership_id, $member_id, $access, $start_date, $end_date, $enable_trial ) {
		$member = new Member( $member_id );
		if ( $member->id > 0 ) {
			$args = array();
			if ( $enable_trial ) {
				$args['status'] = self::STATUS_TRIAL;
			} else {
				if ( $access == 'date' ) {
					$args['end'] 	= $end_date;
					$args['start'] 	= $start_date;
				} else if ( $access == 'permanent' ) {
					$args['status'] = self::STATUS_ACTIVE;
					$args['start'] 	= date_i18n( 'Y-m-d H:i:s' );
				} else if ( $access == 'invoice' ) {
					$args['status'] = self::STATUS_PENDING;
				}
			}
			$plan = $member->add_plan( $membership_id, $args );
			if ( $plan ) {
				if ( $access == 'invoice' ) {
					$membership = new Membership( $membership_id );

					$due_date = date_i18n( 'Y-m-d H:i:s', strtotime( 'now' ) );

					if ( $plan->has_trial() ) {
						//After trial
						$due_date = $plan->end_date;
					}

					/**
					 * Filter to modify the due date on auto-generate plans
					 * 
					 * @param string $due_date - the due date. Defaults to now
					 * @param object $member - the member object
					 * @param object $membership - the membership object
					 * @param object $plan - the plan
					 * 
					 * @since 1.0.0
					 * 
					 * @return $due_date
					 */
					$due_date = apply_filters( 'hammock_admin_new_plan_invoice_due_date', $due_date, $member, $membership, $plan );

					/**
					 * Save transaction
					 * This will save a transaction and send an email to the user
					 */
					$this->transaction_service->save_transaction( '', Transactions::STATUS_PENDING, $member, $plan, $membership->get_price(), $due_date );
				}
				return array(
					'status' 	=> true,
					'message'	=> __( 'Plan saved', 'hammock' ),
					'plan_id'	=> $plan->id
				);
			} else {
				return array(
					'status'	=> false,
					'message'	=> __( 'Error adding plan to member', 'hammock' )
				);
			}
		} else {
			return array(
				'status'	=> false,
				'message'	=> __( 'Member does not exist', 'hammock' )
			);
		}
	}

	/**
	 * Update plan
	 * 
	 * @param int $plan_id - the plan id
	 * @param string $status - the status
	 * @param string $start_date - optional start date depending on access
	 * @param string $end_date - optional end date depending on the access
	 * @param bool $enabled - enable or disable plan
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function admin_update_plan( $plan_id, $status, $start_date, $end_date, $enabled ) {
		$plan = new Plan( $plan_id );
		if ( $plan->id > 0 ) {
			$old_status 	= $plan->status;
			$plan->status 	= $status;
			if ( $status == self::STATUS_ACTIVE ) {
				$plan->start_date 	= $start_date;
				$plan->end_date 	= $end_date;
			}
			$plan->enabled = $enabled;
			$plan->save();

			/**
			 * Plan updated
			 * 
			 * @param string $old_status - the old status
			 * @param string $status - the new status
			 * @param object $plan - the plan
			 */
			do_action( 'hammock_member_plan_update_plan', $old_status, $status, $plan );
			return array(
				'status'	=> true,
				'message'	=> __( 'Plan updated', 'hammock' )
			);
		} else {
			return array(
				'status'	=> false,
				'message'	=> __( 'Selected plan does not exist', 'hammock' )
			);
		}
	}

	/**
	 * Admin remove plan
	 * 
	 * @param int $plan_id - the plan id
	 * @param bool $delete_user - set to true to also delete user
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function admin_remove_plan( $plan_id, $delete_user ) {
		$plan = new Plan( $plan_id );
		if ( $plan->id > 0 ) {
			$member_id 	= $plan->member_id;
			$member 	= new Member( $member_id );
			$plan->delete();
			if ( $delete_user && $member->id > 0 ) {
				$member->delete();
				return array(
					'status'	=> true,
					'message'	=> __( 'Member and plan removed', 'hammock' )
				);
			}
			return array(
				'status'	=> true,
				'message'	=> __( 'Plan removed', 'hammock' )
			);
		} else {
			return array(
				'status'	=> false,
				'message'	=> __( 'Selected plan does not exist', 'hammock' )
			);
		}
	}

	/**
	 * Remove member
	 * This deletes a member and all plans
	 * 
	 * @param int $member_id - the member id
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function remove_member( $member_id ) {
		$member = new Member( $member_id );
		if ( $member->id > 0 ) {
			$member->delete();
			return array(
				'status'	=> true,
				'message'	=> __( 'Member deleted', 'hammock' )
			);
		} else {
			return array(
				'status'	=> false,
				'message'	=> __( 'Member does not exist', 'hammock' )
			);
		}
	}

	/**
	 * Save Meta
	 *
	 * @param int    $member_id - the member id
	 * @param string $key - the meta key
	 * @param string $value - the meta value
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function save_meta( $member_id, $key, $value ) {
		return $this->meta->save( $member_id, 'member', $key, $value );
	}

	/**
	 * Save or update meta
	 *
	 * @param int    $member_id - the member id
	 * @param string $key - the meta key
	 * @param string $value - the meta value
	 *
	 * @since 1.0.0
	 */
	public function update_meta( $member_id, $key, $value ) {
		$this->meta->update( $member_id, 'member', $key, $value );
	}


	/**
	 * Delete meta
	 *
	 * @param int    $member_id - the member id
	 * @param string $key - the meta key
	 * @param string $value - the meta value
	 *
	 * @since 1.0.0
	 */
	public function delete_meta( $member_id, $key ) {
		$this->meta->delete( $member_id, 'member', $key );
	}

	/**
	 * Get member stats to be used in the dashboard
	 * This returns an array of the number of new members per day of the week
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_weekly_member_stats() {
		global $wpdb;
		$members	= array();
		$sql 		= "SELECT count(`id`) as total, WEEKDAY(`start_date`) as week_day FROM {$this->plans_table_name} WHERE `start_date` BETWEEN (FROM_DAYS(TO_DAYS(CURDATE())-MOD(TO_DAYS(CURDATE())-1,7))) AND (FROM_DAYS(TO_DAYS(CURDATE())-MOD(TO_DAYS(CURDATE())-1,7)) + INTERVAL 7 DAY)";
		$results    = $wpdb->get_results( $sql );
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$members[$result->week_day] = $result->total;
			}
		}
		return $members;
	}
}

