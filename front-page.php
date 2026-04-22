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
		<?php $projects_data = array(); ?>

		<div class="slider" id="project-slider" aria-label="<?php esc_attr_e('Projects', 'jeanne'); ?>">

			<div class="slider__track" id="slider-track">

				<?php while ($projects_query->have_posts()) : $projects_query->the_post(); ?>
					<?php
					$project_data = jeanne_get_project_data(get_the_ID());
					$projects_data[$project_data['id']] = $project_data;

					$card_thumb = has_post_thumbnail()
						? get_the_post_thumbnail_url(get_the_ID(), 'full')
						: '';
					?>

					<article
						class="project-card"
						role="button"
						tabindex="0"
						aria-label="<?php echo esc_attr(sprintf(__('View project: %s', 'jeanne'), $project_data['title'])); ?>"
						data-project-id="<?php echo esc_attr($project_data['id']); ?>">

						<div class="project-card__image">
							<?php if ($card_thumb) : ?>
								<img
									src="<?php echo esc_url($card_thumb); ?>"
									alt="<?php echo esc_attr($project_data['title']); ?>"
									loading="lazy">
							<?php else : ?>
								<div class="project-card__placeholder"></div>
							<?php endif; ?>
						</div>

						<div class="project-card__info">
							<h2 class="project-card__title"><?php echo esc_html($project_data['title']); ?></h2>
							<?php if ($project_data['category']) : ?>
								<p class="project-card__meta"><?php echo esc_html($project_data['category']); ?></p>
							<?php endif; ?>
						</div>

					</article>

				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>

				<?php
				// Pass project data to JS
				wp_add_inline_script('jeanne-main', 'var jeanneProjects = ' . wp_json_encode($projects_data) . ';', 'before');
				?>

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
