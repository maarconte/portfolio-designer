<footer class="site-footer" role="contentinfo">
	<div class="site-footer__inner">

		<div class="site-footer__left">
			<?php
			$footer_text = get_theme_mod( 'jeanne_footer_text', '' );
			if ( $footer_text ) {
				echo wp_kses_post( $footer_text );
			} else {
				echo '<span class="site-footer__copy">&copy; ' . esc_html( date( 'Y' ) ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
			}
			?>
		</div>

		<div class="site-footer__right">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'footer-nav',
				'depth'          => 1,
				'fallback_cb'    => false,
			) );
			?>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
