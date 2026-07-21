<?php get_header(); ?>

<main id="main" class="page-plain">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="entry"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
