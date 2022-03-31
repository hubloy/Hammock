<?php
namespace HubloyMembership\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Duration helper
 *
 * @since 1.0.0
 */
class Duration {

	/**
	 * Period types
	 */
	const PERIOD_TYPE_DAYS   = 'day';
	const PERIOD_TYPE_WEEKS  = 'week';
	const PERIOD_TYPE_MONTHS = 'month';
	const PERIOD_TYPE_YEARS  = 'year';

	/**
	 * List days
	 * This gets an array of days
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function list_days() {
		$days = array(
			1  => __( 'One Day', 'hubloy-membership' ),
			2  => __( 'Two Days', 'hubloy-membership' ),
			3  => __( 'Three Days', 'hubloy-membership' ),
			4  => __( 'Four Days', 'hubloy-membership' ),
			5  => __( 'Five Days', 'hubloy-membership' ),
			6  => __( 'Six Days', 'hubloy-membership' ),
			7  => __( 'Seven Days', 'hubloy-membership' ),
			8  => __( 'Eight Days', 'hubloy-membership' ),
			9  => __( 'Nine Days', 'hubloy-membership' ),
			10 => __( 'Ten Days', 'hubloy-membership' ),
			11 => __( 'Eleven Days', 'hubloy-membership' ),
			12 => __( 'Twelve Days', 'hubloy-membership' ),
			13 => __( 'Thirteen Days', 'hubloy-membership' ),
			14 => __( 'Fourteen Days', 'hubloy-membership' ),
			15 => __( 'Fifteen Days', 'hubloy-membership' ),
			16 => __( 'Sixteen Days', 'hubloy-membership' ),
			17 => __( 'Seventeen Days', 'hubloy-membership' ),
			18 => __( 'Eighteen Days', 'hubloy-membership' ),
			19 => __( 'Nineteen Days', 'hubloy-membership' ),
			20 => __( 'Twenty Days', 'hubloy-membership' ),
			21 => __( 'Twenty-one Days', 'hubloy-membership' ),
			22 => __( 'Twenty-two Days', 'hubloy-membership' ),
			23 => __( 'Twenty-three Days', 'hubloy-membership' ),
			24 => __( 'Twenty-four Days', 'hubloy-membership' ),
			25 => __( 'Twenty-five Days', 'hubloy-membership' ),
			26 => __( 'Twenty-six Days', 'hubloy-membership' ),
			27 => __( 'Twenty-seven Days', 'hubloy-membership' ),
			28 => __( 'Twenty-eight Days', 'hubloy-membership' ),
		);
		return apply_filters( 'hubloy-membership_helper_duration_list_days', $days );
	}

	/**
	 * Add a period interval to a date.

	 * @param int        $period_unit The period unit to add.
	 * @param string     $period_type The period type to add.
	 * @param string|int $start_date The start date to add to.
	 *
	 * @since  1.0.0
	 *
	 * @return string The added date.
	 */
	public static function add_interval( $period_unit, $period_type, $start_date = null ) {
		if ( empty( $start_date ) ) {
			$start_date = strtotime( 'now' );
		}
		if ( ! is_numeric( $start_date ) ) {
			$start_date = strtotime( $start_date );
		}
		$result = $start_date;

		if ( is_numeric( $period_unit ) && $period_unit > 0 ) {
			$days   = self::get_period_in_days( $period_unit, $period_type );
			$result = strtotime( '+' . $days . 'days', $start_date );

			if ( false === $result ) {
				$result = $start_date;
			}
		}

		return apply_filters(
			'hubloy-membership_duration_add_interval',
			$result,
			$period_unit,
			$period_type,
			$start_date
		);
	}

	/**
	 * Subtract a period interval to a date.
	 *
	 * @since  1.0.0
	 *
	 * @param int        $period_unit The period unit to subtract.
	 * @param string     $period_type The period type to subtract.
	 * @param string|int $start_date The start date to subtract to.
	 * @return string The subtracted date.
	 */
	public static function subtract_interval( $period_unit, $period_type, $start_date = null ) {
		if ( empty( $start_date ) ) {
			$start_date = strtotime( 'now' );
		}
		if ( ! is_numeric( $start_date ) ) {
			$start_date = strtotime( $start_date );
		}
		$result = $start_date;

		if ( is_numeric( $period_unit ) && $period_unit > 0 ) {
			$days   = self::get_period_in_days( $period_unit, $period_type );
			$result = strtotime( '-' . $days . 'days', $start_date );

			if ( false === $result ) {
				$result = $start_date;
			}
		}

		$date_format = get_option( 'date_format' );

		return apply_filters(
			'hubloy-membership_subtract_interval',
			date_i18n( $date_format, $result )
		);
	}

