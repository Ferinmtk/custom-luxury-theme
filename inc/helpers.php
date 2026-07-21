<?php
/**
 * Generic template helpers — ACF-safe fields, asset URLs, company name, images.
 *
 * @package Luxury_Homes
 */

defined( 'ABSPATH' ) || exit;

/**
 * ACF-safe field getter with default fallback.
 * Works before ACF is installed; once ACF is active,
 * matching field names on the front page override defaults.
 */
function lh_field( $name, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name );
		if ( $value !== null && $value !== '' && $value !== false ) {
			return $value;
		}
	}
	return $default;
}

/**
 * Company name — single source of truth for header, footer and legal line.
 *
 * Resolution order: ACF "company_name" -> WP Site Title -> theme default.
 * If the wordmark reads wrong, set Settings > General > Site Title.
 * Filter with 'lh_company' to override without touching options.
 */
function lh_company() {
	$name = trim( (string) lh_field( 'company_name', '' ) );
	if ( '' === $name ) {
		$name = trim( (string) get_bloginfo( 'name' ) );
	}
	if ( '' === $name ) {
		$name = 'Tester';
	}
	return apply_filters( 'lh_company', $name );
}

/**
 * Render an image from an ACF field that may return an attachment ID,
 * an array, or a plain URL. Falls back to a theme asset path.
 * Returns '' when nothing usable is available.
 */
function lh_field_image( $field, $fallback_asset = '', $size = 'large', $attr = array() ) {
	$value = function_exists( 'get_field' ) ? get_field( $field ) : null;

	if ( is_array( $value ) && ! empty( $value['ID'] ) ) {
		$value = $value['ID'];
	}
	if ( is_numeric( $value ) && (int) $value > 0 ) {
		$img = wp_get_attachment_image( (int) $value, $size, false, $attr );
		if ( $img ) { return $img; }
	}

	$url = is_string( $value ) && '' !== $value ? $value : '';
	if ( '' === $url && '' !== $fallback_asset ) {
		$path = get_template_directory() . '/assets/' . ltrim( $fallback_asset, '/' );
		if ( file_exists( $path ) ) {
			$url = lh_asset( $fallback_asset );
		}
	}
	if ( '' === $url ) { return ''; }

	$out = '<img src="' . esc_url( $url ) . '"';
	foreach ( $attr as $k => $v ) {
		$out .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return $out . '>';
}

/** Theme asset URL helper. */
function lh_asset( $path ) {
	return esc_url( get_template_directory_uri() . '/assets/' . ltrim( $path, '/' ) );
}

/**
 * Default portfolio items (placeholder stills from the hero film).
 * Replace via ACF repeater "portfolio" (fields: image, title, location)
 * once real project photos arrive.
 */
function lh_default_portfolio() {
	return array(
		array( 'image' => lh_asset( 'img/project-1.jpg' ), 'title' => 'The Ridgeline Residence', 'location' => 'Cherry Hills' ),
		array( 'image' => lh_asset( 'img/project-2.jpg' ), 'title' => 'Stonebrook Court',        'location' => 'Castle Pines' ),
		array( 'image' => lh_asset( 'img/project-3.jpg' ), 'title' => 'The Meridian House',      'location' => 'Boulder' ),
		array( 'image' => lh_asset( 'img/project-4.jpg' ), 'title' => 'Willow & Vine',           'location' => 'Greenwood Village' ),
	);
}
