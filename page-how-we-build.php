<?php
/**
 * Template Name: How We Build
 * Auto-applies to the page with slug "how-we-build".
 */
get_header();
?>

<main id="main" class="homes-page hwb-page">

	<!-- 1. Intro -->
	<?php while ( have_posts() ) : the_post(); ?>
		<section class="homes-hero">
			<div class="homes-hero-in">
				<span class="eyebrow"><?php echo esc_html( lh_field( 'hwb_eyebrow', 'How We Build' ) ); ?></span>
				<h1><?php the_title(); ?></h1>
				<?php if ( trim( get_the_content() ) ) : ?>
					<div class="homes-intro"><?php the_content(); ?></div>
				<?php else : ?>
					<div class="homes-intro"><p>From the first walk of the land to the day we hand you the keys. One crew, one home, built entirely on-site. Scroll to watch a home rise.</p></div>
				<?php endif; ?>
			</div>
		</section>
	<?php endwhile; ?>

	<!-- 2. Scroll-scrubbed build -->
	<section class="build" id="build">
		<div class="b-pin">
			<div class="b-mat" id="buildMat">
				<video
					id="buildVid"
					muted
					playsinline
					preload="auto"
					src="<?php echo lh_asset( 'video/build.mp4' ); ?>"
					poster="<?php echo lh_asset( 'img/build-poster.jpg' ); ?>"
				></video>
				<div class="d-grade" aria-hidden="true"></div>

				<div class="b-copy" id="b1">
					<span class="eyebrow">01 &middot; The Land</span>
					<h2>It starts with the <em>ground</em>.</h2>
					<p>We walk the site, read its slopes and light, then set the home where the land wants it.</p>
				</div>
				<div class="b-copy" id="b2">
					<span class="eyebrow">02 &middot; Foundation</span>
					<h2>Anchored to <em>bedrock</em>.</h2>
					<p>Excavation and engineered footings. It's the part no one sees, done like everything else.</p>
				</div>
				<div class="b-copy" id="b3">
					<span class="eyebrow">03 &middot; Framing</span>
					<h2>The rooms take <em>shape</em>.</h2>
					<p>Stick-built on-site, wall by wall, until the views are framed for the first time.</p>
				</div>
				<div class="b-copy" id="b4">
					<span class="eyebrow">04 &middot; The Finish</span>
					<h2>Slow on <em>purpose</em>.</h2>
					<p>Stone, timber, plaster, brass. The final months are where craft earns its name.</p>
				</div>
			</div>

			<div class="b-steps" aria-hidden="true">
				<div class="b-step-read"><span id="bStepNum">01</span><span>/ 04</span></div>
				<div class="b-rail">
					<i id="bRailFill"></i>
					<s class="tick" style="top:0"></s>
					<s class="tick" style="top:33.33%"></s>
					<s class="tick" style="top:66.66%"></s>
					<s class="tick" style="top:100%"></s>
				</div>
				<div class="b-rail-label">Build</div>
			</div>

			<div class="d-cue" id="bCue"><?php esc_html_e( 'Scroll to build', 'luxury-homes' ); ?><i></i></div>
		</div>
	</section>

	<!-- 4. Detailed steps -->
	<section class="hwb-steps">
		<div class="hwb-steps-in">
			<ol class="hwb-list">
				<li class="hwb-step">
					<span class="num">01</span>
					<div class="body">
						<h3>The Land</h3>
						<p>Before a line is drawn, we spend time on your site mapping slopes, mature trees, sun paths and views. The home is positioned so the land does half the design work while the excavation plan protects everything worth keeping.</p>
					</div>
				</li>
				<li class="hwb-step">
					<span class="num">02</span>
					<div class="body">
						<h3>Foundation</h3>
						<p>Engineered footings, waterproofed walls and radiant-ready slabs, placed and inspected in weeks and built to hold for generations. It's the least photographed stage and the one we're strictest about.</p>
					</div>
				</li>
				<li class="hwb-step">
					<span class="num">03</span>
					<div class="body">
						<h3>Framing</h3>
						<p>Every wall is stick-built on-site by our own framing crew with no factory panels. This is when the drawings become rooms and when you'll walk the house with us weekly to feel each space before it's closed in.</p>
					</div>
				</li>
				<li class="hwb-step">
					<span class="num">04</span>
					<div class="body">
						<h3>The Finish</h3>
						<p>Stone, timber, plaster and brass, installed by master trades who have been with us for years. The last months move slowly on purpose. Then comes a thorough handover, a full home manual and a warranty team that still answers.</p>
					</div>
				</li>
			</ol>
		</div>
	</section>

	<!-- 5. CTA -->
	<section class="hm-cta">
		<div class="hm-cta-in">
			<span class="eyebrow">Next</span>
			<h2>Ready to <em>begin</em>?</h2>
			<a class="sh-btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Start the conversation &rarr;</a>
		</div>
	</section>

</main>

<?php get_footer(); ?>
