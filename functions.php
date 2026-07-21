<?php
/**
 * Luxury Homes theme setup.
 */

define('LH_VERSION', '1.24.4');

/**
 * Functionality (Homes CPT, contact form, chat concierge, Anthropic API) now
 * lives in the companion plugin "Luxury Homes Core" so content survives a theme
 * switch. If it is not active, show an admin notice. Templates that render the
 * `home` CPT degrade to empty output rather than fatalling.
 */
add_action('admin_notices', function () {
    if (function_exists('lhc_bootstrapped')) {
        return;
    }
    echo '<div class="notice notice-warning"><p><strong>Luxury Homes:</strong> '
        . 'the <em>Luxury Homes Core</em> plugin is not active. The Homes post type, '
        . 'contact form and chat concierge are disabled until it is installed and activated.'
        . '</p></div>';
});

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'luxury-homes'),
    ));
    add_image_size('lh-portfolio', 900, 1125, true); // 4:5 portrait cards
});

add_action('wp_enqueue_scripts', function () {
    // Fonts: Fraunces (hero display) + Marcellus (site display) + Inter (body, incl. 300 for hero).
    wp_enqueue_style(
        'lh-fonts',
        'https://fonts.googleapis.com/css2?family=Archivo:wght@400;500&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,400&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,400&family=Inter:wght@300;400;500;600&family=Marcellus&family=Space+Grotesk:wght@400;500&display=swap',
        array(),
        null
    );
    wp_enqueue_style('lh-main', get_template_directory_uri() . '/assets/css/main.css', array('lh-fonts'), LH_VERSION);
    wp_enqueue_script('lh-main', get_template_directory_uri() . '/assets/js/main.js', array(), LH_VERSION, true);
});

// Preconnect to Google Fonts (matches approved hero prototype head).
add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }
    return $urls;
}, 10, 2);

/**
 * ACF-safe field getter with default fallback.
 * Works before ACF is installed; once ACF is active,
 * matching field names on the front page override defaults.
 */
function lh_field($name, $default = '')
{
    if (function_exists('get_field')) {
        $value = get_field($name);
        if ($value !== null && $value !== '' && $value !== false) {
            return $value;
        }
    }
    return $default;
}

/** Site pages for navigation (header, overlay, footer). */
function lh_nav_items()
{
    return array(
        'our-homes'    => array('label' => 'Our Homes', 'url' => home_url('/our-homes/')),
        'how-we-build' => array('label' => 'How We Build', 'url' => home_url('/how-we-build/')),
        'about'        => array('label' => 'About', 'url' => home_url('/about/')),
        'contact'      => array('label' => 'Contact', 'url' => home_url('/contact/')),
    );
}

/** Print a nav list with current-page markers. */
function lh_nav_list($class, $exclude = array())
{
    echo '<ul class="' . esc_attr($class) . '">';
    foreach (lh_nav_items() as $slug => $item) {
        if (in_array($slug, (array)$exclude, true)) {
            continue;
        }
        $current = is_page($slug) || ('our-homes' === $slug && is_singular('home'));
        echo '<li' . ($current ? ' class="is-current"' : '') . '>';
        echo '<a href="' . esc_url($item['url']) . '"' . ($current ? ' aria-current="page"' : '') . '>' . esc_html($item['label']) . '</a>';
        echo '</li>';
    }
    echo '</ul>';
}

/**
 * Company name — single source of truth for header, footer and legal line.
 * ACF "company_name" wins; otherwise the WP site title; otherwise the theme default.
 */
/**
 * Company name — single source of truth for header, footer and legal line.
 *
 * Resolution order: ACF "company_name" -> WP Site Title -> theme default.
 * If the wordmark reads wrong, set Settings > General > Site Title.
 * Filter with 'lh_company' to override without touching options.
 */
function lh_company()
{
    $name = trim((string)lh_field('company_name', ''));
    if ('' === $name) {
        $name = trim((string)get_bloginfo('name'));
    }
    if ('' === $name) {
        $name = 'Tester';
    }
    return apply_filters('lh_company', $name);
}

/**
 * Render an image from an ACF field that may return an attachment ID,
 * an array, or a plain URL. Falls back to a theme asset path.
 * Returns '' when nothing usable is available.
 */
function lh_field_image($field, $fallback_asset = '', $size = 'large', $attr = array())
{
    $value = function_exists('get_field') ? get_field($field) : null;

    if (is_array($value) && !empty($value['ID'])) {
        $value = $value['ID'];
    }
    if (is_numeric($value) && (int)$value > 0) {
        $img = wp_get_attachment_image((int)$value, $size, false, $attr);
        if ($img) {
            return $img;
        }
    }

    $url = is_string($value) && '' !== $value ? $value : '';
    if ('' === $url && '' !== $fallback_asset) {
        $path = get_template_directory() . '/assets/' . ltrim($fallback_asset, '/');
        if (file_exists($path)) {
            $url = lh_asset($fallback_asset);
        }
    }
    if ('' === $url) {
        return '';
    }

    $out = '<img src="' . esc_url($url) . '"';
    foreach ($attr as $k => $v) {
        $out .= ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
    }
    return $out . '>';
}

/** Theme asset URL helper. */
function lh_asset($path)
{
    return esc_url(get_template_directory_uri() . '/assets/' . ltrim($path, '/'));
}

/**
 * Default portfolio items (placeholder stills from the hero film).
 * Replace via ACF repeater "portfolio" (fields: image, title, location)
 * once real project photos arrive.
 */
