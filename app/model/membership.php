<?php
namespace Hammock\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Core\Database;
use Hammock\Services\Memberships;
use Hammock\Services\Members;
use Hammock\Helper\Duration;

/**
 * Membership model
 *
 * @since 1.0.0
 */
class Membership {

	/**
	 * The database ID
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * The unique membership id
	 * 
	 * @since 1.0.0
	 * 
	 * @var string
	 */
	public $membership_id = '';

	/**
	 * The membership name
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * The membership details
	 * This takes long text of the details
	 * 
	 * @since 1.0.0
	 * 
	 * @var string
	 */
	public $details = '';

	/**
	 * Enabled status
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $enabled = false;

	/**
	 * Trial enabled
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $trial_enabled = false;

	/**
	 * Limit spaces on membership
	 * This allows the admin to set the number of people that can sign up for this
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $limit_spaces = false;

	/**
	 * The membership type
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Human readable type
	 * 
	 * @since 1.0.0
	 * 
	 * @var string
	 */
	public $type_text = '';

	/**
	 * Payment duration
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $duration = '';

	/**
	 * Membership price
	 *
	 * @since 1.0.0
	 *
	 * @var float
	 */
	public $price = 0.00;

	/**
	 * Membership signup price
	 * Price charged for signup
	 * 
	 * @since 1.0.0
	 *
	 * @var float
	 */
	public $signup_price = 0.00;

	/**
	 * Trial price
	 *
	 * @since 1.0.0
	 *
	 * @var float
	 */
	public $trial_price = 0.00;

	/**
	 * Trial period based on duration
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $trial_period = 0;

	/**
	 * Trial duration
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $trial_duration = 'day';

	/**
	 * Trial duration ttext
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $trial_duration_text = 'day';

	/**
	 * Total memberships available
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $total_available = 0;

	/**
	 * Invite list
	 * This is a list of codes that have access to this membership
	 * 
	 * @since 1.0.0
	 * 
	 * @var array
	 */
	public $invite_list = array();

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
	 * Initialize the Model
	 *
	 * @since 1.0
	 */
	public function __construct( $id = null, $load_meta = true ) {
		$this->table_name      = Database::get_table_name( Database::MEMBERSHIP );
		$this->members_service = new Members();
		if ( is_numeric( $id ) && $id > 0 ) {
			$this->get_one( $id );
		}
	}

