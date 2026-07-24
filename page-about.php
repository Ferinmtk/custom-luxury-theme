<?php
/**
 * Template Name: About
 * Auto-applies to the page with slug "about".
 *
 * Structure (all content is ACF-editable; defaults below are placeholders):
 *   hero (headline + office band) -> story (+ figures) -> team rows ->
 *   the plate -> awards -> one-team + cost -> warranty -> credentials -> quote -> CTA
 *
 * The page's argument: a custom home is four things a buyer is trusting to
 * other people, and there is a named person for each. The team is therefore
 * the spine, not a footer courtesy.
 *
 * Portraits live in assets/img/team/ as a fallback; wire real ones through the
 * ACF repeater "about_team" so the client can edit without code. All four were
 * cropped square on the face — a replacement must match or it will stand out.
 *
 * PLACEHOLDER DATA that must be replaced before launch is marked @placeholder.
 */

defined('ABSPATH') || exit;

get_header();

$company = function_exists('lh_company') ? lh_company() : 'Tester';

/** Team. @placeholder bios — real people, borrowed copy. */
$ab_team = lh_field('about_team', array(
        array('name' => 'Marcus Flinders', 'title' => 'Builder Prime', 'photo' => 'img/team/marcus-flinders.jpg',
                'bio' => 'Founder and President. Accountable for your house from the first walk of the land to the keys — and on your site every week in between. Still owns the first hammer he ever bought.'),
        array('name' => 'Spencer Edwards', 'title' => 'Master Builder', 'photo' => 'img/team/spencer-edwards.jpg',
                'bio' => 'Twenty-two years a firefighter before he built his first home in 2001. Father of four. He knows current code, current materials, and exactly where the expensive mistakes like to hide.'),
        array('name' => 'Patty Smith', 'title' => 'Designer Extraordinaire', 'photo' => 'img/team/patty-smith.jpg',
                'bio' => 'Designing since 1998, on houses from 1,500 to 15,000 square feet. She will ask how you make coffee before she draws the kitchen — and she means it.'),
        array('name' => 'Charles Edington', 'title' => 'The Money Guy', 'photo' => 'img/team/charles-edington.jpg',
                'bio' => 'Lending since 2002. Construction loans, draw schedules, allowances — and the person who will tell you the honest number before you fall for the wrong lot.'),
));

/** Story figures. @placeholder values. */
$ab_founded = (int)lh_field('about_founded', 2006);
$ab_figs = lh_field('about_figures', array(
        array('n' => '20', 'suffix' => '+', 'label' => 'Years building'),
        array('n' => '56', 'suffix' => '', 'label' => 'Homes completed'),
        array('n' => '2–4', 'suffix' => '', 'label' => 'Homes a year'),
));

/** Awards. @placeholder — must be real before launch. */
$ab_awards = lh_field('about_awards', array(
        array('year' => '2024', 'name' => 'MAME Award — Custom Home over 6,000 sq ft', 'body' => 'HBA of Metro Denver'),
        array('year' => '2023', 'name' => 'Best of Houzz — Service', 'body' => 'Fourth consecutive year'),
        array('year' => '2022', 'name' => 'Parade of Homes — People’s Choice', 'body' => 'Stonebrook Court'),
        array('year' => '2021', 'name' => 'MAME Award — Best Interior Design', 'body' => 'HBA of Metro Denver'),
        array('year' => '2019', 'name' => 'Best in American Living — Regional', 'body' => 'NAHB'),
));

/** Warranty schedule. */
$ab_warranty = lh_field('about_warranty', array(
        array('term' => '1', 'unit' => 'year', 'covers' => 'Workmanship, finishes and fit'),
        array('term' => '2', 'unit' => 'years', 'covers' => 'Mechanical, electrical, plumbing'),
        array('term' => '10', 'unit' => 'years', 'covers' => 'Structural'),
));

