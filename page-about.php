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

/**
 * Team. @placeholder bios — real people, borrowed copy.
 *
 * "role" is the short trade word stamped on the maker's plate (section 4).
 * It is deliberately separate from "title", which is the long marketing one:
 * the plate is cast at 140 x 180 mm and only fits about ten characters a line.
 * Leave it empty and the plate falls back to "title" — which will fit badly,
 * so fill it in.
 */
$ab_team = lh_field('about_team', array(
        array('name' => 'Marcus Flinders', 'title' => 'Builder Prime', 'role' => 'Founder', 'photo' => 'img/team/marcus-flinders.jpg',
                'bio' => 'Founder and President. Accountable for your house from the first walk of the land to the keys — and on your site every week in between. Still owns the first hammer he ever bought.'),
        array('name' => 'Spencer Edwards', 'title' => 'Master Builder', 'role' => 'Build', 'photo' => 'img/team/spencer-edwards.jpg',
                'bio' => 'Twenty-two years a firefighter before he built his first home in 2001. Father of four. He knows current code, current materials, and exactly where the expensive mistakes like to hide.'),
        array('name' => 'Patty Smith', 'title' => 'Designer Extraordinaire', 'role' => 'Design', 'photo' => 'img/team/patty-smith.jpg',
                'bio' => 'Designing since 1998, on houses from 1,500 to 15,000 square feet. She will ask how you make coffee before she draws the kitchen — and she means it.'),
        array('name' => 'Charles Edington', 'title' => 'The Money Guy', 'role' => 'Finance', 'photo' => 'img/team/charles-edington.jpg',
                'bio' => 'Lending since 2002. Construction loans, draw schedules, allowances — and the person who will tell you the honest number before you fall for the wrong lot.'),
));

/**
 * Names cast into the plate. Four is not a style choice — the roster block is
 * sized for four lines and a fifth pushes the raised date off the brass.
 */
$ab_plate_roster = array_slice((array)$ab_team, 0, 4);

/** Story figures. @placeholder values. */
$ab_founded = (int)lh_field('about_founded', 2006);

/**
 * Houses completed. Single source for the plate number. The prose that
 * spells it out
 * ("fifty-six") stays editable copy rather than a generated number word —
 * generating it would only work in English.
 */
$ab_houses = (int)lh_field('about_houses', 56);


/** Awards. @placeholder — must be real before launch. "house" is the plate
 *  number the award was won on; leave it empty and the citation just omits it. */
$ab_awards = lh_field('about_awards', array(
        array('year' => '2024', 'name' => 'MAME Award — Custom Home over 6,000 sq ft', 'body' => 'HBA of Metro Denver', 'house' => '52'),
        array('year' => '2023', 'name' => 'Best of Houzz — Service', 'body' => 'Fourth consecutive year', 'house' => ''),
        array('year' => '2022', 'name' => 'Parade of Homes — People’s Choice', 'body' => 'Stonebrook Court', 'house' => '45'),
        array('year' => '2021', 'name' => 'MAME Award — Best Interior Design', 'body' => 'HBA of Metro Denver', 'house' => '42'),
        array('year' => '2019', 'name' => 'Best in American Living — Regional', 'body' => 'NAHB', 'house' => '37'),
));

