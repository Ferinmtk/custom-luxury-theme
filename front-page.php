<?php
/**
 * Front page — cinematic descent homepage.
 * Hero: approved "tester" design (white gallery mat + scroll-scrubbed descent).
 * Portfolio: "Homes by style" pinned horizontal scrub (ported from prototype).
 */
get_header();
?>

    <main id="main">

        <!-- ============ INTRO (above the film) ============ -->
        <section class="intro">
            <div class="intro-in">
                <span class="eyebrow">Custom Luxury Homes</span>
                <h1>Every Tester home begins <em>above</em> the land it will belong to.</h1>
            </div>
        </section>

        <!-- ============ DESCENT HERO ============ -->
        <section class="descent" id="descent">
            <div class="d-pin">
                <div class="d-mat" id="mat">
                    <video
                            id="vid"
                            muted
                            playsinline
                            preload="auto"
                            src="<?php echo lh_asset('video/descent.mp4'); ?>"
                            data-mobile-src="<?php echo lh_asset('video/descent-mobile.mp4'); ?>"
                            poster="<?php echo lh_asset('img/poster.jpg'); ?>"
                    ></video>
                    <div class="d-grade" aria-hidden="true"></div>

                    <div class="d-copy" id="c1">
                        <span class="eyebrow">Phase I &middot; The Land</span>
                        <h2>It begins <em>above</em> your land.</h2>
                        <p>Every Tester home starts with the site itself: the slope, the trees, the light.</p>
                    </div>

                    <div class="d-copy" id="c2">
                        <span class="eyebrow">Phase II &middot; The Home</span>
                        <h2>Shaped to <em>meet</em> it.</h2>
                        <p>Stick-built on-site, one home at a time.</p>
                    </div>

                    <div class="d-copy" id="c3">
                        <span class="eyebrow">Phase III &middot; The Life</span>
                        <h2>Built for the way <em>you live</em>.</h2>
                        <p>Step inside. This is where the drawings end and your life begins.</p>
                    </div>
                </div>

                <div class="alt" aria-hidden="true">
                    <div class="alt-read"><span id="altNum">400</span><span>ft</span></div>
                    <div class="alt-rail"><i id="altDot"></i></div>
                    <div class="alt-label">Descent</div>
                </div>

                <div class="d-cue" id="dCue"
                     aria-hidden="true"><?php esc_html_e('Scroll to descend', 'luxury-homes'); ?><i></i></div>
            </div>
        </section>

        <!-- ============ PHILOSOPHY ============ -->
        <?php
        $ph_index = trim((string)lh_field('philosophy_index', ''));

        /*
         * ------------------------------------------------------------------
         * TEMPORARY PLACEHOLDER IMAGE — DO NOT DEPLOY
         * ------------------------------------------------------------------
         * assets/img/philosophy-PLACEHOLDER.jpg is an unlicensed, watermarked
         * stock comp used for local layout work only. It also depicts a
         * traditional cottage, which does not represent this builder's work.
         *
         * Before going live, either:
         *   1. Set the ACF field "philosophy_image", or
         *   2. Replace the file with a licensed photograph and update the
         *      fallback filename below.
         * Then delete philosophy-PLACEHOLDER.jpg and .png from assets/img/.
         * ------------------------------------------------------------------
         */
        $ph_img = lh_field_image('philosophy_image', 'img/philosophy-PLACEHOLDER.jpg', 'large', array(
                'alt'      => lh_field('philosophy_image_alt', __('A family looking toward their finished home', 'luxury-homes')),
                'loading'  => 'lazy',
                'decoding' => 'async',
        ));
        ?>
        <section class="philosophy" id="philosophy" data-reveal>
            <div class="philosophy__in">

                <div class="philosophy__type">
                    <h2 class="philosophy__display">
                        <span class="ph-line"><?php echo esc_html(lh_field('philosophy_line_1', __('We design', 'luxury-homes'))); ?></span>
                        <span class="ph-line ph-line--hollow"><?php echo esc_html(lh_field('philosophy_line_2', __('& build', 'luxury-homes'))); ?></span>
                        <span class="ph-line ph-line--ital"><?php echo esc_html(lh_field('philosophy_line_3', __('one of a kind', 'luxury-homes'))); ?></span>
                        <span class="ph-line ph-line--hollow"><?php echo esc_html(lh_field('philosophy_line_4', __('Residences', 'luxury-homes'))); ?></span>
                    </h2>
                </div>

                <?php if ($ph_img) : ?>
                    <figure class="philosophy__figure">
                        <?php if ('' !== $ph_index) : ?>
                            <span class="philosophy__index"><?php echo esc_html($ph_index); ?></span>
                        <?php endif; ?>
                        <div class="philosophy__media"><?php echo $ph_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                    </figure>
                <?php endif; ?>

            </div>

            <div class="philosophy__foot">
                <p class="philosophy__note">
                    <?php echo esc_html(lh_field('philosophy_note', 'From the first sketch to the last brass detail, for families who expect their home to be as considered as everything else they own.')); ?>
                </p>
            </div>
        </section>

        <!-- ============ HOMES BY STYLE (pinned horizontal scrub) ============ -->
        <?php
        $scrub_homes = get_posts(array(
                'post_type'      => 'home',
                'posts_per_page' => 6,
                'orderby'        => array('menu_order' => 'ASC', 'date' => 'DESC'),
        ));
        if ($scrub_homes) :
            ?>
            <section class="scrub" id="portfolio">
                <div class="scrub-stage">
                    <div class="scrub-head">
                        <h2 class="reveal d1"><?php echo esc_html(lh_field('portfolio_heading', 'Homes by style')); ?></h2>
                    </div>
                    <div class="scrub-track" id="scrubTrack">
                        <?php
                        foreach ($scrub_homes as $lh_home) :
                            $m = lh_home_meta($lh_home->ID);
                            $bits = array();
                            if ($m['location']) {
                                $bits[] = $m['location'];
                            }
                            if ($m['beds']) {
                                $bits[] = sprintf(_n('%s bed', '%s beds', (int)$m['beds'], 'luxury-homes'), number_format_i18n($m['beds']));
                            }
                            if ($m['sqft']) {
                                /* translators: %s: square footage */
                                $bits[] = sprintf(__('%s sq ft', 'luxury-homes'), number_format_i18n($m['sqft']));
                            }
                            ?>
                            <a href="<?php echo esc_url(get_permalink($lh_home)); ?>" class="scrub-card">
                                <div class="sc-frame">
                                    <?php if ($m['style']) : ?>
                                        <span class="sc-tag"><?php echo esc_html($m['style']); ?></span>
                                    <?php endif; ?>
                                    <?php
                                    echo lh_home_image($lh_home->ID, 'large', array(
                                            'alt'      => get_the_title($lh_home),
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                    ));
                                    ?>
                                    <div class="sc-info">
                                        <div class="sc-name"><?php echo esc_html(get_the_title($lh_home)); ?></div>
                                        <?php if ($bits) : ?>
                                            <div class="sc-meta"><?php echo esc_html(implode(' · ', $bits)); ?></div>
                                        <?php endif; ?>
                                        <span class="sc-btn"><?php esc_html_e('Tour this home', 'luxury-homes'); ?> &rarr;</span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <a href="<?php echo esc_url(home_url('/our-homes/')); ?>" class="scrub-card scrub-card--more">
                            <div class="sc-frame sc-more">
                                <span class="sc-more-label"><?php esc_html_e('See more', 'luxury-homes'); ?></span>
                                <span class="sc-more-arrow" aria-hidden="true">&rarr;</span>
                            </div>
                        </a>
                    </div>
                    <div class="scrub-progress"><i></i></div>
                </div>
            </section>
        <?php endif; ?>

        <!-- ============ HOW WE WORK — two threads ============ -->
        <?php
        /*
         * ------------------------------------------------------------------
         * PLACEHOLDER TESTIMONIAL — DO NOT DEPLOY
         * The quote and attribution below are FABRICATED. Replace via the ACF
         * fields "hww_quote" / "hww_quote_attr" with a real, consenting
         * client's words, or clear them to remove the block entirely.
         * ------------------------------------------------------------------
         */
        ?>
        <section class="hww" id="how-we-work" data-nav="dark">
            <div class="hww-eyebrow">
                <div class="hww-eyebrow-rule" aria-hidden="true"></div>
                <div class="hww-eyebrow-text"><?php echo esc_html(lh_field('hww_eyebrow', __('How we work', 'luxury-homes'))); ?></div>
                <div class="hww-eyebrow-rule" aria-hidden="true"></div>
            </div>

            <h2 class="hww-headline"><?php echo wp_kses_post(lh_field('hww_headline', __('Eighteen months, <em>walked together.</em>', 'luxury-homes'))); ?></h2>

            <svg class="hww-svg" viewBox="0 0 1400 470" fill="none" role="img"
                 aria-label="<?php esc_attr_e('Two threads — you and your builder — travel from week one across the land, become the outline of your home, and arrive at the keys together.', 'luxury-homes'); ?>">
                <defs>
                    <linearGradient id="hww-gold" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0" stop-color="#A98F4F"></stop>
                        <stop offset="0.5" stop-color="#C2A55E"></stop>
                        <stop offset="1" stop-color="#E3D294"></stop>
                    </linearGradient>
                    <filter id="hww-glow" x="-200%" y="-200%" width="500%" height="500%">
                        <feGaussianBlur stdDeviation="7"></feGaussianBlur>
                    </filter>
                </defs>

                <path d="M 40 404 C 180 372, 300 420, 460 396 C 640 370, 900 416, 1120 398 C 1220 391, 1300 402, 1360 398"
                      stroke="#C2A55E" stroke-width="0.75" opacity="0.12"></path>
                <path d="M 40 432 C 240 414, 420 444, 640 428 C 880 411, 1120 442, 1360 426" stroke="#C2A55E"
                      stroke-width="0.75" opacity="0.07"></path>

                <path class="anim hww-thread"
                      d="M 118 366 C 175 338, 225 396, 320 368 C 420 341, 490 392, 590 374 C 680 358, 750 380, 850 376 L 850 258 L 1000 196 L 1060 220.8 L 1060 190 L 1090 190 L 1090 233.2 L 1150 258 L 1150 376 L 1360 376"
                      stroke="url(#hww-gold)" stroke-width="1.25" stroke-linejoin="round"></path>
                <path class="anim hww-thread"
                      d="M 118 382 C 175 354, 225 384, 320 376 C 420 352, 490 398, 590 366 C 680 366, 750 370, 850 383 L 857 383 L 857 265 L 1000 206 L 1057 229 L 1057 197 L 1083 197 L 1083 237 L 1143 262 L 1143 383 L 1360 376"
                      stroke="#E9EDE0" stroke-width="0.9" stroke-linejoin="round" opacity="0.75"></path>

                <path class="anim fade d1" d="M 34 378 L 122 378" stroke="#C2A55E" stroke-width="0.75"
                      opacity="0.35"></path>
                <g class="anim fade d1" fill="#C2A55E">
                    <circle cx="57.5" cy="313" r="6.3"></circle>
                    <path d="M 54 324 C 52 326, 51 332, 51 340 C 51 349, 52 355, 52 359 L 48 375 L 52 376 L 57 361 L 61 376 L 65 375 L 62 358 C 63 352, 63 344, 62 338 C 67 341, 73 342, 79 341 L 79 337.5 C 73 338, 68 336, 64 331 C 62 327, 58 325, 54 324 Z"></path>
                </g>
                <g class="anim fade d1" fill="#E9EDE0" opacity="0.92">
                    <circle cx="104.5" cy="311.5" r="6.3"></circle>
                    <path d="M 108 322.5 C 110 324.5, 111 330.5, 111 338.5 C 111 347.5, 110 353.5, 110 357.5 L 114 374 L 110 375 L 105 360 L 101 375 L 97 374 L 100 357 C 99 351, 99 343, 100 337 C 95 340, 89 341, 84 340 L 84 336.5 C 90 337, 94 335, 98 330 C 100 326, 104 324, 108 322.5 Z"></path>
                </g>
                <g class="anim fade d2" stroke="#C2A55E" stroke-width="1" opacity="0.65" fill="none">
                    <path d="M 74 299 C 78 295, 84 295, 88 299"></path>
                    <path d="M 77 305 C 80 302, 82 302, 85 305"></path>
                </g>

                <circle class="anim fade d6" cx="1360" cy="376" r="4.5" fill="none" stroke="#E3D294"
                        stroke-width="1"></circle>
                <circle class="anim fade d6" cx="1360" cy="376" r="1.8" fill="#E3D294"></circle>

                <line class="anim fade d3" x1="300" y1="390" x2="300" y2="402" stroke="#C2A55E" stroke-width="0.75"
                      opacity="0.6"></line>
                <line class="anim fade d4" x1="640" y1="378" x2="640" y2="402" stroke="#C2A55E" stroke-width="0.75"
                      opacity="0.6"></line>
                <line class="anim fade d5" x1="1000" y1="390" x2="1000" y2="402" stroke="#C2A55E" stroke-width="0.75"
                      opacity="0.6"></line>
                <text class="anim fade d3 hww-caps" x="300" y="424" text-anchor="middle"
                      font-size="9.5"><?php echo esc_html(lh_field('hww_ms_1', __('DESIGNED, TOGETHER', 'luxury-homes'))); ?></text>
                <text class="anim fade d4 hww-caps" x="640" y="424" text-anchor="middle"
                      font-size="9.5"><?php echo esc_html(lh_field('hww_ms_2', __('BROKE GROUND', 'luxury-homes'))); ?></text>
                <text class="anim fade d5 hww-caps" x="1000" y="424" text-anchor="middle"
                      font-size="9.5"><?php echo esc_html(lh_field('hww_ms_3', __('WALKED EVERY ROOM', 'luxury-homes'))); ?></text>

                <text class="anim fade d1 hww-caps-gold" x="40" y="460" text-anchor="start"
                      font-size="10"><?php echo esc_html(lh_field('hww_start', __('WEEK ONE — YOU MEET YOUR BUILDER', 'luxury-homes'))); ?></text>
                <text class="anim fade d6 hww-caps-gold" x="1360" y="460" text-anchor="end"
                      font-size="10"><?php echo esc_html(lh_field('hww_end', __('MONTH EIGHTEEN — KEYS, HAND TO HAND', 'luxury-homes'))); ?></text>
                <text class="anim fade d6 hww-note" x="1000" y="164" text-anchor="middle"
                      font-size="21"><?php echo esc_html(lh_field('hww_home_note', __('your home', 'luxury-homes'))); ?></text>
                <text class="anim fade d4 hww-note-muted" x="470" y="318" text-anchor="middle"
                      font-size="18"><?php echo esc_html(lh_field('hww_crossing', __('every crossing, a conversation', 'luxury-homes'))); ?></text>

                <circle class="hww-comet" r="8" fill="#E3D294" opacity="0.4" filter="url(#hww-glow)">
                    <animateMotion id="hww-m1" begin="indefinite" dur="10s" repeatCount="1" fill="freeze"
                                   calcMode="linear"
                                   path="M 118 366 C 175 338, 225 396, 320 368 C 420 341, 490 392, 590 374 C 680 358, 750 380, 850 376 L 850 258 L 1000 196 L 1060 220.8 L 1060 190 L 1090 190 L 1090 233.2 L 1150 258 L 1150 376 L 1360 376"></animateMotion>
                </circle>
                <circle class="hww-comet" r="2.5" fill="#C2A55E">
                    <animateMotion id="hww-m2" begin="indefinite" dur="10s" repeatCount="1" fill="freeze"
                                   calcMode="linear"
                                   path="M 118 366 C 175 338, 225 396, 320 368 C 420 341, 490 392, 590 374 C 680 358, 750 380, 850 376 L 850 258 L 1000 196 L 1060 220.8 L 1060 190 L 1090 190 L 1090 233.2 L 1150 258 L 1150 376 L 1360 376"></animateMotion>
                </circle>
                <circle class="hww-comet" r="2.2" fill="#E9EDE0">
                    <animateMotion id="hww-m3" begin="indefinite" dur="10s" repeatCount="1" fill="freeze"
                                   calcMode="linear"
                                   path="M 118 382 C 175 354, 225 384, 320 376 C 420 352, 490 398, 590 366 C 680 366, 750 370, 850 383 L 857 383 L 857 265 L 1000 206 L 1057 229 L 1057 197 L 1083 197 L 1083 237 L 1143 262 L 1143 383 L 1360 376"></animateMotion>
                </circle>
            </svg>

            <!-- Below ~760px the drawing's 9.5px captions render at ~2px. Show the
                 same milestones as readable text instead. -->
            <ol class="hww-steps">
                <li>
                    <span><?php echo esc_html(lh_field('hww_start', __('WEEK ONE — YOU MEET YOUR BUILDER', 'luxury-homes'))); ?></span>
                </li>
                <li><span><?php echo esc_html(lh_field('hww_ms_1', __('DESIGNED, TOGETHER', 'luxury-homes'))); ?></span>
                </li>
                <li><span><?php echo esc_html(lh_field('hww_ms_2', __('BROKE GROUND', 'luxury-homes'))); ?></span></li>
                <li><span><?php echo esc_html(lh_field('hww_ms_3', __('WALKED EVERY ROOM', 'luxury-homes'))); ?></span>
                </li>
                <li>
                    <span><?php echo esc_html(lh_field('hww_end', __('MONTH EIGHTEEN — KEYS, HAND TO HAND', 'luxury-homes'))); ?></span>
                </li>
            </ol>

            <?php
            $hq = trim((string)lh_field('hww_quote', 'The person who walked our land in the first week was the person who handed us the keys.'));
            $ha = trim((string)lh_field('hww_quote_attr', 'The Halvorsens · Meadow House, Park City'));
            if ('' !== $hq) :
                ?>
                <figure class="hww-quote-block">
                    <blockquote class="hww-quote"><p><?php echo esc_html($hq); ?></p></blockquote>
                    <?php if ('' !== $ha) : ?>
                        <figcaption class="hww-attrib"><?php echo esc_html($ha); ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endif; ?>

            <a class="hww-cta"
               href="<?php echo esc_url(home_url('/how-we-build/')); ?>"><?php esc_html_e('See how we build', 'luxury-homes'); ?>
                <span aria-hidden="true">&rarr;</span></a>
        </section>

        <!-- ============ START WHERE YOU ARE (flip cards) ============ -->
        <?php
        $lh_tel_display = trim((string)lh_field('phone_display', '(303) 555-0100'));
        $lh_tel_href    = 'tel:' . preg_replace('/[^0-9+]/', '', lh_field('phone', '+13035550100'));

        // Front title + back message per option. Back copy is editable via ACF later.
        $lh_ways = array(
                array('icon' => 'map-pin', 'tone' => '#3a6033', 'title' => 'You own the land.', 'back' => 'Send us the parcel. We’ll walk it and tell you what it wants to become.'),
                array('icon' => 'search', 'tone' => '#347391', 'title' => 'You’re still looking for a site.', 'back' => 'Bring us three. We’ll tell you which one builds, and what it costs before you buy.'),
                array('icon' => 'activity', 'tone' => '#b3812a', 'title' => 'You have drawings already.', 'back' => 'Send the plans. We’ll price them honestly and flag what we’d change.'),
                array('icon' => 'compass', 'tone' => '#c46c36', 'title' => 'You don’t know where to start.', 'back' => 'Start with a walk. No plans, no pressure — just the land and a conversation.'),
                array('icon' => 'shield', 'tone' => '#b03e2c', 'title' => 'You’ve been burned before.', 'back' => 'Fair. Ask us anything, call our last three clients, then decide.'),
                array('icon' => 'eye', 'tone' => '#93794b', 'title' => 'You’re just looking.', 'back' => 'Look. Nothing here needs your email.', 'gold' => true),
        );
        ?>
        <section class="ways" id="contact">
            <div class="ways-in">
                <header class="ways-head" data-reveal>
                    <h2 class="ways-title"><?php esc_html_e('Start where you are', 'luxury-homes'); ?></h2>
                    <p class="ways-sub"><?php esc_html_e('Six ways in.', 'luxury-homes'); ?></p>
                </header>

                <div class="ways-grid" data-reveal>
                    <?php foreach ($lh_ways as $lh_i => $lh_w) :
                        $lh_gold = empty($lh_w['gold']) ? '' : ' ways-card--gold';
                        ?>
                        <a class="ways-card<?php echo esc_attr($lh_gold); ?>"
                           href="<?php echo esc_url(home_url('/contact/')); ?>"
                           style="--tone: <?php echo esc_attr($lh_w['tone']); ?>;">
						<span class="ways-card__inner">
							<span class="ways-card__front">
								<span class="ways-card__icon"><?php echo lh_option_icon($lh_w['icon']); // phpcs:ignore
                                    ?></span>
								<span class="ways-card__title"><?php echo esc_html($lh_w['title']); ?></span>
								<span class="ways-card__hint" aria-hidden="true">&rarr;</span>
							</span>
							<span class="ways-card__back">
								<span class="ways-card__msg"><?php echo esc_html($lh_w['back']); ?></span>
								<span class="ways-card__arrow" aria-hidden="true">&rarr;</span>
							</span>
						</span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="ways-foot" data-reveal>
                    <p class="ways-foot__line"><?php esc_html_e('Whichever one you are, the first conversation is the same.', 'luxury-homes'); ?></p>
                    <a class="ways-btn" href="<?php echo esc_url(home_url('/contact/')); ?>"><span
                                class="ways-btn__label"><?php esc_html_e('Start the conversation', 'luxury-homes'); ?></span><span
                                class="ways-btn__arrow" aria-hidden="true">&rarr;</span></a>
                    <p class="ways-foot__call"><?php
                        printf(
                        /* translators: %s: telephone link */
                                esc_html__('Or call us directly · %s', 'luxury-homes'),
                                '<a href="' . esc_url($lh_tel_href) . '">' . esc_html($lh_tel_display) . '</a>'
                        );
                        ?></p>
                </div>
            </div>
        </section>

    </main>

<?php get_footer(); ?>