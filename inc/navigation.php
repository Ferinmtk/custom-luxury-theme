<?php
/**
 * Primary navigation — canonical items and a marked-up list renderer.
 *
 * @package Luxury_Homes
 */

defined( 'ABSPATH' ) || exit;

/** Site pages for navigation (header, overlay, footer). */
function lh_nav_items() {
	return array(
		'our-homes'    => array( 'label' => 'Our Homes',    'url' => home_url( '/our-homes/' ) ),
		'how-we-build' => array( 'label' => 'How We Build', 'url' => home_url( '/how-we-build/' ) ),
		'about'        => array( 'label' => 'About',        'url' => home_url( '/about/' ) ),
		'contact'      => array( 'label' => 'Contact',      'url' => home_url( '/contact/' ) ),
	);
}

/** Print a nav list with current-page markers. */
function lh_nav_list( $class, $exclude = array() ) {
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( lh_nav_items() as $slug => $item ) {
		if ( in_array( $slug, (array) $exclude, true ) ) { continue; }
		$current = is_page( $slug ) || ( 'our-homes' === $slug && is_singular( 'home' ) );
		echo '<li' . ( $current ? ' class="is-current"' : '' ) . '>';
		echo '<a href="' . esc_url( $item['url'] ) . '"' . ( $current ? ' aria-current="page"' : '' ) . '>' . esc_html( $item['label'] ) . '</a>';
		echo '</li>';
	}
	echo '</ul>';
}
