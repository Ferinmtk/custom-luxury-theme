<?php
/**
 * Front-end assets — global CSS/JS/fonts, resource hints, page-specific enqueues.
 *
 * @package Luxury_Homes
 */

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    // Fonts: Fraunces (hero display) + Marcellus (site display) + Inter (body, incl. 300 for hero).
    wp_enqueue_style(
        'lh-fonts',
        'https://fonts.googleapis.com/css2?family=Archivo:wght@400;500&family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;0,6..96,600;1,6..96,400;1,6..96,500&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,400&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,400&family=Inter:wght@300;400;500;600&family=Marcellus&family=Space+Grotesk:wght@400;500&display=swap',
        array(),
        null
    );
    wp_enqueue_style('lh-main', get_template_directory_uri() . '/assets/css/main.css', array('lh-fonts'), LH_VERSION);

    /*
     * Image-placeholder tiles label themselves with the brand, via CSS
     * `content:`, which cannot call PHP. Publishing the name as a custom
     * property is what lets those rules follow lh_company_short() instead of
     * hardcoding it — one of them was still stamped with a previous project's
     * company name and rendered it to visitors.
     *
     * The value lands inside a CSS string, so quotes and backslashes are
     * stripped rather than escaped: either would end the string early and
     * break every rule after it.
     */
    $lh_label = preg_replace('/["\\\\]/', '', lh_company_short());
    wp_add_inline_style('lh-main', ':root{--lh-brand-label:"' . $lh_label . '";}');

    /*
     * Design layer — HOMEPAGE ONLY while the client picks a direction. Each
     * layer loads AFTER main.css and overrides it, so main.css stays the
     * single warm base and any layer can be dropped without editing a rule.
     * Inner pages stay warm-coherent rather than half-converted; widen the
     * is_front_page() gate page by page as the migration proceeds.
     *
     *   (default)          edition.css  — black edition
     *   ?palette=platinum  platinum.css — the earlier recolour
     *   ?palette=warm      neither      — the original scheme
     *
     * Being able to flip between all three in front of the client is the
     * point; nothing is thrown away until one is chosen.
     */
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only preview toggle, changes no state
    $lh_palette = isset($_GET['palette']) ? sanitize_key(wp_unslash($_GET['palette'])) : '';
    $lh_layer = 'edition';
    if ('warm' === $lh_palette) {
        $lh_layer = '';
    } elseif ('platinum' === $lh_palette) {
        $lh_layer = 'platinum';
    }
    $lh_layer = apply_filters('lh_design_layer', is_front_page() ? $lh_layer : '');

    if ($lh_layer && file_exists(get_template_directory() . '/assets/css/' . $lh_layer . '.css')) {
        wp_enqueue_style(
            'lh-' . $lh_layer,
            get_template_directory_uri() . '/assets/css/' . $lh_layer . '.css',
            array('lh-main'),
            LH_VERSION
        );
    }

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
 * Contact page only: Leaflet map for the service area.
 *
 * KEEP THE INLINE JS FREE OF // COMMENTS. It is emitted through
 * wp_add_inline_script and the cache layer minifies it, collapsing newlines —
 * at which point the first // swallows the rest of the script and the map
 * renders as an empty grey box with working zoom controls. Explain things
 * here instead. (The original code carried no comments at all; that was the
 * author knowing this, not an oversight.)
 *
 * Layers: Esri World Imagery, plus Esri transportation and place-name overlays
 * on top so roads and towns stay legible. No API key; no SLA either.
 * Note the {z}/{y}/{x} tile path — ArcGIS puts y before x, and the usual
 * {z}/{x}/{y} returns real tiles from the wrong place rather than erroring.
 *
 * Boundary: a hand-drawn Front Range corridor, blue over a dark casing — no
 * blue separates from tan rangeland on luminance alone (azure scores 1.3:1
 * against it), so the halo is what makes the line read. The polygon is
 * approximate and follows no county line; the four pins must stay inside it,
 * since fitBounds derives the whole view from poly.getBounds().
 *
 * No map-wide click handler — the frame pans and zooms, so a click meant to
 * explore was opening a new tab. The "Open in Google Maps" link above the
 * frame already does that deliberately. Pin clicks are kept.
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
  L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',{maxZoom:19,className:'si-tile-sat',attribution:'Imagery &copy; Esri, Maxar, Earthstar Geographics'}).addTo(map);
  L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}',{maxZoom:19,className:'si-tile-ref',pane:'overlayPane'}).addTo(map);
  L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',{maxZoom:19,className:'si-tile-ref',pane:'overlayPane'}).addTo(map);
  var area=[[40.13,-105.35],[40.13,-104.85],[39.85,-104.72],[39.38,-104.78],[39.38,-105.15],[39.58,-105.42],[39.95,-105.42]];
  L.polygon(area,{color:'#0b1a2b',weight:7,opacity:0.5,fill:false}).addTo(map);
  var poly=L.polygon(area,{color:'#5cc0ff',weight:3,opacity:1,fillColor:'#5cc0ff',fillOpacity:0.10}).addTo(map);
  function pin(lat,lng,label){var m=L.marker([lat,lng],{icon:L.divIcon({className:'',html:'<span class="si-pin"><b></b><span>'+label+'</span></span>',iconSize:[0,0]})}).addTo(map);m.on('click',function(){window.open('https://www.google.com/maps/search/?api=1&query='+lat+','+lng,'_blank','noopener');});}
  pin(39.7392,-104.9903,'Denver');pin(40.0150,-105.2705,'Boulder');pin(39.4578,-104.8961,'Castle Pines');pin(39.6333,-105.3172,'Evergreen');
  map.fitBounds(poly.getBounds(),{padding:[36,36]});
  map.on('focus',function(){map.scrollWheelZoom.enable();});
  map.on('blur',function(){map.scrollWheelZoom.disable();});
})();
JS;
    wp_add_inline_script('leaflet', $init);
});

/** Our Homes: architect-handwriting font for the drawn blueprint backdrop.
 *  About no longer needs it — the signed note that used it has been removed. */
add_action('wp_enqueue_scripts', function () {
    if (is_page('our-homes') || is_page_template('page-our-homes.php')) {
        wp_enqueue_style('lh-architect-font', 'https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap', array(), null);
    }
});