	/**
	 * Get one by id
	 * Checks the database and sets all values in the model
	 *
	 * @param string $id - the membership id
	 * @param bool $load_meta - load meta
	 *
	 * @since 1.0.0
	 */
	public function get_one( $id, $load_meta = true ) {
		global $wpdb;

		$sql  = "SELECT `membership_id`, `date_created`, `date_updated`, `name`, `details`, `enabled`, `trial_enabled`, `limit_spaces`, `type`, `duration`, `price`, `signup_price`, `trial_price`, `trial_period`, `trial_duration`, `total_available` FROM {$this->table_name} WHERE `id` = %d";
		$item = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );
		if ( $item ) {
			$date_format           		= get_option( 'date_format' );
			$this->id              		= $id;
			$this->membership_id		= $item->membership_id;
			$this->date_created    		= date_i18n( $date_format, strtotime( $item->date_created ) );
			$this->date_updated    		= ! empty( $item->date_updated ) ? date_i18n( $date_format, strtotime( $item->date_updated ) ) : '';
			$this->name            		= $item->name;
			$this->details				= $item->details;
			$this->enabled         		= ( $item->enabled == 1 );
			$this->trial_enabled   		= ( $item->trial_enabled == 1 );
			$this->limit_spaces    		= ( $item->limit_spaces == 1 );
			$this->type            		= $item->type;
			$this->type_text			= Memberships::get_type( $item->type );
			$this->duration    			= $item->duration;
			$this->price           		= $item->price;
			$this->signup_price			= $item->signup_price;
			$this->trial_price     		= $item->trial_price;
			$this->trial_period    		= $item->trial_period;
			$this->trial_duration  		= $item->trial_duration;
			$this->trial_duration_text	= Memberships::get_trial_duration( $item->trial_duration );
			$this->total_available 		= $item->total_available;
			if ( $load_meta ) {
				$this->meta            	= Meta::get_all( $id, 'membership' );
				$this->invite_list		= $this->get_invite_codes();
			}
		}
	}

	/**
	 * If membership is valid
	 * This checks if the id is greater than 0
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function is_valid() {
		return $this->id > 0;
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
		if ( isset( $this->meta[$meta_key] ) ) {
			return $this->meta[$meta_key]['meta_value'];
		}
		return false;
	}

	/**
	 * Get total members
	 * 
	 * @since 1.0.0
	 * 
	 * @return int
	 */
	public function total_members() {
		return $this->members_service->count_membership_members( $this->id );
	}

	/**
	 * Get membership price
	 * This checks if there is a trial and the amount of available memberships
	 * 
	 * @since 1.0.0
	 * 
	 * @return double
	 */
	public function get_price() {
		if ( $this->trial_enabled ) {
			return $this->trial_price;
		} else {
			if ( $this->signup_price > 0 ) {
				return $this->signup_price;
			}
			return $this->price;
		}
	}

	/**
	 * Check if its limited memberships
	 * If the total available is greater than 0, it will be limited
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function limited_memberships() {
		return $this->total_available > 0;
	}

	/**
	 * Checks the remaining memberships
	 * Returns 1000 if the memberships are not limited
	 * 
	 * @since 1.0.0
	 * 
	 * @return int
	 */
	public function remaining_memberships() {
		if ( $this->limited_memberships() ) {
			return $this->total_available - $this->total_members();
		} else {
			return 1000;
		}
	}

	/**
	 * Get invite codes
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_invite_codes() {
		$codes = $this->get_meta_value( 'invite_list' );
		if ( $codes ) {
			$service = new \Hammock\Services\Codes( 'invitation' );
			return $service->drop_down( $codes );
		}
		return array();
	}

	/**
	 * Get the readable type
	 * This returns a readable type to the user e.g. per month. one time access
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_readable_type() {
		switch ( $this->type ) {
			case Memberships::PAYMENT_TYPE_PERMANENT:
				return __( 'one time', 'hammock' );
			break;
			case Memberships::PAYMENT_TYPE_DATE_RANGE:
				$days = $this->get_meta_value( 'membership_days' );
				return sprintf( __( 'for %d days', 'hammock' ), $days );
			break;
			case Memberships::PAYMENT_TYPE_RECURRING:
				return Memberships::get_payment_duration( $this->duration );
			break;
			default:
				return apply_filters( 'hammock_membership_get_readable_type', '', $this );
			break;
		}
	}

	/**
	 * Checks if membership is recurring
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function is_recurring() {
		return ( $this->type === Memberships::PAYMENT_TYPE_RECURRING );
	}

	/**
	 * Readable trial text
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_readable_trial_text() {
		$trial_text = '';
		if ( $this->trial_enabled ) {
			$trial_text = sprintf( __( 'Free trial for %s %s', 'hammock' ), $this->trial_period, strtolower( $this->trial_duration_text ) );
		}
		return apply_filters( 'hammock_membership_get_readable_trial_text', $trial_text, $this );
	}

	/**
	 * Get trial period days
	 * This gets the total days in the trial
	 * 
	 * @since 1.0.0
	 * 
	 * @return int
	 */
	public function get_trial_period_days() {
		return Duration::get_period_in_days( $this->trial_duration, $this->trial_period );
	}


	/**
	 * Render values to readable strings
	 *
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function to_html() {
		return apply_filters(
			'hammock_membership_to_html',
			array(
				'id'              		=> $this->id,
				'membership_id'			=> $this->membership_id,
				'date_created'    		=> $this->date_created,
				'date_updated'    		=> $this->date_updated,
				'name'            		=> $this->name,
				'details'				=> $this->details,
				'enabled'         		=> $this->enabled ? __( 'Active', 'hammock' ) : __( 'Active', 'hammock' ),
				'trial_enabled'   		=> $this->trial_enabled ? __( 'Trial Enabled', 'hammock' ) : __( 'No Trial', 'hammock' ),
				'limit_spaces'    		=> $this->limit_spaces ? __( 'Limited Registration', 'hammock' ) : __( 'Open Registration', 'hammock' ),
				'type'            		=> Memberships::get_type( $this->type ),
				'duration'    			=> $this->duration,
				'signup_price'			=> hammock_format_currency( $this->signup_price ),
				'price'           		=> hammock_format_currency( $this->price ),
				'trial_price'     		=> hammock_format_currency( $this->trial_price ),
				'trial_period'    		=> $this->trial_period,
				'trial_duration'  		=> $this->trial_duration,
				'trial_duration_text' 	=> $this->trial_duration_text,
				'total_available' 		=> $this->total_available,
				'members'         		=> $this->total_members(),
				'meta'            		=> $this->meta,
			),
			$this
		);
	}
}

