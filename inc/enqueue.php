<?php
/**
 * Enqueue scripts and styles.
 *
 * @package Jeanne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jeanne_enqueue_assets() {
	// Google Fonts — DM Sans 600 + Averia Serif Libre 300 / 300i
	wp_enqueue_style(
		'jeanne-google-fonts',
		'https://fonts.googleapis.com/css2?family=Averia+Serif+Libre:ital,wght@0,300;1,300&family=DM+Sans:wght@600&display=swap',
		array(),
		null
	);

	// Main stylesheet (theme declaration file — minimal)
	wp_enqueue_style( 'jeanne-style', get_stylesheet_uri(), array(), JEANNE_VERSION );

	// Primary stylesheet
	wp_enqueue_style(
		'jeanne-main',
		JEANNE_URI . '/assets/css/main.css',
		array(),
		JEANNE_VERSION
	);

	// Main JS (slider + modal + cursor)
	wp_enqueue_script(
		'jeanne-main',
		JEANNE_URI . '/assets/js/main.js',
		array(),
		JEANNE_VERSION,
		true
	);

	wp_localize_script( 'jeanne-main', 'jeanneConfig', array(
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'homeUrl'       => home_url( '/' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'jeanne_enqueue_assets' );

/**
 * Enqueue admin assets for the project post type.
 */
function jeanne_enqueue_admin_assets( $hook ) {
	global $post;

	// Only load on the project edit screen
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	if ( ! $post || 'project' !== $post->post_type ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_style(
		'jeanne-admin',
		JEANNE_URI . '/assets/css/admin.css',
		array(),
		JEANNE_VERSION
	);

	wp_enqueue_script(
		'jeanne-admin-gallery',
		JEANNE_URI . '/assets/js/admin-gallery.js',
		array( 'jquery' ),
		JEANNE_VERSION,
		true
	);

	wp_localize_script( 'jeanne-admin-gallery', 'jeanneAdmin', array(
		'selectImages' => __( 'Select Gallery Images', 'jeanne' ),
		'useImages'    => __( 'Use these images', 'jeanne' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'jeanne_enqueue_admin_assets' );
