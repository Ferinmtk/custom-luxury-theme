<?php
/**
 * Luxury Homes — theme bootstrap.
 *
 * Theme logic is split into single-responsibility modules under /inc. This file
 * only defines the version and loads them in dependency order (helpers first).
 *
 * NOTE: WordPress requires the page templates, header.php, footer.php,
 * functions.php and style.css to live in the theme ROOT — they are found by the
 * template hierarchy and cannot be moved into subfolders. Splitting logic into
 * /inc (and reusable markup into /template-parts) is the correct WordPress way to
 * get clean architecture without breaking that contract.
 *
 * @package Luxury_Homes
 */

defined( 'ABSPATH' ) || exit;

define( 'LH_VERSION', '1.24.4' );

$lh_inc = get_template_directory() . '/inc/';

require $lh_inc . 'helpers.php';     // lh_field, lh_asset, lh_company, images
require $lh_inc . 'navigation.php';  // primary menu items + renderer
require $lh_inc . 'icons.php';       // inline SVG icon library
require $lh_inc . 'homes.php';       // portfolio (Homes CPT) queries/helpers
require $lh_inc . 'setup.php';       // theme supports, menus, image sizes, admin notice
require $lh_inc . 'enqueue.php';     // styles / scripts / fonts
require $lh_inc . 'meta.php';        // Our Homes <head> preload + Open Graph

unset( $lh_inc );
