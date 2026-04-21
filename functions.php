<?php
/**
 * Jeanne theme functions and definitions.
 *
 * @package Jeanne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JEANNE_VERSION', '1.0.0' );
define( 'JEANNE_DIR', get_template_directory() );
define( 'JEANNE_URI', get_template_directory_uri() );

require_once JEANNE_DIR . '/inc/post-types.php';
require_once JEANNE_DIR . '/inc/meta-boxes.php';
require_once JEANNE_DIR . '/inc/customizer.php';
require_once JEANNE_DIR . '/inc/enqueue.php';

function jeanne_setup() {
	load_theme_textdomain( 'jeanne', JEANNE_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	add_image_size( 'jeanne-card', 800, 1100, true );
	add_image_size( 'jeanne-modal', 1920, 1440, false );
	add_image_size( 'jeanne-modal-thumb', 400, 400, true );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'jeanne' ),
	) );
}
add_action( 'after_setup_theme', 'jeanne_setup' );

/**
 * Add body classes for the front page.
 */
function jeanne_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'is-front-page';
	}
	return $classes;
}
add_filter( 'body_class', 'jeanne_body_classes' );
