<?php
/**
 * Theme setup — supports, menus, image sizes, companion-plugin notice.
 *
 * @package Luxury_Homes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Functionality (Homes CPT, contact form, chat concierge, Anthropic API) now
 * lives in the companion plugin "Luxury Homes Core" so content survives a theme
 * switch. If it is not active, show an admin notice. Templates that render the
 * `home` CPT degrade to empty output rather than fatalling.
 */
add_action( 'admin_notices', function () {
	if ( function_exists( 'lhc_bootstrapped' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>Luxury Homes:</strong> '
		. 'the <em>Luxury Homes Core</em> plugin is not active. The Homes post type, '
		. 'contact form and chat concierge are disabled until it is installed and activated.'
		. '</p></div>';
} );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'luxury-homes' ),
	) );
	add_image_size( 'lh-portfolio', 900, 1125, true ); // 4:5 portrait cards
} );
