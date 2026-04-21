<?php
/**
 * Register the Project custom post type.
 *
 * @package Jeanne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jeanne_register_post_types() {
	$labels = array(
		'name'                  => _x( 'Projects', 'Post type general name', 'jeanne' ),
		'singular_name'         => _x( 'Project', 'Post type singular name', 'jeanne' ),
		'menu_name'             => _x( 'Projects', 'Admin Menu text', 'jeanne' ),
		'name_admin_bar'        => _x( 'Project', 'Add New on Toolbar', 'jeanne' ),
		'add_new'               => __( 'Add New', 'jeanne' ),
		'add_new_item'          => __( 'Add New Project', 'jeanne' ),
		'new_item'              => __( 'New Project', 'jeanne' ),
		'edit_item'             => __( 'Edit Project', 'jeanne' ),
		'view_item'             => __( 'View Project', 'jeanne' ),
		'all_items'             => __( 'All Projects', 'jeanne' ),
		'search_items'          => __( 'Search Projects', 'jeanne' ),
		'not_found'             => __( 'No projects found.', 'jeanne' ),
		'not_found_in_trash'    => __( 'No projects found in Trash.', 'jeanne' ),
		'featured_image'        => __( 'Project Cover Image', 'jeanne' ),
		'set_featured_image'    => __( 'Set cover image', 'jeanne' ),
		'remove_featured_image' => __( 'Remove cover image', 'jeanne' ),
		'use_featured_image'    => __( 'Use as cover image', 'jeanne' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'project' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-portfolio',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'show_in_rest'       => true,
	);

	register_post_type( 'project', $args );
}
add_action( 'init', 'jeanne_register_post_types' );

/**
 * Flush rewrite rules on theme activation.
 */
function jeanne_rewrite_flush() {
	jeanne_register_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'jeanne_rewrite_flush' );