/** Warranty schedule. */
$ab_warranty = lh_field('about_warranty', array(
        array('term' => '1', 'unit' => 'year', 'covers' => 'Workmanship, finishes and fit'),
        array('term' => '2', 'unit' => 'years', 'covers' => 'Mechanical, electrical, plumbing'),
        array('term' => '10', 'unit' => 'years', 'covers' => 'Structural — frame, foundation, roof'),
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

        <!-- 2. Story — dark photographic band.
             The card is warm white on forest so the origin story reads as an
             inset plate; the figures sit on the dark ground below it. This is
             the page's one tonal break: cream runs unbroken from here to the
             closing CTA otherwise, and the brass plate lands harder for it. -->
        <section class="ab-story" aria-labelledby="ab-story-title">
            <?php
            $ab_story_img = lh_field_image('about_story_image', 'img/story-dusk.jpg', 'full', array(
                    'alt' => '',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                    'width' => '1500',
                    'height' => '854',
            ));
            if ($ab_story_img) : ?>
                <div class="ab-story-media" aria-hidden="true">
                    <?php echo $ab_story_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by lh_field_image ?>
                    <span class="ab-story-fade"></span>
                </div>
            <?php endif; ?>

            <div class="ab-story-in">
                <span class="ab-story-year" aria-hidden="true"><?php echo esc_html($ab_founded); ?></span>

                <div class="ab-story-card" data-reveal>
                    <h2 class="ab-story-pull" id="ab-story-title">
                        <?php echo wp_kses_post(lh_field('about_story_head', 'We never became a volume builder, <span class="ab-pull-b">on&nbsp;purpose.</span>')); ?>
                    </h2>
                    <span class="ab-story-rule" aria-hidden="true"></span>
                    <div class="ab-story-cols">
                        <?php echo wp_kses_post(lh_field('about_story_body',
                                '<p>We started in ' . esc_html($ab_founded) . ' with a pickup, a framing crew of three and a promise to a family in Cherry Hills: we would build their house as if it were our own. We have not found a reason to work any other way since.</p>'
                                . '<p>We could have grown. Instead we kept the crew and capped the year at four houses &mdash; as many as we can stand on site for, every day, until they are finished.</p>'
                                . '<p>Twenty years on, most of the same people are still here. That is the entire trick, and there is not a second one.</p>'
                        )); ?>
                    </div>
                </div>

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
                    <p><?php echo esc_html(lh_field('about_plate_note', 'We have made fifty-six. The names change slowly — that is the entire point.')); ?></p>

                </div>
                <figure class="ab-plate-stage" data-reveal>
                    <div class="ab-plate" id="abPlate">
                        <span class="ab-screw ab-screw-tl" aria-hidden="true"></span><span class="ab-screw ab-screw-tr"
                                                                                           aria-hidden="true"></span>
                        <span class="ab-screw ab-screw-bl" aria-hidden="true"></span><span class="ab-screw ab-screw-br"
                                                                                           aria-hidden="true"></span>
                        <div class="ab-plate-face">
                            <p class="ab-plate-maker">Cherry Hills &middot; Colorado</p>
                            <p class="ab-plate-no">House N<sup>o</sup> <?php echo esc_html($ab_houses); ?></p>
                            <span class="ab-plate-line" aria-hidden="true"></span>
                            <ul class="ab-plate-roster">
                                <?php foreach ($ab_plate_roster as $ab_pr) :
                                    $ab_pr_role = trim((string)($ab_pr['role'] ?? ''));
                                    if ('' === $ab_pr_role) {
                                        $ab_pr_role = trim((string)($ab_pr['title'] ?? ''));
                                    }
                                    ?>
                                    <li>
                                        <b><?php echo esc_html($ab_pr['name'] ?? ''); ?></b><i><?php echo esc_html($ab_pr_role); ?></i>
                                    </li>
                                <?php endforeach; ?>
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

        <!-- 5. Awards — set as a colophon: year, citation, then the house it was
             won on. The house number is the point; it ties a jury's opinion to
             something the reader can go and stand in front of. -->
        <section class="ab-awards" aria-labelledby="ab-awards-title">
            <div class="ab-awards-in">
                <div class="ab-awards-head">
                    <h2 id="ab-awards-title">Recognised work</h2>
                    <p><?php echo esc_html(lh_field('about_awards_note', 'Five of the fifty-six carry a pin. Judged by people who build for a living.')); ?>
                        <a class="ab-link"
                           href="<?php echo esc_url(home_url('/our-homes/')); ?>"><?php esc_html_e('See them all', 'luxury-homes'); ?></a>.
                    </p>
                </div>
                <ol class="ab-awards-list" data-reveal-self>
                    <?php foreach ($ab_awards as $ab_a) : ?>
                        <li>
                            <span class="ab-aw-year"><?php echo esc_html($ab_a['year']); ?></span>
                            <span class="ab-aw-name"><?php echo esc_html($ab_a['name']); ?></span>
                            <span class="ab-aw-for">
                                <?php echo esc_html($ab_a['body']); ?>
                                <?php if (!empty($ab_a['house'])) : ?>
                                    <i aria-hidden="true">/</i>
                                    <em>N<sup>o</sup>&nbsp;<?php echo esc_html($ab_a['house']); ?></em>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>

        <!-- 6. One team + cost -->
        <section class="ab-turnkey" aria-labelledby="ab-turnkey-title">
            <div class="ab-turnkey-in">
                <div class="ab-turnkey-copy">
                    <span class="eyebrow"><?php esc_html_e('One team', 'luxury-homes'); ?></span>
                    <h2 id="ab-turnkey-title">Land, drawings, permits, <span class="ab-pull-b">build.</span></h2>
                    <p>You are not coordinating an architect, a builder and three subcontractors who have never met. We
                        <a class="ab-link" href="<?php echo esc_url(home_url('/how-we-build/')); ?>">walk the site, draw
                            it, permit it and build it</a> &mdash; and the person who shook your hand at the first
                        meeting is the one who hands you the keys.</p>

                    <?php
                    /* The range was buried mid-paragraph behind a bolded clause.
                       It is the one number every visitor is scanning for, so it
                       is set as a figure with the method beside it. */
                    ?>
                    <div class="ab-price">
                        <div class="ab-price-col">
                            <span class="ab-price-lab"><?php esc_html_e('Typical range', 'luxury-homes'); ?></span>
                            <b class="ab-price-fig"><?php echo esc_html(lh_field('about_price_range', '$1.5–4M')); ?></b>
                        </div>
                        <span class="ab-price-rule" aria-hidden="true"></span>
                        <div class="ab-price-col">
                            <span class="ab-price-lab"><?php esc_html_e('How it is priced', 'luxury-homes'); ?></span>
                            <p class="ab-price-note">From real drawings, not a per-square-foot guess. Fixed before a
                                shovel moves &mdash; <a class="ab-link"
                                                        href="<?php echo esc_url(home_url('/contact/')); ?>">ask us what
                                    yours would cost</a>.</p>
                        </div>
                    </div>
                </div>
                <?php
                /* The stylesheet has carried .ab-turnkey figure and a multiply
                   blend for this slot all along — the markup just never had the
                   figure, which is why the section sat in a half-empty band.
                   turnkey-plans.jpg is white-point corrected so multiply knocks
                   the studio sweep out cleanly; a replacement needs the same
                   treatment or it will sit in a grey box. */
                $ab_turnkey_img = lh_field_image('about_turnkey_image', 'img/turnkey-plans.jpg', 'large', array(
                        'alt' => esc_attr__('A house under construction standing on its own blueprints, with rolled drawings and window and door samples alongside', 'luxury-homes'),
                        'loading' => 'lazy',
                        'decoding' => 'async',
                        'width' => '500',
                        'height' => '410',
                ));
                if ($ab_turnkey_img) : ?>
                    <figure data-reveal><?php echo $ab_turnkey_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by lh_field_image ?></figure>
                <?php endif; ?>
            </div>
        </section>

        <!-- 7. Warranty — the terms are ruled cells rather than a flat row: a
             guarantee reads as a guarantee when it looks like it was drawn up. -->
        <section class="ab-warranty" aria-labelledby="ab-warranty-title">
            <div class="ab-warranty-in">
                <div class="ab-warranty-head">
                    <h2 id="ab-warranty-title">After you move in</h2>
                    <p>The part most builders go quiet about.</p>
                </div>
                <p class="ab-warranty-promise">If something we built fails, we fix it &mdash; <b>on our dime, no
                        argument.</b> In writing:</p>
                <ol class="ab-warranty-grid">
                    <?php foreach ($ab_warranty as $ab_w) : ?>
                        <li>
                            <span class="ab-term-n">
                                <b><?php echo esc_html($ab_w['term']); ?></b><i><?php echo esc_html($ab_w['unit']); ?></i>
                            </span>
                            <span class="ab-term-covers"><?php echo esc_html($ab_w['covers']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>

        <!-- 8. Credentials -->
        <section class="ab-cred" aria-labelledby="ab-cred-title">
            <div class="ab-cred-in">
                <div class="ab-cred-where">
                    <h2 class="eyebrow" id="ab-cred-title"><?php esc_html_e('Where we build', 'luxury-homes'); ?></h2>
                    <p class="ab-cred-metros"><?php echo esc_html(implode(' · ', $ab_metros)); ?></p>
                    <p class="ab-cred-caveat"><?php echo esc_html(lh_field('about_range_caveat', 'If your site is more than ninety minutes from our shop, we will tell you honestly that someone closer should build it.')); ?></p>
                </div>
                <?php
                /* Registry rows, label left and value right. The members line is
                   folded in as a final row rather than spanning underneath —
                   that span was what forced the phantom cell in the old grid. */
                $ab_rows = $ab_reg;
                if ($ab_members) {
                    $ab_rows[] = array('k' => esc_html__('Member', 'luxury-homes'), 'v' => $ab_members, 'wide' => true);
                }
                ?>
                <dl class="ab-creds">
                    <?php foreach ($ab_rows as $ab_r) : ?>
                        <div class="ab-creds-row<?php echo empty($ab_r['wide']) ? '' : ' ab-creds-row--wide'; ?>">
                            <dt><?php echo esc_html($ab_r['k']); ?></dt>
                            <dd><?php echo esc_html($ab_r['v']); ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        </section>

        <!-- 9. CTA -->
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