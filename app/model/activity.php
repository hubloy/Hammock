<?php
namespace HubloyMembership\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Activity model
 * Helps to inteprate the activities
 *
 * @since 1.0.0
 */
class Activity {

	/**
	 * Render activity to readable values to be used in html
	 *
	 * @param object $activity - the activity
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function to_html( $activity ) {
		$date        = self::render( 'date', $activity );
		$author      = self::render( 'author', $activity );
		$role        = self::render( 'role', $activity );
		$type        = self::render( 'type', $activity );
		$action      = self::render( 'action', $activity );
		$description = self::render( 'description', $activity );
		return apply_filters(
			'hubloy-membership_activity_to_html',
			array(
				'ref_id'      => $activity->ref_id,
				'date'        => $date,
				'author'      => $author,
				'role'        => $role,
				'type'        => $type,
				'action'      => $action,
				'description' => $description,
			),
			$activity
		);
	}

	/**
	 * Render a field in the activity
	 *
	 * @param string $name - the column name
	 * @param object $activity - the activity
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function render( $name, $activity ) {
		$return = '';
		switch ( $name ) {
			case 'date':
				$date_format = get_option( 'date_format' );
				$time_format = get_option( 'time_format' );
				$return      = date_i18n( $date_format . ' ' . $time_format . ' (P)', strtotime( $activity->date_created ) );
				break;
			case 'author':
				$return = sprintf(
					'%s <span class="author-name">%s</span>',
					__( 'by', 'hubloy-membership' ),
					__( 'N/A', 'hubloy-membership' )
				);

				if ( ! empty( $activity->user_id ) && 0 !== (int) $activity->user_id ) {
					$user = get_user_by( 'id', $activity->user_id );
					if ( $user instanceof \WP_User && 0 !== $user->ID ) {
						$return = sprintf(
							'%s <a href="%s" target="_blank"><span class="author-name">%s</span></a>',
							__( 'by', 'hubloy-membership' ),
							get_edit_user_link( $user->ID ),
							$user->display_name
						);
					}
				}
				break;
			case 'role':
				global $wp_roles;
				$return = isset( $activity->caps ) && isset( $wp_roles->role_names[ $activity->caps ] ) ? $wp_roles->role_names[ $activity->caps ] : __( 'Unknown', 'hubloy-membership' );
				break;
			case 'type':
				$return = ucfirst( $activity->object_type );
				break;
			case 'action':
				$return = $activity->action;
				break;
			case 'description':
				$return = self::render_description( $activity );
				break;

		}
		return apply_filters( 'hubloy-membership_activity_default_column', $return, $name, $activity );
	}

	/**
	 * Render description
	 *
	 * @param object $activity
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private static function render_description( $activity ) {
		$name = esc_html( $activity->object_name );
		$url  = '#';
		if ( ! empty( $activity->object_id ) ) {

			switch ( $activity->object_type ) {
				case 'member':
					$item = new Member( $activity->object_id );
					if ( $item->id > 0 ) {
						$url  = admin_url( 'admin.php?page=hubloy-membership-members#/member/' . $item->id );
						$name = $item->user_info['name'];
					}
					break;

				case 'transaction':
					$item = new Invoice( $activity->object_id );
					if ( $item->id > 0 ) {
						$url  = admin_url( 'admin.php?page=hubloy-membership-transactions#/transaction/' . $item->id );
						$name = $item->invoice_id;
					}
					break;

				case 'plan':
					$item = new Plan( $activity->object_id );
					if ( $item->id > 0 ) {
						$membership = new Membership( $item->membership_id );
						if ( $membership->id > 0 ) {
							$url  = admin_url( 'admin.php?page=hubloy-membership-memberships#/edit/' . $membership->id );
							$name = $membership->name;
						}
					}
					break;

				default:
					break;
			}
		}
		$return = sprintf( '<a href="%s" target="_blank">%s</a>', $url, esc_html( $name ) );
		return apply_filters( 'hubloy-membership_activity_render_description', $return, $activity );
	}
}

