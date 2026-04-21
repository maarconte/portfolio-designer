<?php
/**
 * Single project template — a standalone accessible project page.
 *
 * @package Jeanne
 */

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$post_id     = get_the_ID();
		$year        = get_post_meta( $post_id, '_jeanne_year', true );
		$client      = get_post_meta( $post_id, '_jeanne_client', true );
		$category    = get_post_meta( $post_id, '_jeanne_category', true );
		$gallery_ids = get_post_meta( $post_id, '_jeanne_gallery', true );
		if ( ! is_array( $gallery_ids ) ) {
			$gallery_ids = array();
		}
		?>

		<main class="site-main site-main--project" id="main" role="main">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-project' ); ?>>

				<header class="single-project__header">
					<h1 class="single-project__title"><?php the_title(); ?></h1>

					<div class="single-project__meta">
						<?php if ( $category ) : ?>
							<span class="single-project__meta-item"><?php echo esc_html( $category ); ?></span>
						<?php endif; ?>
						<?php if ( $client ) : ?>
							<span class="single-project__meta-item"><?php echo esc_html( $client ); ?></span>
						<?php endif; ?>
						<?php if ( $year ) : ?>
							<span class="single-project__meta-item"><?php echo esc_html( $year ); ?></span>
						<?php endif; ?>
					</div>
				</header>

				<?php if ( ! empty( $gallery_ids ) ) : ?>
					<div class="single-project__gallery">
						<?php foreach ( $gallery_ids as $img_id ) : ?>
							<?php
							$img  = wp_get_attachment_image( $img_id, 'jeanne-modal', false, array( 'class' => 'single-project__gallery-img', 'loading' => 'lazy' ) );
							$full = wp_get_attachment_image_src( $img_id, 'full' );
							?>
							<?php if ( $img && $full ) : ?>
								<figure class="single-project__gallery-item">
									<a href="<?php echo esc_url( $full[0] ); ?>" target="_blank" rel="noopener">
										<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								</figure>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php elseif ( has_post_thumbnail() ) : ?>
					<div class="single-project__cover">
						<?php the_post_thumbnail( 'jeanne-modal' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( get_the_content() ) : ?>
					<div class="single-project__content">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>

				<footer class="single-project__footer">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="back-link">
						<?php esc_html_e( '← Back to projects', 'jeanne' ); ?>
					</a>
				</footer>

			</article>
		</main>

		<?php
	}
}

get_footer();
