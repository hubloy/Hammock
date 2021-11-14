<?php
/**
 * Account functions
 * These functions can be used within themes or external resources
 *
 * @package Hammock/Functions
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Content protected message
 *
 * @param int    $content_id - the content id
 * @param string $type - content type (post, term)
 * @param string $object_type - content type object type
 *
 * @since 1.0.0
 */
function hammock_content_protected_message( $content_id, $type, $object_type ) {
	return apply_filters( 'hammock_content_protected_message', __( 'Access to this content is restricted', 'hammock' ), $content_id, $type, $object_type );
}

/**
 * Check if post is protected
 *
 * @param int $post_id - optional post id
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hammock_is_post_protected( $post_id = null ) {
	global $post;

	$post_type = null;

	$protected = false;

	if ( is_int( $post_id ) ) {
		$post = get_post( $post_id );
	}

	if ( ! $post_id && $post && isset( $post->ID ) ) {
		$post_id   = $post->ID;
		$post_type = $post->post_type;
	} elseif ( $post_id instanceof \WP_Post ) {
		$post      = $post_id;
		$post_type = $post_id->post_type;
		$post_id   = $post_id->ID;
	}

	if ( $post_id ) {
		/**
		 * @see \Hammock\Rule\Post::has_access
		 */
		$has_access = apply_filters( 'hammock_post_content_has_access', true, $post, $post_type );
		$protected  = ! $has_access;

		if ( ! $protected ) {
			$protected = hammock_is_post_term_protected( $post_id );
		}
	}
	return $protected;
}

/**
 * Check if the post term is protected
 *
 * @param int $post_id - optional post id
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hammock_is_post_term_protected( $post_id = null ) {
	$settings      = \Hammock\Model\Settings::instance();
	$addon_setting = $settings->get_addon_setting( 'category' );
	$protected     = isset( $addon_setting['protected'] ) ? $addon_setting['protected'] : array();
	$post_terms    = array();
	foreach ( $protected as $slug ) {
		$terms      = hammock_get_post_terms( $post_id, $slug );
		$post_terms = array_merge( $post_terms, $terms );
	}

	foreach ( $post_terms as $term ) {
		if ( hammock_is_term_protected( $term ) ) {
			return true;
		}
	}
	return false;
}


/**
 * Get post terms
 *
 * @param int    $post_id - the post id
 * @param string $term - the term slug
 *
 * @since 1.0.0
 *
 * @return array
 */
function hammock_get_post_terms( $post_id, $term ) {
	global $wpdb;
	$output  = array();
	$sql     = "SELECT t.term_id FROM jp_posts AS p LEFT JOIN jp_term_relationships AS tr ON (p.ID = tr.object_id) LEFT JOIN jp_term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) LEFT JOIN jp_terms AS t ON (t.term_id = tt.term_id) WHERE p.ID = %d AND  p.post_status = 'publish' AND tt.taxonomy = %s ORDER BY p.post_date DESC;";
	$results = $wpdb->get_results( $wpdb->prepare( $sql, $post_id, $term ) );
	foreach ( $results as $result ) {
		$term     = get_term( $result->term_id, $term );
		$output[] = $term;
	}
	return $output;
}

/**
 * Checks if term is protected
 *
 * @param null|int    $term_id - optional term id
 * @param null|string $taxonomy - taxonomy name (unused when checking directly a WP_Term object)
 *
 * @since 1.0.0
 *
 * @return bool
 */
function hammock_is_term_protected( $term_id = null, $taxonomy = null ) {

	global $wp_query;

	$check_term = false;

	if ( null === $term_id && null === $taxonomy && ( $wp_query->is_tax() || $wp_query->is_category() || $wp_query->is_tag() ) ) {

		$term = get_queried_object();

		if ( $term instanceof \WP_Term ) {
			$check_term = $term;
		}
	} elseif ( $term_id instanceof \WP_Term ) {
		$check_term = $term_id;
	}

	if ( (int) $check_term->term_id > 0 && is_string( $check_term->taxonomy ) ) {
		/**
		 * @see \Hammock\Rule\Category::has_access
		 */
		$has_access = apply_filters( 'hammock_term_content_has_access', true, $check_term, $check_term->taxonomy );
		return ! $has_access;
	}
	return false;
}

