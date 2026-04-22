<?php

/**
 * Front page template — full-viewport horizontal project slider.
 *
 * @package Jeanne
 */

$projects_count = (int) get_theme_mod('jeanne_projects_count', -1);

$projects_query = new WP_Query(array(
	'post_type'      => 'project',
	'posts_per_page' => $projects_count > 0 ? $projects_count : -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'post_status'    => 'publish',
));

get_header();
?>

<main class="site-main site-main--home" id="main" role="main">

	<?php if ($projects_query->have_posts()) : ?>

		<div class="slider" id="project-slider" aria-label="<?php esc_attr_e('Projects', 'jeanne'); ?>">

			<div class="slider__track" id="slider-track">

				<?php while ($projects_query->have_posts()) : $projects_query->the_post(); ?>
					<?php
					$post_id     = get_the_ID();
					$title       = get_the_title();
					$description = wp_strip_all_tags(get_the_content());
					$permalink   = get_permalink();
					$year        = get_post_meta($post_id, '_jeanne_year', true);
					$client      = get_post_meta($post_id, '_jeanne_client', true);
					$category    = get_post_meta($post_id, '_jeanne_category', true);

					// Gallery images
					$gallery_ids    = get_post_meta($post_id, '_jeanne_gallery', true);
					$gallery_images = array();

					if (is_array($gallery_ids) && ! empty($gallery_ids)) {
						foreach ($gallery_ids as $img_id) {
							$full  = wp_get_attachment_image_src($img_id, 'jeanne-modal');
							$thumb = wp_get_attachment_image_src($img_id, 'jeanne-modal-thumb');
							$alt   = get_post_meta($img_id, '_wp_attachment_image_alt', true);

							if ($full) {
								$gallery_images[] = array(
									'id'    => $img_id,
									'url'   => $full[0],
									'thumb' => $full[0],
									'alt'   => $alt ?: $title,
									'w'     => $full[1],
									'h'     => $full[2],
								);
							}
						}
					}

					// Fallback to featured image if no gallery
					if (empty($gallery_images) && has_post_thumbnail()) {
						$thumb_id = get_post_thumbnail_id();
						$full     = wp_get_attachment_image_src($thumb_id, 'jeanne-modal');
						$alt      = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
						if ($full) {
							$gallery_images[] = array(
								'id'    => $thumb_id,
								'url'   => $full[0],
								'thumb' => $full[0],
								'alt'   => $alt ?: $title,
								'w'     => $full[1],
								'h'     => $full[2],
							);
						}
					}

					$card_thumb = has_post_thumbnail()
						? get_the_post_thumbnail_url($post_id, 'full')
						: '';
					?>

					<article
						class="project-card"
						role="button"
						tabindex="0"
						aria-label="<?php echo esc_attr(sprintf(__('View project: %s', 'jeanne'), $title)); ?>"
						data-project-id="<?php echo esc_attr($post_id); ?>"
						data-title="<?php echo esc_attr($title); ?>"
						data-description="<?php echo esc_attr($description); ?>"
						data-year="<?php echo esc_attr($year); ?>"
						data-client="<?php echo esc_attr($client); ?>"
						data-category="<?php echo esc_attr($category); ?>"
						data-gallery="<?php echo esc_attr(wp_json_encode($gallery_images)); ?>"
						data-permalink="<?php echo esc_url($permalink); ?>">
						<div class="project-card__image">
							<?php if ($card_thumb) : ?>
								<img
									src="<?php echo esc_url($card_thumb); ?>"
									alt="<?php echo esc_attr($title); ?>"
									loading="lazy">
							<?php else : ?>
								<div class="project-card__placeholder"></div>
							<?php endif; ?>
						</div>

						<div class="project-card__info">
							<h2 class="project-card__title"><?php echo esc_html($title); ?></h2>
							<?php if ($category) : ?>
								<p class="project-card__meta"><?php echo esc_html($category); ?></p>
							<?php endif; ?>
						</div>

					</article>

				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>

			</div><!-- .slider__track -->

			<!-- Invisible navigation zones: hover to reveal cursor arrows, click to navigate -->
			<div class="nav-zone nav-zone--prev" role="button" aria-label="<?php esc_attr_e('Previous project', 'jeanne'); ?>" tabindex="0"></div>
			<div class="nav-zone nav-zone--next" role="button" aria-label="<?php esc_attr_e('Next project', 'jeanne'); ?>" tabindex="0"></div>



		</div><!-- .slider -->

	<?php else : ?>

		<div class="no-projects">
			<p><?php esc_html_e('No projects yet. Add your first project from the WordPress admin.', 'jeanne'); ?></p>
		</div>

	<?php endif; ?>
	<?php $tagline = get_bloginfo('description');
	if ($tagline) : ?>
		<p class="slider__tagline"><?php echo esc_html($tagline); ?></p>
	<?php endif; ?>
</main>

<!-- Project Drawer -->
<div
	class="project-modal"
	id="project-modal"
	role="dialog"
	aria-modal="true"
	aria-labelledby="modal-title"
	hidden>

	<div class="modal__overlay" id="modal-overlay"></div>

	<div class="drawer__panel">

		<header class="drawer__header">
			<div>
				<h2 class="modal__title" id="modal-title"></h2>
				<p class="modal__meta" id="modal-meta"></p>
			</div>
			<button class="modal__close" id="modal-close" aria-label="<?php esc_attr_e('Fermer', 'jeanne'); ?>">
				&#x2715;
			</button>
		</header>

		<div class="drawer__body">
			<p class="modal__description" id="modal-description"></p>
			<div class="drawer__gallery" id="modal-image-container"></div>
		</div>

	</div>

</div><!-- .project-modal -->

<!-- Custom cursor element -->
<div class="custom-cursor" id="custom-cursor" aria-hidden="true">
	<span class="custom-cursor__arrow" id="cursor-arrow"></span>
	<span class="custom-cursor__text">More</span>
</div>

<?php get_footer(); ?>
