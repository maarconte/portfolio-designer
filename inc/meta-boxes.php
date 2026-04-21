<?php
/**
 * Project gallery meta box.
 *
 * @package Jeanne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jeanne_add_meta_boxes() {
	add_meta_box(
		'jeanne_project_gallery',
		__( 'Project Gallery', 'jeanne' ),
		'jeanne_render_gallery_meta_box',
		'project',
		'normal',
		'high'
	);

	add_meta_box(
		'jeanne_project_details',
		__( 'Project Details', 'jeanne' ),
		'jeanne_render_details_meta_box',
		'project',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'jeanne_add_meta_boxes' );

function jeanne_render_gallery_meta_box( $post ) {
	wp_nonce_field( 'jeanne_save_gallery', 'jeanne_gallery_nonce' );

	$gallery_ids = get_post_meta( $post->ID, '_jeanne_gallery', true );
	if ( ! is_array( $gallery_ids ) ) {
		$gallery_ids = array();
	}

	$ids_string = implode( ',', $gallery_ids );
	?>
	<div class="jeanne-gallery-metabox">
		<p class="description"><?php esc_html_e( 'Select images for the project gallery. These appear in the lightbox when a visitor clicks the project. The featured image is used as the card thumbnail.', 'jeanne' ); ?></p>

		<div class="jeanne-gallery-preview" id="jeanne-gallery-preview">
			<?php foreach ( $gallery_ids as $attachment_id ) : ?>
				<?php
				$thumb = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
				if ( ! $thumb ) {
					continue;
				}
				?>
				<div class="jeanne-gallery-item" data-id="<?php echo esc_attr( $attachment_id ); ?>">
					<img src="<?php echo esc_url( $thumb[0] ); ?>" alt="">
					<button type="button" class="jeanne-gallery-remove" title="<?php esc_attr_e( 'Remove', 'jeanne' ); ?>">&#x2715;</button>
				</div>
			<?php endforeach; ?>
		</div>

		<input
			type="hidden"
			name="jeanne_gallery_ids"
			id="jeanne-gallery-ids"
			value="<?php echo esc_attr( $ids_string ); ?>"
		>

		<p>
			<button type="button" class="button button-primary" id="jeanne-gallery-button">
				<?php esc_html_e( 'Add / Edit Gallery Images', 'jeanne' ); ?>
			</button>
			<button type="button" class="button" id="jeanne-gallery-clear">
				<?php esc_html_e( 'Clear Gallery', 'jeanne' ); ?>
			</button>
		</p>
	</div>

	<style>
		.jeanne-gallery-metabox .description { margin-bottom: 12px; color: #666; }
		.jeanne-gallery-preview { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; min-height: 40px; padding: 8px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 3px; }
		.jeanne-gallery-item { position: relative; width: 80px; height: 80px; }
		.jeanne-gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; border: 1px solid #ddd; }
		.jeanne-gallery-remove { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; background: #cc0000; color: #fff; border: none; border-radius: 50%; cursor: pointer; font-size: 10px; line-height: 1; padding: 0; display: flex; align-items: center; justify-content: center; }
		.jeanne-gallery-remove:hover { background: #990000; }
	</style>
	<?php
}

function jeanne_render_details_meta_box( $post ) {
	wp_nonce_field( 'jeanne_save_details', 'jeanne_details_nonce' );

	$year     = get_post_meta( $post->ID, '_jeanne_year', true );
	$client   = get_post_meta( $post->ID, '_jeanne_client', true );
	$category = get_post_meta( $post->ID, '_jeanne_category', true );
	?>
	<p>
		<label for="jeanne-year"><strong><?php esc_html_e( 'Year', 'jeanne' ); ?></strong></label><br>
		<input type="number" id="jeanne-year" name="jeanne_year" value="<?php echo esc_attr( $year ); ?>" class="widefat" placeholder="<?php echo esc_attr( date( 'Y' ) ); ?>">
	</p>
	<p>
		<label for="jeanne-client"><strong><?php esc_html_e( 'Client', 'jeanne' ); ?></strong></label><br>
		<input type="text" id="jeanne-client" name="jeanne_client" value="<?php echo esc_attr( $client ); ?>" class="widefat">
	</p>
	<p>
		<label for="jeanne-category"><strong><?php esc_html_e( 'Category', 'jeanne' ); ?></strong></label><br>
		<input type="text" id="jeanne-category" name="jeanne_category" value="<?php echo esc_attr( $category ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Branding, Print, Digital…', 'jeanne' ); ?>">
	</p>
	<?php
}

function jeanne_save_meta_boxes( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Gallery
	if (
		isset( $_POST['jeanne_gallery_nonce'] ) &&
		wp_verify_nonce( sanitize_key( $_POST['jeanne_gallery_nonce'] ), 'jeanne_save_gallery' )
	) {
		$raw_ids = isset( $_POST['jeanne_gallery_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['jeanne_gallery_ids'] ) ) : '';

		if ( '' === $raw_ids ) {
			delete_post_meta( $post_id, '_jeanne_gallery' );
		} else {
			$ids = array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) );
			update_post_meta( $post_id, '_jeanne_gallery', $ids );
		}
	}

	// Details
	if (
		isset( $_POST['jeanne_details_nonce'] ) &&
		wp_verify_nonce( sanitize_key( $_POST['jeanne_details_nonce'] ), 'jeanne_save_details' )
	) {
		$fields = array(
			'jeanne_year'     => '_jeanne_year',
			'jeanne_client'   => '_jeanne_client',
			'jeanne_category' => '_jeanne_category',
		);

		foreach ( $fields as $post_key => $meta_key ) {
			if ( isset( $_POST[ $post_key ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) );
				if ( '' === $value ) {
					delete_post_meta( $post_id, $meta_key );
				} else {
					update_post_meta( $post_id, $meta_key, $value );
				}
			}
		}
	}
}
add_action( 'save_post_project', 'jeanne_save_meta_boxes' );
