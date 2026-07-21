<?php
/**
 * Our Homes <head> — LCP image preload + Open Graph/Twitter tags.
 *
 * @package Luxury_Homes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Our Homes <head>: preload the LCP hero image and emit Open Graph / Twitter
 * tags (skipped when a dedicated SEO plugin is already handling them).
 */
add_action( 'wp_head', function () {
	if ( ! ( is_page( 'our-homes' ) || is_page_template( 'page-our-homes.php' ) ) ) {
		return;
	}

	list( $featured, ) = lh_split_featured( lh_get_homes() );
	$img = $featured ? lh_home_image_url( $featured->ID, 'full' ) : '';

	if ( $img ) {
		printf( "<link rel=\"preload\" as=\"image\" href=\"%s\" fetchpriority=\"high\">\n", esc_url( $img ) );
	}

	// Don't duplicate OG tags if a SEO plugin owns them.
	if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}

	$title = function_exists( 'wp_get_document_title' ) ? wp_get_document_title() : get_the_title();
	$desc  = 'Custom homes drawn from the land and stick-built on-site, one family at a time.';

	echo "<meta property=\"og:type\" content=\"website\">\n";
	printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
	printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $desc ) );
	printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( home_url( '/our-homes/' ) ) );
	if ( $img ) {
		printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $img ) );
	}
	echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
	printf( "<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr( $title ) );
	printf( "<meta name=\"twitter:description\" content=\"%s\">\n", esc_attr( $desc ) );
	if ( $img ) {
		printf( "<meta name=\"twitter:image\" content=\"%s\">\n", esc_url( $img ) );
	}
}, 5 );
