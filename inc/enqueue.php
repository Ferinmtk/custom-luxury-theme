<?php
/**
 * Front-end assets — global CSS/JS/fonts, resource hints, page-specific enqueues.
 *
 * @package Luxury_Homes
 */

defined( 'ABSPATH' ) || exit;

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
