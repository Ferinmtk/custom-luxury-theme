<footer class="site-footer">
	<div class="site-footer__inner">

		<div class="site-footer__brandcol">
			<a class="site-footer__wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php
				/*
				 * The wordmark alone, not the stacked lockup. The client's stacked
				 * artwork sets the RM monogram in a light metallic square, which on a
				 * true-black footer reads as a white box floating above the name. The
				 * signature carries the brand on its own.
				 */
				?>
				<img class="site-footer__logo" src="<?php echo lh_asset( 'img/brand/wordmark-light.png' ); ?>"
				     alt="<?php echo esc_attr( lh_company() ); ?>" width="656" height="160" loading="lazy" decoding="async">
			</a>
			<p class="site-footer__blurb"><?php echo esc_html( lh_field( 'footer_blurb', 'One team, from the first walk of the land to the day we hand you the keys.' ) ); ?></p>
			<ul class="site-footer__social" aria-label="<?php esc_attr_e( 'Social', 'luxury-homes' ); ?>">
				<?php
				$lh_socials = array(
					array( 'label' => 'Instagram', 'icon' => 'instagram', 'url' => lh_field( 'social_instagram', '#' ) ),
					array( 'label' => 'Houzz',     'icon' => 'houzz',     'url' => lh_field( 'social_houzz', '#' ) ),
					array( 'label' => 'LinkedIn',  'icon' => 'linkedin',  'url' => lh_field( 'social_linkedin', '#' ) ),
					array( 'label' => 'Pinterest', 'icon' => 'pinterest', 'url' => lh_field( 'social_pinterest', '#' ) ),
					array( 'label' => 'Facebook',  'icon' => 'facebook',  'url' => lh_field( 'social_facebook', '#' ) ),
				);
				foreach ( $lh_socials as $lh_s ) :
					$lh_ext = ( '#' !== $lh_s['url'] ) ? ' target="_blank" rel="noopener"' : '';
					?>
					<li><a href="<?php echo esc_url( $lh_s['url'] ); ?>" aria-label="<?php echo esc_attr( $lh_s['label'] ); ?>"<?php echo $lh_ext; // phpcs:ignore ?>><?php echo lh_social_icon( $lh_s['icon'] ); // phpcs:ignore ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="site-footer__col">
			<?php
			/*
			 * Phone and service area render only when set. They used to default to
			 * a fabricated Colorado number and "the Front Range" — wrong region and
			 * a dead link. Set ACF "phone" / "phone_display" / "service_areas".
			 */
			$lh_f_phone   = trim( (string) lh_field( 'phone_display', '' ) );
			$lh_f_tel     = trim( (string) lh_field( 'phone', '' ) );
			$lh_f_areas   = trim( (string) lh_field( 'service_areas', '' ) );
			if ( '' !== $lh_f_phone || '' !== $lh_f_tel ) :
				$lh_f_tel = '' !== $lh_f_tel ? $lh_f_tel : $lh_f_phone;
				?>
				<p class="site-footer__label"><?php esc_html_e( 'Talk to us', 'luxury-homes' ); ?></p>
				<p class="site-footer__phone">
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $lh_f_tel ) ); ?>"><?php echo esc_html( '' !== $lh_f_phone ? $lh_f_phone : $lh_f_tel ); ?></a>
				</p>
			<?php endif; ?>
			<?php if ( '' !== $lh_f_areas ) : ?>
				<p class="site-footer__label"><?php esc_html_e( 'Where we build', 'luxury-homes' ); ?></p>
				<p class="site-footer__text"><?php echo esc_html( $lh_f_areas ); ?></p>
			<?php endif; ?>
		</div>

		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Footer', 'luxury-homes' ); ?>">
			<p class="site-footer__label"><?php esc_html_e( 'Explore', 'luxury-homes' ); ?></p>
			<ul class="site-footer__links">
				<?php foreach ( lh_nav_items() as $lh_item ) : ?>
					<li><a href="<?php echo esc_url( $lh_item['url'] ); ?>"><?php echo esc_html( $lh_item['label'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

	</div>

	<div class="site-footer__bar">
		<p class="site-footer__legal">&copy;&nbsp;<?php
			printf(
				/* translators: 1: year, 2: company name */
				esc_html__( '%1$s %2$s. All rights reserved.', 'luxury-homes' ),
				esc_html( gmdate( 'Y' ) ),
				esc_html( lh_company() )
			);
		?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
