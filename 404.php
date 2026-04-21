<?php
/**
 * The 404 template.
 *
 * @package Jeanne
 */

get_header();
?>
<main class="site-main" id="main" role="main">
	<div class="container error-404">
		<h1 class="error-404__title">404</h1>
		<p class="error-404__message"><?php esc_html_e( 'Page not found.', 'jeanne' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="back-link">
			<?php esc_html_e( '← Return home', 'jeanne' ); ?>
		</a>
	</div>
</main>
<?php get_footer(); ?>
