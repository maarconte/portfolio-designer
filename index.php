<?php
/**
 * The main template file — fallback for all pages not covered by a specific template.
 *
 * @package Jeanne
 */

get_header();
?>

<main class="site-main" id="main" role="main">
	<div class="container">

		<?php if ( have_posts() ) : ?>

			<div class="posts-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="post-card__thumb">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</a>
							</div>
						<?php endif; ?>
						<div class="post-card__body">
							<h2 class="post-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="post-card__excerpt"><?php the_excerpt(); ?></div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_navigation(); ?>

		<?php else : ?>

			<p class="no-results"><?php esc_html_e( 'Nothing found here.', 'jeanne' ); ?></p>

		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
