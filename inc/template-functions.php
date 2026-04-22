<?php
/**
 * Helper functions for the Jeanne theme.
 *
 * @package Jeanne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get project data for a specific post.
 *
 * @param int $post_id The post ID.
 * @return array The project data.
 */
function jeanne_get_project_data( $post_id ) {
	$title       = get_the_title( $post_id );
	$content     = get_post( $post_id )->post_content;
	$description = wp_strip_all_tags( $content );
	$permalink   = get_permalink( $post_id );
	$year        = get_post_meta( $post_id, '_jeanne_year', true );
	$client      = get_post_meta( $post_id, '_jeanne_client', true );
	$category    = get_post_meta( $post_id, '_jeanne_category', true );

	// Gallery images
	$gallery_ids    = get_post_meta( $post_id, '_jeanne_gallery', true );
	$gallery_images = array();

	if ( is_array( $gallery_ids ) && ! empty( $gallery_ids ) ) {
		foreach ( $gallery_ids as $img_id ) {
			$full = wp_get_attachment_image_src( $img_id, 'jeanne-modal' );
			$alt  = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

			if ( $full ) {
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
	if ( empty( $gallery_images ) && has_post_thumbnail( $post_id ) ) {
		$thumb_id = get_post_thumbnail_id( $post_id );
		$full     = wp_get_attachment_image_src( $thumb_id, 'jeanne-modal' );
		$alt      = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
		if ( $full ) {
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

	return array(
		'id'          => $post_id,
		'title'       => $title,
		'description' => $description,
		'year'        => $year,
		'client'      => $client,
		'category'    => $category,
		'gallery'     => $gallery_images,
		'permalink'   => $permalink,
	);
}
