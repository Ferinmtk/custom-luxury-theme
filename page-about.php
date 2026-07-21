<?php
/**
 * Template Name: About
 * Auto-applies to the page with slug "about".
 */
get_header();
?>

<main id="main" class="homes-page about-page">

	<!-- 1. Intro -->
	<?php while ( have_posts() ) : the_post(); ?>
		<section class="homes-hero">
			<div class="homes-hero-in">
				<span class="eyebrow"><?php echo esc_html( lh_field( 'about_eyebrow', 'About' ) ); ?></span>
				<h1>Our <em>Story</em></h1>
				<?php if ( trim( get_the_content() ) ) : ?>
					<div class="homes-intro"><?php the_content(); ?></div>
				<?php else : ?>
					<div class="homes-intro"><p>A small Utah builder with one conviction: a home should be shaped by its land, its family and the hands that raise it.</p></div>
				<?php endif; ?>
			</div>
		</section>
	<?php endwhile; ?>

	<!-- 2. Story -->
	<section class="ab-story">
		<div class="ab-story-in">
			<div class="ab-story-copy">
				<p class="lead">Tester began twenty years ago with a pickup, a framing crew of three and a promise to a family in Heber City: we would build their home as if it were our own.</p>
				<p>We never became a volume builder, on purpose. Every home is stick-built on-site with no factory panels and no rotating subcontractors. The land always has something to say once you start digging and only a crew that's present every day can listen.</p>
				<p>One home at a time means our best people are on your site, our weekly walkthroughs actually happen and the person who shook your hand at the first meeting is the one handing you the keys.</p>
			</div>
			<figure class="ab-photo">
				<span class="ab-ph" aria-hidden="true"></span>
				<figcaption>Site walk, Wasatch Back (photo placeholder)</figcaption>
			</figure>
		</div>
	</section>

	<!-- 3. Values strip -->
	<section class="hm-values">
		<div class="hm-values-in">
			<div class="hm-value">
				<h3>Craft</h3>
				<p>Master trades who have been with us for years, working stone, timber and glass by hand.</p>
			</div>
			<div class="hm-value">
				<h3>Honesty</h3>
				<p>Real budgets, real timelines and news delivered early, good or bad.</p>
			</div>
			<div class="hm-value">
				<h3>One at a time</h3>
				<p>A limited number of homes each year, each with a dedicated crew.</p>
			</div>
			<div class="hm-value">
				<h3>The land first</h3>
				<p>The site sets the home's shape. We protect what's worth keeping.</p>
			</div>
		</div>
	</section>

	<!-- 4. Founder -->
	<section class="ab-founder">
		<div class="ab-founder-in">
			<figure class="ab-founder-photo">
				<span class="ab-ph" aria-hidden="true"></span>
			</figure>
			<div class="ab-founder-copy">
				<span class="eyebrow">Founder</span>
				<h2>Jane Tester</h2>
				<p>Jane grew up on job sites. Her father was a finish carpenter in the Heber Valley. She founded Tester after a decade framing custom homes across the Wasatch, still walks every site weekly and still owns the first hammer.</p>
			</div>
		</div>
	</section>

	<!-- 5. CTA -->
	<section class="hm-cta">
		<div class="hm-cta-in">
			<span class="eyebrow">Next</span>
			<h2>Let&rsquo;s build <em>together</em>.</h2>
			<a class="sh-btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Start the conversation &rarr;</a>
		</div>
	</section>

</main>

<?php get_footer(); ?>
