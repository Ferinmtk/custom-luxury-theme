<footer class="site-footer">
	<div class="site-footer__inner">
		<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'luxury-homes' ); ?>">
			<?php lh_nav_list( 'site-footer__menu' ); ?>
		</nav>
		<p class="site-footer__brand"><?php echo esc_html( lh_company() ); ?></p>
		<p class="site-footer__meta">
			<?php echo esc_html( lh_field( 'service_areas', 'Serving the Front Range' ) ); ?>
			<span aria-hidden="true">&middot;</span>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', lh_field( 'phone', '+13035550100' ) ) ); ?>">
				<?php echo esc_html( lh_field( 'phone_display', '(303) 555-0100' ) ); ?>
			</a>
		</p>
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