/** Credentials. @placeholder licence/bond/EIN — legal risk if published as-is. */
$ab_metros = lh_field('about_metros', array('Denver Metro', 'Boulder County', 'Castle Pines', 'Evergreen'));
$ab_reg = lh_field('about_registry', array(
        array('k' => 'Licence', 'v' => 'Denver GC-A 00000'),
        array('k' => 'Insured', 'v' => '$0,000,000 GL'),
        array('k' => 'Bonded', 'v' => 'Surety 000000'),
        array('k' => 'EIN', 'v' => '00-0000000'),
        array('k' => 'Est.', 'v' => (string)$ab_founded),
));
$ab_members = lh_field('about_memberships', 'Home Builders Association of Metro Denver · National Association of Home Builders · Houzz Pro');

/** Resolve a portrait to a URL: absolute passes through, theme-relative is checked. */
if (!function_exists('lh_about_photo')) {
    function lh_about_photo($value)
    {
        $value = ltrim((string)$value, '/');
        if ('' === $value) {
            return '';
        }
        if (preg_match('#^https?://#', $value)) {
            return $value;
        }
        return file_exists(get_template_directory() . '/assets/' . $value) ? lh_asset($value) : '';
    }
}
?>

    <main id="main" class="homes-page about-page">

        <!-- 1. Hero -->
        <?php while (have_posts()) : the_post(); ?>
            <section class="ab-hero">
                <div class="ab-hero-in">
                    <h1><?php echo wp_kses_post(lh_field('about_h1', 'Twenty years. Fifty-six houses. Mostly the <em>same hands</em>.')); ?></h1>
                    <?php if (trim(get_the_content())) : ?>
                        <div class="ab-lede"><?php the_content(); ?></div>
                    <?php else : ?>
                        <div class="ab-lede"><p>A custom home is two years of your life. We think you should know
                                exactly who you are spending them with.</p></div>
                    <?php endif; ?>
                </div>
                <?php
                $ab_hero_img = lh_field_image('about_hero_image', 'img/about-hero.jpg', 'full', array(
                        'alt' => sprintf(
                        /* translators: %s: company name */
                                esc_attr__('The %s studio', 'luxury-homes'),
                                $company
                        ),
                ));
                if ($ab_hero_img) : ?>
                    <figure class="ab-hero-band"><?php echo $ab_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by lh_field_image ?></figure>
                <?php endif; ?>
            </section>
        <?php endwhile; ?>

        <!-- 2. Story -->
        <section class="ab-story" aria-labelledby="ab-story-title">
            <div class="ab-story-in">
                <span class="ab-story-year" aria-hidden="true"><?php echo esc_html($ab_founded); ?></span>
                <h2 class="ab-story-pull" id="ab-story-title" data-reveal>
                    <?php echo wp_kses_post(lh_field('about_story_head', 'We never became a volume builder, <span class="ab-pull-b">on&nbsp;purpose.</span>')); ?>
                </h2>
                <div class="ab-story-cols">
                    <?php echo wp_kses_post(lh_field('about_story_body',
                            '<p>We started in ' . esc_html($ab_founded) . ' with a pickup, a framing crew of three and a promise to a family in Cherry Hills: we would build their house as if it were our own. We have not found a reason to work any other way since.</p>'
                            . '<p>We could have grown. Instead we kept the crew and capped the year at four houses &mdash; as many as we can stand on site for, every day, until they are finished.</p>'
                            . '<p>Twenty years on, most of the same people are still here. That is the entire trick, and there is not a second one.</p>'
                    )); ?>
                </div>
                <ul class="ab-story-figs" data-reveal-self>
                    <?php foreach ($ab_figs as $ab_f) : ?>
                        <li>
                            <b><span class="ab-count"
                                     data-to="<?php echo esc_attr(preg_replace('/[^0-9].*$/', '', $ab_f['n'])); ?>"><?php echo esc_html($ab_f['n']); ?></span><?php
                                if (!empty($ab_f['suffix'])) : ?>
                                    <i><?php echo esc_html($ab_f['suffix']); ?></i><?php endif;
                                ?></b>
                            <span><?php echo esc_html($ab_f['label']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <!-- 3. Team -->
        <section class="ab-team" aria-labelledby="ab-team-title">
            <div class="ab-team-in">
                <div class="ab-team-head">
                    <h2 id="ab-team-title">The people you&rsquo;ll actually deal&nbsp;with</h2>
                    <p>Here they are. No project manager you never meet &mdash; these four are on your house from the
                        first sketch to the final walkthrough.</p>
                </div>
                <ol class="ab-rows">
                    <?php foreach ($ab_team as $ab_i => $ab_p) :
                        $ab_src = lh_about_photo($ab_p['photo'] ?? '');
                        ?>
                        <li class="ab-row" data-reveal>
                            <figure class="ab-row-shot">
                                <?php if ($ab_src) : ?>
                                    <img src="<?php echo esc_url($ab_src); ?>"
                                         alt="<?php echo esc_attr($ab_p['name']); ?>" loading="lazy" decoding="async"
                                         width="600" height="600">
                                <?php else : ?>
                                    <span class="ab-ph" aria-hidden="true"></span>
                                <?php endif; ?>
                            </figure>
                            <div class="ab-row-copy">
                                <span class="ab-no"
                                      aria-hidden="true"><?php echo esc_html(str_pad($ab_i + 1, 2, '0', STR_PAD_LEFT)); ?></span>
                                <h3 class="ab-name"><?php echo esc_html($ab_p['name']); ?></h3>
                                <span class="ab-title"><?php echo esc_html($ab_p['title']); ?></span>
                                <p class="ab-bio"><?php echo esc_html($ab_p['bio']); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>

        <!-- 4. The plate -->
        <section class="ab-plate-sec" aria-labelledby="ab-plate-title">
            <div class="ab-plate-in">
                <div class="ab-plate-copy">
                    <span class="eyebrow"><?php esc_html_e('The plate', 'luxury-homes'); ?></span>
                    <h2 id="ab-plate-title">Every house we finish gets one of&nbsp;these.</h2>
                    <p>Cast brass, set into the mechanical room wall where no guest will ever see it. Stamped with the
                        house number and everyone whose hands were on it.</p>
                    <p>We have made fifty-six. The names change slowly &mdash; that is the entire point.</p>
                </div>
                <figure class="ab-plate-stage" data-reveal>
                    <div class="ab-plate" id="abPlate">
                        <span class="ab-screw ab-screw-tl" aria-hidden="true"></span><span class="ab-screw ab-screw-tr"
                                                                                           aria-hidden="true"></span>
                        <span class="ab-screw ab-screw-bl" aria-hidden="true"></span><span class="ab-screw ab-screw-br"
                                                                                           aria-hidden="true"></span>
                        <div class="ab-plate-face">
                            <p class="ab-plate-maker">Cherry Hills &middot; Colorado</p>
                            <p class="ab-plate-no">House N<sup>o</sup> 56</p>
                            <span class="ab-plate-line" aria-hidden="true"></span>
                            <ul class="ab-plate-roster">
                                <li><b>Marcus Flinders</b><i>Founder</i></li>
                                <li><b>Spencer Edwards</b><i>Build</i></li>
                                <li><b>Patty Smith</b><i>Design</i></li>
                                <li><b>Charles Edington</b><i>Finance</i></li>
                            </ul>
                            <span class="ab-plate-line" aria-hidden="true"></span>
                            <p class="ab-plate-raised">Raised <?php echo esc_html(gmdate('Y')); ?></p>
                        </div>
                        <span class="ab-plate-sheen" aria-hidden="true"></span>
                    </div>
                    <figcaption>Maker&rsquo;s plate, cast brass, 140 &times; 180&nbsp;mm.</figcaption>
                </figure>
            </div>
        </section>

        <!-- 5. Awards -->
        <section class="ab-awards" aria-labelledby="ab-awards-title">
            <div class="ab-awards-in">
                <div class="ab-awards-head">
                    <h2 id="ab-awards-title">Recognised work</h2>
                    <p>Judged by people who build for a living, on houses you can go and look at. <a class="ab-link"
                                                                                                     href="<?php echo esc_url(home_url('/our-homes/')); ?>">See
                            all fifty-six</a>.</p>
                </div>
                <ol class="ab-awards-list" data-reveal-self>
                    <?php foreach ($ab_awards as $ab_a) : ?>
                        <li>
                            <span class="ab-aw-year"><?php echo esc_html($ab_a['year']); ?></span>
                            <span class="ab-aw-name"><?php echo esc_html($ab_a['name']); ?></span>
                            <span class="ab-aw-for"><?php echo esc_html($ab_a['body']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>

        <!-- 6. One team + cost -->
        <section class="ab-turnkey">
            <div class="ab-turnkey-in">
                <div class="ab-turnkey-copy">
                    <span class="eyebrow"><?php esc_html_e('One team', 'luxury-homes'); ?></span>
                    <h2>Land, drawings, permits, build.</h2>
                    <p>You are not coordinating an architect, a builder and three subcontractors who have never met. We
                        <a class="ab-link" href="<?php echo esc_url(home_url('/how-we-build/')); ?>">walk the site, draw
                            it, permit it and build it</a> &mdash; and the person who shook your hand at the first
                        meeting is the one who hands you the keys.</p>
                    <p class="ab-cost"><b>Most of our homes fall between $1.5M and $4M</b>, depending on the site and
                        the finish. We price from real drawings, not a per-square-foot guess, and the contract is fixed
                        before a shovel moves &mdash; <a class="ab-link"
                                                         href="<?php echo esc_url(home_url('/contact/')); ?>">ask us
                            what yours would cost</a>.</p>
                </div>
            </div>
        </section>

        <!-- 7. Warranty -->
        <section class="ab-warranty" aria-labelledby="ab-warranty-title">
            <div class="ab-warranty-in">
                <div class="ab-warranty-head">
                    <h2 id="ab-warranty-title">After you move in</h2>
                    <p>The part most builders go quiet about. Here is ours in writing.</p>
                </div>
                <ol class="ab-warranty-grid">
                    <?php foreach ($ab_warranty as $ab_w) : ?>
                        <li><b><?php echo esc_html($ab_w['term']); ?>
                                <i><?php echo esc_html($ab_w['unit']); ?></i></b><span><?php echo esc_html($ab_w['covers']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>

        <!-- 8. Credentials -->
        <section class="ab-cred">
            <div class="ab-cred-in">
                <div class="ab-cred-where">
                    <span class="eyebrow"><?php esc_html_e('Where we build', 'luxury-homes'); ?></span>
                    <ul>
                        <?php foreach ($ab_metros as $ab_m) : ?>
                            <li><?php echo esc_html($ab_m); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <dl class="ab-titleblock-in">
                    <?php foreach ($ab_reg as $ab_r) : ?>
                        <div class="ab-tb-cell">
                            <dt><?php echo esc_html($ab_r['k']); ?></dt>
                            <dd><?php echo esc_html($ab_r['v']); ?></dd>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($ab_members) : ?>
                        <div class="ab-members"><?php echo esc_html__('Member', 'luxury-homes') . ' &mdash; ' . esc_html($ab_members); ?></div>
                    <?php endif; ?>
                </dl>
            </div>
        </section>

        <!-- 9. Quote -->
        <section class="ab-quote">
            <div class="ab-quote-in">
                <?php $ab_q_src = lh_about_photo($ab_team[0]['photo'] ?? ''); ?>
                <?php if ($ab_q_src) : ?>
                    <figure class="ab-quote-shot"><img src="<?php echo esc_url($ab_q_src); ?>"
                                                       alt="<?php echo esc_attr($ab_team[0]['name']); ?>" loading="lazy"
                                                       decoding="async" width="600" height="600"></figure>
                <?php endif; ?>
                <blockquote>
                    <p>If we build for you, you&rsquo;ll see me at your house every week. That&rsquo;s not a service
                        promise &mdash; it&rsquo;s just how I like to spend a Tuesday.</p>
                    <span class="ab-sign"><?php echo esc_html($ab_team[0]['name']); ?></span>
                    <cite><?php esc_html_e('Founder & President', 'luxury-homes'); ?></cite>
                </blockquote>
            </div>
        </section>

        <!-- 10. CTA -->
        <section class="hm-cta">
            <div class="hm-cta-in">
                <span class="eyebrow"><?php esc_html_e('Next', 'luxury-homes'); ?></span>
                <h2>Let&rsquo;s walk your <em>land</em>.</h2>
                <a class="sh-btn"
                   href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Start the conversation', 'luxury-homes'); ?>
                    &rarr;</a>
            </div>
        </section>

    </main>

<?php get_footer(); ?>