function lh_default_portfolio()
{
    return array(
        array('image' => lh_asset('img/project-1.jpg'), 'title' => 'The Ridgeline Residence', 'location' => 'Cherry Hills'),
        array('image' => lh_asset('img/project-2.jpg'), 'title' => 'Stonebrook Court', 'location' => 'Castle Pines'),
        array('image' => lh_asset('img/project-3.jpg'), 'title' => 'The Meridian House', 'location' => 'Boulder'),
        array('image' => lh_asset('img/project-4.jpg'), 'title' => 'Willow & Vine', 'location' => 'Greenwood Village'),
    );
}

/**
 * Contact page only: Leaflet map for the service area.
 * Tiles load client-side from CARTO/OpenStreetMap (no API key).
 */
add_action('wp_enqueue_scripts', function () {
    if (!(is_page('contact') || is_page_template('page-contact.php'))) {
        return;
    }
    wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
    wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
    $init = <<<'JS'
(function(){
  var el=document.getElementById('si-map'); if(!el||!window.L) return;
  var map=L.map(el,{scrollWheelZoom:false,zoomControl:true});
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{maxZoom:18,subdomains:'abcd',attribution:'&copy; OpenStreetMap &copy; CARTO'}).addTo(map);
  var area=[[40.86,-111.96],[40.80,-111.55],[40.72,-111.28],[40.60,-111.20],[40.44,-111.34],[40.43,-111.58],[40.58,-111.86],[40.76,-112.00]];
  var poly=L.polygon(area,{color:'#a8834f',weight:2,opacity:0.9,dashArray:'2 6',fillColor:'#a8834f',fillOpacity:0.08}).addTo(map);
  function pin(lat,lng,label){var m=L.marker([lat,lng],{icon:L.divIcon({className:'',html:'<span class="si-pin"><b></b><span>'+label+'</span></span>',iconSize:[0,0]})}).addTo(map);m.on('click',function(){window.open('https://www.google.com/maps/search/?api=1&query='+lat+','+lng,'_blank','noopener');});}
  pin(40.7608,-111.8910,'Salt Lake');pin(40.6461,-111.4980,'Park City');pin(40.5070,-111.4130,'Heber');pin(40.6431,-111.2800,'Kamas');
  map.fitBounds(poly.getBounds(),{padding:[36,36]});
  map.on('click',function(e){window.open('https://www.google.com/maps/@'+e.latlng.lat.toFixed(5)+','+e.latlng.lng.toFixed(5)+',11z','_blank','noopener');});
  map.on('focus',function(){map.scrollWheelZoom.enable();});
  map.on('blur',function(){map.scrollWheelZoom.disable();});
})();
JS;
    wp_add_inline_script('leaflet', $init);
});

/** Our Homes page only: architect-handwriting font for the drawn backdrop. */
add_action('wp_enqueue_scripts', function () {
    if (is_page('our-homes') || is_page_template('page-our-homes.php')) {
        wp_enqueue_style('lh-architect-font', 'https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap', array(), null);
    }
});

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
function lh_get_homes()
{
    static $cache = null;
    if (null !== $cache) {
        return $cache;
    }
    if (!post_type_exists('home')) {
        $cache = array();
        return $cache;
    }
    $cache = get_posts(array(
        'post_type'      => 'home',
        'posts_per_page' => (int)apply_filters('lh_homes_per_page', 60),
        'orderby'        => array('menu_order' => 'ASC', 'date' => 'DESC'),
    ));
    return $cache;
}

/**
 * Split homes into array( $featured, $rest ).
 * Featured = first flagged with _home_featured, else the first home.
 */
function lh_split_featured(array $homes)
{
    $featured = null;
    $rest     = array();
    foreach ($homes as $h) {
        if (!$featured && get_post_meta($h->ID, '_home_featured', true)) {
            $featured = $h;
        } else {
            $rest[] = $h;
        }
    }
    if (!$featured && $rest) {
        $featured = array_shift($rest);
    }
    return array($featured, $rest);
}

/**
 * Best available image URL for a home, matching what the templates display.
 * Prefers the exact <img> the plugin renders; falls back to the featured image.
 */
function lh_home_image_url($id, $size = 'large')
{
    if (function_exists('lh_home_image')) {
        $html = lh_home_image($id, $size, array());
        if (is_string($html) && preg_match('/src=[\'"]([^\'"]+)[\'"]/', $html, $m)) {
            return $m[1];
        }
    }
    if (has_post_thumbnail($id)) {
        $url = get_the_post_thumbnail_url($id, $size);
        if ($url) {
            return $url;
        }
    }
    return '';
}

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
    $desc  = 'Custom homes drawn from the land and stick-built on-site, one family at a time.';

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
 * Inline social icon SVGs for the footer. Returns '' for unknown names.
 * Icons inherit color via currentColor and are sized in CSS.
 */
function lh_social_icon($name)
{
    $icons = array(
        'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
        'houzz'     => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9.005 24v-8.002h5.99V24H24V8.895L6.005 3.681V0H0v24z"/></svg>',
        'linkedin'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
        'pinterest' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345c-.091.378-.293 1.194-.333 1.361-.052.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>',
        'facebook'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
    );
    return isset($icons[$name]) ? $icons[$name] : '';
}

/**
 * Inline UI icons for the "Start where you are" option cards (Feather-style,
 * stroke = currentColor). Returns '' for unknown names.
 */
function lh_option_icon($name)
{
    $common = 'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
    $paths  = array(
        'map-pin'  => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'search'   => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'activity' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>',
        'compass'  => '<circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>',
        'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'eye'      => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
    );
    if (!isset($paths[$name])) {
        return '';
    }
    return '<svg ' . $common . '>' . $paths[$name] . '</svg>';
}