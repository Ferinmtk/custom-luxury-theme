<?php
/**
 * Luxury Homes theme setup.
 */

define( 'LH_VERSION', '1.24.4' );

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

add_action( 'wp_enqueue_scripts', function () {
	// Fonts: Fraunces (hero display) + Marcellus (site display) + Inter (body, incl. 300 for hero).
	wp_enqueue_style(
		'lh-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wght@400;500&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,400&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,400&family=Inter:wght@300;400;500;600&family=Marcellus&family=Space+Grotesk:wght@400;500&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'lh-main', get_template_directory_uri() . '/assets/css/main.css', array( 'lh-fonts' ), LH_VERSION );
	wp_enqueue_script( 'lh-main', get_template_directory_uri() . '/assets/js/main.js', array(), LH_VERSION, true );
} );

// Preconnect to Google Fonts (matches approved hero prototype head).
add_filter( 'wp_resource_hints', function ( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}, 10, 2 );

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

/**
 * Contact page only: Leaflet map for the service area.
 * Tiles load client-side from CARTO/OpenStreetMap (no API key).
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! ( is_page( 'contact' ) || is_page_template( 'page-contact.php' ) ) ) {
		return;
	}
	wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
	wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
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
	wp_add_inline_script( 'leaflet', $init );
} );

/** Our Homes page only: architect-handwriting font for the drawn backdrop. */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_page( 'our-homes' ) || is_page_template( 'page-our-homes.php' ) ) {
		wp_enqueue_style( 'lh-architect-font', 'https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap', array(), null );
	}
} );