	/**
	 * Subtract dates.
	 *
	 * Return (end_date - start_date) in period_type format
	 *
	 * @param  Date $end_date The end date to subtract from in the format yyyy-mm-dd
	 * @param  Date $start_date The start date to subtraction the format yyyy-mm-dd
	 * @param  int  $precission Time constant HOURS_IN_SECONDS will return the
	 *          difference in hours. Default is DAY_IN_SECONDS (return = days).
	 * @param  bool $real_diff If set to true then the result is negative if
	 *         enddate is before startdate. Default is false, which will return
	 *         the absolute difference which is always positive.
	 *
	 * @since  1.0.0
	 *
	 * @return int The resulting difference of the date subtraction.
	 */
	public static function subtract_dates( $end_date, $start_date, $precission = null, $real_diff = false ) {
		if ( empty( $end_date ) ) {
			// Empty end date is assumed to mean "never"
			$end_date = '2999-12-31';
		}

		// TODO: This could cause problems, since new DateTime() uses the servers
		// timezone, not the WP timezone! This will lead to subscriptions
		// expiring early in some countries...
		//
		// E.g. Server timezone is UTC
		// WP timezone is UTC -9
		// Expire date is '2016-03-01'
		//
		// Resulting expire timestamp is:
		// 2016-03-02 00:00:00 UTC
		//
		// While actual timestamp should be:
		// 2016-03-02 00:00:00 UTC-9
		// (or) 2016-03-02 09:00:00 UTC
		$end_date   = new \DateTime( $end_date );
		$start_date = new \DateTime( $start_date );

		if ( ! is_numeric( $precission ) || $precission <= 0 ) {
			$precission = DAY_IN_SECONDS;
		}

		$result = intval(
			( $end_date->format( 'U' ) - $start_date->format( 'U' ) ) /
			$precission
		);

		if ( ! $real_diff ) {
			$result = abs( $result );
		}

		return apply_filters(
			'hubloy-membership_duration_subtract_dates',
			$result
		);
	}

	/**
	 * Check if is past due date
	 *
	 * @param string $end_date - the end date
	 * @param string $start_date - the start date. Defaults to today
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_past_date( $end_date, $start_date = '' ) {
		if ( empty( $start_date ) ) {
			// Empty end date is assumed to mean "never"
			$start_date = 'NOW';
		}

		$end_date   = strtotime( $end_date );
		$start_date = strtotime( $start_date );

		$result = $end_date - $start_date;
		return $result < 0;
	}

	/**
	 * Get period in days.
	 *
	 * Convert period in week, month, years to days.
	 *
	 * @param $period The period to convert.
	 *
	 * @since  1.0.0
	 *
	 * @return int The calculated days.
	 */
	public static function get_period_in_days( $unit, $type ) {
		$days = 0;

		switch ( $type ) {
			case self::PERIOD_TYPE_DAYS:
				$days = intval( $unit );
				break;

			case self::PERIOD_TYPE_WEEKS:
				$days = intval( $unit ) * 7;
				break;

			case self::PERIOD_TYPE_MONTHS:
				$days = intval( $unit ) * 30;
				break;

			case self::PERIOD_TYPE_YEARS:
				$days = intval( $unit ) * 365;
				break;
		}

		return apply_filters(
			'hubloy-membership_duration_get_period_in_days',
			$days,
			$type
		);
	}

	/**
	 * Convert the mysql weekday to string
	 *
	 * @param int $week_day - the mysql week day
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function mysql_week_day_to_string( $week_day ) {
		switch ( $week_day ) {
			case 0:
				return 'mon';
			break;
			case 1:
				return 'tue';
			break;
			case 2:
				return 'wed';
			break;
			case 3:
				return 'thu';
			break;
			case 4:
				return 'fri';
			break;
			case 5:
				return 'sat';
			break;
			case 6:
				return 'sun';
			break;
			default:
				return '';
			break;
		}
	}
}

