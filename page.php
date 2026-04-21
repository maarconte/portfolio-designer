<?php
/**
 * The template for displaying static pages.
 *
 * @package Jeanne
 */

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		?>
		<main class="site-main" id="main" role="main">
			<div class="container">
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>
					<header class="page-header">
						<h1 class="page-title"><?php the_title(); ?></h1>
					</header>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</article>
			</div>
		</main>
		<?php
	}
}

get_footer();
