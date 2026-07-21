<?php
/**
 * Portfolio (Homes CPT) queries and image helpers, shared by templates and <head>.
 *
 * @package Luxury_Homes
 */

defined( 'ABSPATH' ) || exit;

/**
 * ---------------------------------------------------------------------------
 * Our Homes portfolio helpers (shared by the template and the <head> hooks so
 * preload / Open Graph always match what actually renders).
 * ---------------------------------------------------------------------------
 */

/**
 * All published homes in portfolio order. Statically cached per request and
 * bounded (filter 'lh_homes_per_page' to raise) so a large CPT can't run away.
 */
function lh_get_homes() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	if ( ! post_type_exists( 'home' ) ) {
		$cache = array();
		return $cache;
	}
	$cache = get_posts( array(
		'post_type'      => 'home',
		'posts_per_page' => (int) apply_filters( 'lh_homes_per_page', 60 ),
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	) );
	return $cache;
}

/**
 * Split homes into array( $featured, $rest ).
 * Featured = first flagged with _home_featured, else the first home.
 */
function lh_split_featured( array $homes ) {
	$featured = null;
	$rest     = array();
	foreach ( $homes as $h ) {
		if ( ! $featured && get_post_meta( $h->ID, '_home_featured', true ) ) {
			$featured = $h;
		} else {
			$rest[] = $h;
		}
	}
	if ( ! $featured && $rest ) {
		$featured = array_shift( $rest );
	}
	return array( $featured, $rest );
}

/**
 * Best available image URL for a home, matching what the templates display.
 * Prefers the exact <img> the plugin renders; falls back to the featured image.
 */
function lh_home_image_url( $id, $size = 'large' ) {
	if ( function_exists( 'lh_home_image' ) ) {
		$html = lh_home_image( $id, $size, array() );
		if ( is_string( $html ) && preg_match( '/src=[\'"]([^\'"]+)[\'"]/', $html, $m ) ) {
			return $m[1];
		}
	}
	if ( has_post_thumbnail( $id ) ) {
		$url = get_the_post_thumbnail_url( $id, $size );
		if ( $url ) {
			return $url;
		}
	}
	return '';
}
