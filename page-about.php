<?php
/**
 * Template Name: About
 * Auto-applies to the page with slug "about".
 *
 * The page is the team. An ADU is four separate worries for a homeowner —
 * will it be run properly, will it be built well, will it fit my lot, can I
 * pay for it — and there is a named person for each one. So the roster is not
 * a courtesy block at the bottom of an About page; it is the whole argument.
 *
 * Portraits: drop four JPGs into assets/img/team/ named as below, or set them
 * per-person via the ACF repeater "team" (sub-fields: name, title, line, photo).
 * Sources are inconsistent (different backgrounds and colour temperatures), so
 * the grid renders them monochrome and returns colour on hover — that is what
 * makes four unrelated headshots read as one set.
 */
get_header();

$ab_team = lh_field('team', array(
        array(
                'name' => 'Marcus Flinders',
                'title' => 'Builder Prime',
                'line' => 'President. He is the one accountable for your build, from the first site visit to the final inspection.',
                'photo' => 'img/team/marcus-flinders.jpg',
        ),
        array(
                'name' => 'Spencer Edwards',
                'title' => 'Master Builder',
                'line' => 'Twenty-two years a firefighter before he built his first home in 2001. Father of four. Builds like it is his own.',
                'photo' => 'img/team/spencer-edwards.jpg',
        ),
        array(
                'name' => 'Patty Smith',
                'title' => 'Designer Extraordinaire',
                'line' => 'Designing since 1998, from single garages to 15,000 square feet. She makes 650 square feet feel like plenty.',
                'photo' => 'img/team/patty-smith.jpg',
        ),
        array(
                'name' => 'Charles Edington',
                'title' => 'The ADU Financing Guy',
                'line' => 'Licensed loan officer, NMLS #272063. He finds the financing that actually fits your situation.',
                'photo' => 'img/team/charles-edington.jpg',
        ),
));

/** Metros. Note: site copy currently disagrees on this list — confirm before launch. */
$ab_metros = lh_field('metros', array('Denver Metro', 'Colorado Springs', 'Chicagoland', 'Salt Lake Metro'));
?>

    <main id="main" class="homes-page about-page">

        <!-- 1. Thesis -->
        <?php while (have_posts()) : the_post(); ?>
            <section class="ab-hero">
                <div class="ab-hero-in">
                    <span class="eyebrow"><?php echo esc_html(lh_field('about_eyebrow', 'About')); ?></span>
                    <h1>Four people, and the four things you&rsquo;re <em>worried</em> about.</h1>
                    <?php if (trim(get_the_content())) : ?>
                        <div class="ab-lede"><?php the_content(); ?></div>
                    <?php else : ?>
                        <div class="ab-lede">
                            <p>An ADU is a design, a permit, a build and a loan. We put a person in charge of each one,
                                and you get all four of their numbers.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endwhile; ?>

        <!-- 2. The team — the page -->
        <section class="ab-team" aria-labelledby="ab-team-title">
            <div class="ab-team-in">
                <div class="ab-team-head">
                    <h2 id="ab-team-title">The people you&rsquo;ll actually deal&nbsp;with</h2>
                    <p>Not a call centre. These four are on your project from the first estimate to the day you get the
                        keys.</p>
                </div>

                <ul class="ab-roster">
                    <?php foreach ($ab_team as $ab_i => $ab_p) :
                        $ab_src = '';
                        if (!empty($ab_p['photo'])) {
                            $ab_rel = ltrim((string)$ab_p['photo'], '/');
                            $ab_path = get_template_directory() . '/assets/' . $ab_rel;
                            // Absolute URLs (e.g. from ACF) pass straight through.
                            if (preg_match('#^https?://#', $ab_rel)) {
                                $ab_src = $ab_rel;
                            } elseif (file_exists($ab_path)) {
                                $ab_src = lh_asset($ab_rel);
                            }
                        }
                        ?>
                        <li class="ab-member" data-reveal style="--i:<?php echo (int)$ab_i; ?>">
                            <figure>
                                <?php if ($ab_src) : ?>
                                    <img
                                            src="<?php echo esc_url($ab_src); ?>"
                                            alt="<?php echo esc_attr($ab_p['name']); ?>"
                                            loading="lazy"
                                            decoding="async"
                                            width="600" height="750">
                                <?php else : ?>
                                    <span class="ab-ph" aria-hidden="true"></span>
                                <?php endif; ?>
                                <figcaption>
                                    <b class="ab-name"><?php echo esc_html($ab_p['name']); ?></b>
                                    <span class="ab-title"><?php echo esc_html($ab_p['title']); ?></span>
                                    <p class="ab-line"><?php echo esc_html($ab_p['line']); ?></p>
                                </figcaption>
                            </figure>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <!-- 3. Reach + record -->
        <section class="ab-reach">
            <div class="ab-reach-in">
                <div class="ab-reach-where">
                    <span class="eyebrow"><?php esc_html_e('Where we build', 'luxury-homes'); ?></span>
                    <ul>
                        <?php foreach ($ab_metros as $ab_m) : ?>
                            <li><?php echo esc_html($ab_m); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="ab-reach-facts">
                    <div><b>25<i>+</i></b><span><?php esc_html_e('Years building', 'luxury-homes'); ?></span></div>
                    <div><b>3</b><span><?php esc_html_e('Models, plus custom', 'luxury-homes'); ?></span></div>
                    <div>
                        <b><?php esc_html_e('Turnkey', 'luxury-homes'); ?></b><span><?php esc_html_e('Design, permits, build', 'luxury-homes'); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. CTA -->
        <section class="hm-cta">
            <div class="hm-cta-in">
                <span class="eyebrow"><?php esc_html_e('Next', 'luxury-homes'); ?></span>
                <h2>Find out what fits in your <em>backyard</em>.</h2>
                <a class="sh-btn"
                   href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Get a free estimate', 'luxury-homes'); ?>
                    &rarr;</a>
            </div>
        </section>

    </main>

<?php get_footer(); ?>