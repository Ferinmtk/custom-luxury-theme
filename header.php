<?php ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'luxury-homes'); ?></a>

<header class="hdr" id="site-header">
    <div class="hdr-in">
        <a class="wordmark" href="<?php echo esc_url(home_url('/')); ?>">
            <?php
            /*
             * The real Richard Marcus lockup, supplied by the client. Two files
             * because the header inverts: .hdr.is-dark swaps them in CSS. The
             * dark-ground file keeps the platinum sheen, which flat text cannot
             * reproduce. Company name stays as the alt text, so the brand is
             * still readable to screen readers and when images fail.
             */
            ?>
            <img class="wordmark__img wordmark__img--on-light"
                 src="<?php echo lh_asset('img/brand/wordmark-dark.png'); ?>"
                 alt="<?php echo esc_attr(lh_company()); ?>" width="263" height="64"
                 fetchpriority="high" decoding="async">
            <img class="wordmark__img wordmark__img--on-dark"
                 src="<?php echo lh_asset('img/brand/wordmark-light.png'); ?>"
                 alt="" aria-hidden="true" width="263" height="64" decoding="async">
        </a>
        <div class="hdr-right">
            <nav class="hdr-nav" aria-label="<?php esc_attr_e('Primary', 'luxury-homes'); ?>">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'container' => false,
                            'menu_class' => 'hdr-menu',
                            'depth' => 1,
                    ));
                } else {
                    lh_nav_list('hdr-menu', array('contact'));
                }
                ?>
            </nav>
            <a class="hdr-cta"
               href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'luxury-homes'); ?></a>
            <button class="nav-toggle" id="navToggle" type="button" aria-expanded="false" aria-controls="navOverlay"
                    aria-label="<?php esc_attr_e('Menu', 'luxury-homes'); ?>">
                <i></i><i></i>
            </button>
        </div>
    </div>
</header>

<div class="nav-overlay" id="navOverlay" role="dialog" aria-modal="true"
     aria-label="<?php esc_attr_e('Site menu', 'luxury-homes'); ?>">
    <nav aria-label="<?php esc_attr_e('Mobile', 'luxury-homes'); ?>">
        <?php
        if (has_nav_menu('primary')) {
            wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'ov-menu',
                    'depth' => 1,
            ));
        } else {
            lh_nav_list('ov-menu');
        }
        ?>
    </nav>
</div>