<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<header class="site-header" id="site-header" role="banner">
		<div class="site-header__inner">

			<div class="site-branding">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title" rel="home">
					<?php
					$random_logos = get_theme_mod( 'jeanne_random_logos_enabled', true ) ? jeanne_get_random_logos() : array();
					if ( ! empty( $random_logos ) ) :
						$logos_json = wp_json_encode( $random_logos );
					?>
						<div class="random-logo-wrap" data-logos="<?php echo esc_attr( $logos_json ); ?>">
							<img
								class="custom-logo random-logo"
								src=""
								alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
								width="200"
								height="60"
							>
						</div>
						<script>
						(function(){
							var wrap  = document.currentScript.previousElementSibling;
							var img   = wrap.querySelector('img');
							var logos = JSON.parse(wrap.dataset.logos);
							if (logos && logos.length) {
								img.src = logos[Math.floor(Math.random() * logos.length)];
							}
						})();
						</script>
					<?php elseif ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php endif; ?>
				</a>

			</div>

			<a href="<?php echo esc_url(home_url('/')); ?>" class="site-title text-title" rel="home">
				<?php bloginfo('name'); ?>
			</a>

			<?php
			wp_nav_menu(array(
				'theme_location'  => 'primary',
				'container'       => 'nav',
				'container_class' => 'site-nav text-title',
				'container_id'    => 'site-navigation',
				'menu_class'      => 'nav-menu',
				'fallback_cb'     => false,
			));
			?>

		</div>
	</header>
