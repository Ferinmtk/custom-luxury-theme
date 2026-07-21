<?php
/**
 * Template Name: Our Homes
 * Auto-applies to the page with slug "our-homes"; also assignable via Template.
 */
get_header();

$all_homes = function_exists( 'lh_get_homes' ) ? lh_get_homes() : array();
list( $featured, $rest ) = function_exists( 'lh_split_featured' )
        ? lh_split_featured( $all_homes )
        : array( null, $all_homes );

// Styles present in the grid, in canonical order.
$present = array();
if ( function_exists( 'lh_normalize_style' ) ) {
    foreach ( $rest as $h ) {
        $s = lh_normalize_style( get_post_meta( $h->ID, '_home_style', true ) );
        if ( $s ) { $present[ $s ] = true; }
    }
}
$filter_styles = function_exists( 'lh_home_styles' )
        ? array_values( array_filter( lh_home_styles(), function ( $s ) use ( $present ) {
            return isset( $present[ $s ] );
        } ) )
        : array();

// Portfolio structured data (schema.org ItemList) — helps search engines
// surface the homes as a rich list.
$ld_homes = $featured ? array_merge( array( $featured ), $rest ) : $rest;
$ld_items = array();
foreach ( $ld_homes as $ld_i => $ld_home ) {
    $ld_item = array(
            '@type'    => 'ListItem',
            'position' => $ld_i + 1,
            'url'      => get_permalink( $ld_home ),
            'name'     => get_the_title( $ld_home ),
    );
    if ( function_exists( 'lh_home_image_url' ) ) {
        $ld_img = lh_home_image_url( $ld_home->ID, 'full' );
        if ( $ld_img ) { $ld_item['image'] = $ld_img; }
    }
    $ld_items[] = $ld_item;
}
if ( $ld_items ) {
    echo '<script type="application/ld+json">' . wp_json_encode( array(
                    '@context'        => 'https://schema.org',
                    '@type'           => 'ItemList',
                    'itemListElement' => $ld_items,
            ) ) . '</script>' . "\n";
}
?>

    <main id="main" class="homes-page our-homes-page">

        <div class="hm-archbg" aria-hidden="true"><img class="hm-archbg-img" src="<?php echo lh_asset( 'img/blueprint.svg' ); ?>" alt="" loading="lazy" decoding="async"></div>


        <!-- 1. Intro -->
        <?php while ( have_posts() ) : the_post(); ?>
            <section class="homes-hero">
                <div class="homes-hero-in">
                    <h1 data-reveal><?php the_title(); ?></h1>
                    <?php if ( trim( get_the_content() ) ) : ?>
                        <div class="homes-intro" data-reveal><?php the_content(); ?></div>
                    <?php else : ?>
                        <div class="homes-intro" data-reveal><p>Every home here was drawn from its land and stick-built on-site. One family, one site, one home at a time.</p></div>
                    <?php endif; ?>
                    <?php if ( $all_homes ) : ?>
                        <p class="homes-count" data-reveal><?php printf(
                                    esc_html( _n( '%s home, built one at a time.', '%s homes, built one at a time.', count( $all_homes ), 'luxury-homes' ) ),
                                    esc_html( number_format_i18n( count( $all_homes ) ) )
                            ); ?></p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endwhile; ?>

        <!-- 2. Featured home -->
        <?php if ( $featured ) :
            $fm = lh_home_meta( $featured->ID );
            $fspec = array();
            if ( $fm['beds'] ) { $fspec[] = $fm['beds'] . ' beds'; }
            if ( $fm['baths'] ) { $fspec[] = $fm['baths'] . ' baths'; }
            if ( $fm['sqft'] ) { $fspec[] = number_format_i18n( (float) $fm['sqft'] ) . ' sq ft'; }
            if ( $fm['year'] ) { $fspec[] = 'Completed ' . $fm['year']; }
            ?>
            <section class="hm-feature-wrap">
                <a class="hm-feature" href="<?php echo esc_url( get_permalink( $featured ) ); ?>" data-reveal>
                    <div class="frame">
                        <?php
                        $feat_img = function_exists( 'lh_home_image' ) ? lh_home_image( $featured->ID, 'full', array(
                                'alt'           => get_the_title( $featured ),
                                'loading'       => 'eager',
                                'fetchpriority' => 'high',
                                'decoding'      => 'async',
                                'sizes'         => '(min-width: 1240px) 1240px, 100vw',
                        ) ) : '';
                        echo '' !== $feat_img ? $feat_img : '<span class="home-ph" aria-hidden="true"></span>';
                        ?>
                    </div>
                    <div class="hm-feature-copy">
                        <span class="eyebrow">Featured<?php echo $fm['location'] ? ' · ' . esc_html( $fm['location'] ) : ''; ?></span>
                        <h2><?php echo esc_html( get_the_title( $featured ) ); ?></h2>
                        <?php if ( $fspec ) : ?>
                            <div class="spec"><?php echo esc_html( implode( ' · ', $fspec ) ); ?></div>
                        <?php endif; ?>
                        <span class="view">View gallery &rarr;</span>
                    </div>
                </a>
            </section>
        <?php endif; ?>

        <!-- 3 + 4. Style filters + grid -->
        <section class="homes-grid-wrap" aria-labelledby="homesGridHeading">
            <?php if ( $rest ) : ?>
                <header class="homes-grid-head" data-reveal>
                    <span class="eyebrow">Portfolio</span>
                    <h2 id="homesGridHeading">Selected <em>homes</em></h2>
                </header>
                <?php if ( $filter_styles ) : ?>
                    <div class="hm-filters" id="hmFilters" role="group" aria-label="Filter homes by style" data-reveal>
                        <button type="button" class="hm-filter is-active" data-filter="*" aria-pressed="true">All</button>
                        <?php foreach ( $filter_styles as $s ) : ?>
                            <button type="button" class="hm-filter" data-filter="<?php echo esc_attr( $s ); ?>" aria-pressed="false"><?php echo esc_html( $s ); ?></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <p id="hmStatus" class="screen-reader-text" role="status" aria-live="polite"></p>

                <div class="homes-grid" id="homesGrid">
                    <?php foreach ( $rest as $post ) : setup_postdata( $post );
                        $meta = lh_home_meta( $post->ID ); ?>
                        <a class="home-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>" data-style="<?php echo esc_attr( $meta['style'] ); ?>" data-reveal>
                            <div class="frame">
                                <?php if ( $meta['style'] ) : ?>
                                    <span class="home-tag"><?php echo esc_html( $meta['style'] ); ?></span>
                                <?php endif; ?>
                                <?php
                                $card_img = function_exists( 'lh_home_image' ) ? lh_home_image( $post->ID, 'large', array(
                                        'alt'      => get_the_title( $post ),
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                        'sizes'    => '(min-width: 980px) 33vw, (min-width: 640px) 50vw, 100vw',
                                ) ) : '';
                                echo '' !== $card_img ? $card_img : '<span class="home-ph" aria-hidden="true"></span>';
                                ?>
                            </div>
                            <div class="meta">
                                <?php if ( $meta['location'] ) : ?>
                                    <div class="loc"><?php echo esc_html( $meta['location'] ); ?></div>
                                <?php endif; ?>
                                <h3><?php echo esc_html( get_the_title( $post ) ); ?></h3>
                                <?php if ( $meta['year'] ) : ?>
                                    <div class="spec"><?php echo esc_html( $meta['year'] ); ?></div>
                                <?php endif; ?>
                                <span class="view">View gallery &rarr;</span>
                            </div>
                        </a>
                    <?php endforeach; wp_reset_postdata(); ?>
                </div>

                <div class="hm-more-wrap">
                    <button type="button" class="hm-more" id="hmMore" hidden>Load more</button>
                </div>
            <?php else : ?>
                <p class="homes-empty">Homes are coming soon. In the meantime, <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">tell us about yours</a>.</p>
            <?php endif; ?>
        </section>

        <!-- 5. Value-prop strip -->
        <section class="hm-values">
            <div class="hm-values-in">
                <header class="hm-values-head" data-reveal>
                    <span class="eyebrow">Why it&rsquo;s different</span>
                    <h2>Built the <em>hard</em> way, on purpose.</h2>
                </header>
                <div class="hm-values-grid">
                    <div class="hm-value" data-reveal>
                        <span class="hm-value-num">01</span>
                        <h3>On-site</h3>
                        <p>Every home is stick-built on its own land with no factory panels and no shortcuts.</p>
                    </div>
                    <div class="hm-value" data-reveal>
                        <span class="hm-value-num">02</span>
                        <h3>One at a time</h3>
                        <p>We take on a limited number of homes each year, each with a dedicated team.</p>
                    </div>
                    <div class="hm-value" data-reveal>
                        <span class="hm-value-num">03</span>
                        <h3>Utah craft</h3>
                        <p>Master trades from the Wasatch, working stone, timber and glass by hand.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Closing CTA -->
        <section class="hm-cta">
            <div class="hm-cta-in">
                <figure class="hm-cta-media" data-reveal>
                    <img src="<?php echo lh_asset( 'img/cta-planning.jpg' ); ?>" alt="A modern custom home rendered above its architectural plans" loading="lazy" decoding="async" width="1024" height="682">
                </figure>
                <div class="hm-cta-copy" data-reveal>
                    <span class="eyebrow">Your turn</span>
                    <h2>Let&rsquo;s build <em>yours</em>.</h2>
                    <a class="sh-btn hm-cta-btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><span>Start the conversation</span><i aria-hidden="true">&rarr;</i></a>
                </div>
            </div>
        </section>

    </main>

<?php get_footer(); ?>