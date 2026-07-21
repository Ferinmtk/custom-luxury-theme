<?php
/**
 * Single "home" — detail page.
 */
get_header();

while ( have_posts() ) : the_post();
	$meta      = lh_home_meta( get_the_ID() );
	$prev_home = get_previous_post();
	$next_home = get_next_post();
	$homes_url  = get_post_type_archive_link( 'home' );
	$homes_page = get_page_by_path( 'our-homes' );
	if ( $homes_page ) { $homes_url = get_permalink( $homes_page ); }
	?>

<main id="main" class="single-home-page">

	<!-- Hero -->
	<section class="sh-hero">
		<?php echo lh_home_image( get_the_ID(), 'full', array( 'class' => 'sh-hero-img', 'alt' => get_the_title() ) ); ?>
		<div class="sh-hero-grade" aria-hidden="true"></div>
		<div class="sh-hero-copy">
			<?php if ( $meta['location'] ) : ?>
				<span class="eyebrow"><?php echo esc_html( $meta['location'] ); ?></span>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
		</div>
	</section>

	<!-- Specs bar -->
	<?php if ( $meta['beds'] || $meta['baths'] || $meta['sqft'] || $meta['style'] || $meta['year'] ) : ?>
		<section class="sh-specs">
			<div class="sh-specs-in">
				<?php if ( $meta['beds'] ) : ?>
					<div class="sh-spec"><span class="n"><?php echo esc_html( $meta['beds'] ); ?></span><span class="l">Beds</span></div>
				<?php endif; ?>
				<?php if ( $meta['baths'] ) : ?>
					<div class="sh-spec"><span class="n"><?php echo esc_html( $meta['baths'] ); ?></span><span class="l">Baths</span></div>
				<?php endif; ?>
				<?php if ( $meta['sqft'] ) : ?>
					<div class="sh-spec"><span class="n"><?php echo esc_html( number_format_i18n( (float) $meta['sqft'] ) ); ?></span><span class="l">Sq Ft</span></div>
				<?php endif; ?>
				<?php if ( $meta['style'] ) : ?>
					<div class="sh-spec"><span class="n"><?php echo esc_html( $meta['style'] ); ?></span><span class="l">Style</span></div>
				<?php endif; ?>
				<?php if ( $meta['year'] ) : ?>
					<div class="sh-spec"><span class="n"><?php echo esc_html( $meta['year'] ); ?></span><span class="l">Completed</span></div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- Story -->
	<section class="sh-story">
		<span class="eyebrow">The story</span>
		<div class="sh-story-copy"><?php the_content(); ?></div>
	</section>

	<!-- Gallery -->
	<?php if ( $meta['gallery'] ) : ?>
		<section class="sh-gallery" aria-label="Photo gallery">
			<div class="sh-gallery-grid">
				<?php foreach ( $meta['gallery'] as $img_id ) :
					$full = wp_get_attachment_image_url( $img_id, 'full' );
					if ( ! $full ) { continue; } ?>
					<button type="button" class="sh-photo" data-full="<?php echo esc_url( $full ); ?>">
						<?php echo wp_get_attachment_image( $img_id, 'large' ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- Prev / next -->
	<nav class="sh-nav" aria-label="More homes">
		<div class="sh-nav-side">
			<?php if ( $prev_home ) : ?>
				<a href="<?php echo esc_url( get_permalink( $prev_home ) ); ?>">
					<span class="d">&larr; Previous home</span>
					<span class="t"><?php echo esc_html( get_the_title( $prev_home ) ); ?></span>
				</a>
			<?php endif; ?>
		</div>
		<a class="sh-nav-back" href="<?php echo esc_url( $homes_url ? $homes_url : home_url( '/our-homes/' ) ); ?>">All homes</a>
		<div class="sh-nav-side sh-nav-next">
			<?php if ( $next_home ) : ?>
				<a href="<?php echo esc_url( get_permalink( $next_home ) ); ?>">
					<span class="d">Next home &rarr;</span>
					<span class="t"><?php echo esc_html( get_the_title( $next_home ) ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</nav>

	<!-- CTA -->
	<section class="sh-cta">
		<div class="sh-cta-in">
			<span class="eyebrow">Begin</span>
			<h2>Begin <em>your</em> home.</h2>
			<p>Tell us about your land and the way you live. We take on a limited number of homes each year.</p>
			<a class="sh-btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Begin your home &rarr;</a>
		</div>
	</section>

</main>

<?php endwhile; get_footer(); ?>
