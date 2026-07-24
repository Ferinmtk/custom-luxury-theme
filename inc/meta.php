<?php
/**
 * Our Homes <head> — LCP image preload + Open Graph/Twitter tags.
 *
 * @package Luxury_Homes
 */

defined('ABSPATH') || exit;

/**
 * Front page <head>: preload the descent poster.
 *
 * With the film deferred (preload="none", src attached by main.js), poster.jpg
 * is the LCP element. Promote it so it starts downloading with the document
 * instead of waiting for CSS to resolve the <video>.
 */
add_action('wp_head', function () {
    if (!is_front_page()) {
        return;
    }
    printf(
        "<link rel=\"preload\" as=\"image\" href=\"%s\" fetchpriority=\"high\">\n",
        esc_url(get_template_directory_uri() . '/assets/img/poster.jpg')
    );
}, 5);

/**
 * Our Homes <head>: preload the LCP hero image and emit Open Graph / Twitter
 * tags (skipped when a dedicated SEO plugin is already handling them).
 */
add_action('wp_head', function () {
    if (!(is_page('our-homes') || is_page_template('page-our-homes.php'))) {
        return;
    }

    list($featured,) = lh_split_featured(lh_get_homes());
    $img = $featured ? lh_home_image_url($featured->ID, 'full') : '';

    if ($img) {
        printf("<link rel=\"preload\" as=\"image\" href=\"%s\" fetchpriority=\"high\">\n", esc_url($img));
    }

    // Don't duplicate OG tags if a SEO plugin owns them.
    if (defined('WPSEO_VERSION') || class_exists('RankMath') || defined('SEOPRESS_VERSION') || defined('AIOSEO_VERSION')) {
        return;
    }

    $title = function_exists('wp_get_document_title') ? wp_get_document_title() : get_the_title();
    $desc = 'Custom homes drawn from the land and stick-built on-site, one family at a time.';

    echo "<meta property=\"og:type\" content=\"website\">\n";
    printf("<meta property=\"og:title\" content=\"%s\">\n", esc_attr($title));
    printf("<meta property=\"og:description\" content=\"%s\">\n", esc_attr($desc));
    printf("<meta property=\"og:url\" content=\"%s\">\n", esc_url(home_url('/our-homes/')));
    if ($img) {
        printf("<meta property=\"og:image\" content=\"%s\">\n", esc_url($img));
    }
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    printf("<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr($title));
    printf("<meta name=\"twitter:description\" content=\"%s\">\n", esc_attr($desc));
    if ($img) {
        printf("<meta name=\"twitter:image\" content=\"%s\">\n", esc_url($img));
    }
}, 5);

/**
 * About page <head> — meta description, Open Graph and Twitter tags, plus a
 * preload for the hero band (the LCP element once it exists).
 *
 * The About page is a vetting page people reach late and often share (texted to
 * a spouse, posted to a forum), so a proper card matters here specifically.
 * Skipped when a dedicated SEO plugin is already emitting these tags.
 */
add_action('wp_head', function () {
    if (!(is_page('about') || is_page_template('page-about.php'))) {
        return;
    }

    $hero = '';
    if (function_exists('get_field')) {
        $f = get_field('about_hero_image');
        if (is_array($f) && !empty($f['url'])) {
            $hero = $f['url'];
        } elseif (is_numeric($f)) {
            $hero = wp_get_attachment_image_url((int)$f, 'full');
        } elseif (is_string($f) && '' !== $f) {
            $hero = $f;
        }
    }
    if ('' === $hero) {
        $path = get_template_directory() . '/assets/img/about-hero.jpg';
        if (file_exists($path)) {
            $hero = get_template_directory_uri() . '/assets/img/about-hero.jpg';
        }
    }

    if ($hero) {
        printf("<link rel=\"preload\" as=\"image\" href=\"%s\" fetchpriority=\"high\">\n", esc_url($hero));
    }

    if (defined('WPSEO_VERSION') || class_exists('RankMath') || defined('SEOPRESS_VERSION') || defined('AIOSEO_VERSION')) {
        return;
    }

    $company = function_exists('lh_company') ? lh_company() : get_bloginfo('name');
    $title = function_exists('wp_get_document_title') ? wp_get_document_title() : get_the_title();
    $desc = sprintf(
    /* translators: %s: company name */
        esc_html__('Meet the people behind %s — the four who design, build, finance and stand behind every custom home we take on.', 'luxury-homes'),
        $company
    );

    echo "<meta name=\"description\" content=\"" . esc_attr($desc) . "\">\n";
    echo "<meta property=\"og:type\" content=\"website\">\n";
    printf("<meta property=\"og:title\" content=\"%s\">\n", esc_attr($title));
    printf("<meta property=\"og:description\" content=\"%s\">\n", esc_attr($desc));
    printf("<meta property=\"og:url\" content=\"%s\">\n", esc_url(home_url('/about/')));
    if ($hero) {
        printf("<meta property=\"og:image\" content=\"%s\">\n", esc_url($hero));
    }
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    printf("<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr($title));
    printf("<meta name=\"twitter:description\" content=\"%s\">\n", esc_attr($desc));
    if ($hero) {
        printf("<meta name=\"twitter:image\" content=\"%s\">\n", esc_url($hero));
    }
}, 5);