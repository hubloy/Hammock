<?php
namespace HubloyMembership\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pagination helper
 * Handles renders for pagination
 *
 * @since 1.0.0
 */
class Pagination {

	/**
	 * Generate array of pages
	 *
	 * @param int $total - the total items
	 * @param int $per_page - the total items per page
	 * @param int $current - the current page
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	static function generate_pages( $total, $per_page, $current = 1 ) {
		$pages = array();
		if ( $total > $per_page ) {
			$total_pages = ceil( $total / $per_page );
			$dots        = false;
			$mid_size    = 2;
			$end_size    = 1;
			for ( $i = 1; $i <= $total_pages; $i ++ ) {
				if ( ( $i <= $end_size || ( $current && $i >= $current - $mid_size && $i <= $current + $mid_size ) || $i > $total_pages - $end_size ) ) {
					$pages[] = $i;
					$dots    = true;
				} elseif ( $dots ) {
					$pages[] = '....';
					$dots    = false;
				}
			}
		}
		return $pages;
	}
}

