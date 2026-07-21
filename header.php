<?php ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="hdr" id="site-header">
	<div class="hdr-in">
		<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html( lh_company() ); ?>
		</a>
		<div class="hdr-right">
		<nav class="hdr-nav" aria-label="<?php esc_attr_e( 'Primary', 'luxury-homes' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'hdr-menu',
					'depth'          => 1,
				) );
			} else {
				lh_nav_list( 'hdr-menu', array( 'contact' ) );
			}
			?>
		</nav>
		<a class="hdr-cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'luxury-homes' ); ?></a>
		<button class="nav-toggle" id="navToggle" type="button" aria-expanded="false" aria-controls="navOverlay" aria-label="<?php esc_attr_e( 'Menu', 'luxury-homes' ); ?>">
			<i></i><i></i>
		</button>
		</div>
	</div>
</header>

<div class="nav-overlay" id="navOverlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'luxury-homes' ); ?>">
	<nav aria-label="<?php esc_attr_e( 'Mobile', 'luxury-homes' ); ?>">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'ov-menu',
				'depth'          => 1,
			) );
		} else {
			lh_nav_list( 'ov-menu' );
		}
		?>
	</nav>
</div>
