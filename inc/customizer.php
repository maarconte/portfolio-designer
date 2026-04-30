<?php
/**
 * Theme Customizer settings.
 *
 * @package Jeanne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jeanne_customizer_register( $wp_customize ) {

	// ─── Site Identity ──────────────────────────────────────────────────────────
	// (title & tagline already exist; we just tweak transport)
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	// ─── Portfolio Section ───────────────────────────────────────────────────────
	$wp_customize->add_section( 'jeanne_portfolio', array(
		'title'    => __( 'Portfolio Settings', 'jeanne' ),
		'priority' => 30,
	) );

	// Slider autoplay delay
	$wp_customize->add_setting( 'jeanne_autoplay_delay', array(
		'default'           => 4000,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'jeanne_autoplay_delay', array(
		'label'       => __( 'Slider Autoplay Delay (ms)', 'jeanne' ),
		'description' => __( 'Time between automatic slide transitions. Set to 0 to disable autoplay.', 'jeanne' ),
		'section'     => 'jeanne_portfolio',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 15000,
			'step' => 500,
		),
	) );

	// Projects per page (slider)
	$wp_customize->add_setting( 'jeanne_projects_count', array(
		'default'           => -1,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'jeanne_projects_count', array(
		'label'       => __( 'Number of Projects to Display', 'jeanne' ),
		'description' => __( 'Enter -1 to show all projects.', 'jeanne' ),
		'section'     => 'jeanne_portfolio',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => -1,
			'max'  => 100,
			'step' => 1,
		),
	) );

	// Footer text
	$wp_customize->add_setting( 'jeanne_footer_text', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_post',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'jeanne_footer_text', array(
		'label'   => __( 'Footer Text', 'jeanne' ),
		'section' => 'jeanne_portfolio',
		'type'    => 'textarea',
	) );

	// ─── Typography Section ───────────────────────────────────────────────────────
	$wp_customize->add_section( 'jeanne_typography', array(
		'title'    => __( 'Typography', 'jeanne' ),
		'priority' => 35,
	) );

	$wp_customize->add_setting( 'jeanne_google_font', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'jeanne_google_font', array(
		'label'       => __( 'Google Font Name', 'jeanne' ),
		'description' => __( 'Enter a Google Fonts name (e.g. "DM Sans"). Leave blank to use system fonts.', 'jeanne' ),
		'section'     => 'jeanne_typography',
		'type'        => 'text',
	) );

	// ─── Random Logos Section ────────────────────────────────────────────────────
	$wp_customize->add_section( 'jeanne_random_logos', array(
		'title'       => __( 'Logos aléatoires', 'jeanne' ),
		'description' => __( 'Uploadez jusqu\'à 5 logos. Un sera choisi aléatoirement à chaque chargement de page. Laissez les emplacements vides pour en utiliser moins.', 'jeanne' ),
		'priority'    => 25,
	) );

	$wp_customize->add_setting( 'jeanne_random_logos_enabled', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'jeanne_random_logos_enabled', array(
		'label'   => __( 'Activer les logos aléatoires', 'jeanne' ),
		'section' => 'jeanne_random_logos',
		'type'    => 'checkbox',
	) );

	for ( $i = 1; $i <= 5; $i++ ) {
		$wp_customize->add_setting( 'jeanne_random_logo_' . $i, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'jeanne_random_logo_' . $i,
				array(
					'label'    => sprintf( __( 'Logo %d', 'jeanne' ), $i ),
					'section'  => 'jeanne_random_logos',
					'settings' => 'jeanne_random_logo_' . $i,
				)
			)
		);
	}
}
add_action( 'customize_register', 'jeanne_customizer_register' );

function jeanne_get_random_logos() {
	$logos = array();
	for ( $i = 1; $i <= 5; $i++ ) {
		$url = get_theme_mod( 'jeanne_random_logo_' . $i, '' );
		if ( $url ) {
			$logos[] = esc_url( $url );
		}
	}
	return $logos;
}

/**
 * Selective refresh bindings for the Customizer live preview.
 */
function jeanne_customizer_preview_js() {
	wp_enqueue_script(
		'jeanne-customizer-preview',
		JEANNE_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		JEANNE_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'jeanne_customizer_preview_js' );

/**
 * Output dynamic CSS for Google Font choice.
 */
function jeanne_dynamic_styles() {
	$font = get_theme_mod( 'jeanne_google_font', '' );

	if ( $font ) {
		$font_url = 'https://fonts.googleapis.com/css2?family=' . urlencode( $font ) . ':wght@300;400;500&display=swap';
		echo '<link rel="stylesheet" href="' . esc_url( $font_url ) . '">' . "\n";
		echo '<style>:root{--jeanne-font:' . esc_html( $font ) . ',system-ui,sans-serif}</style>' . "\n";
	}
}
add_action( 'wp_head', 'jeanne_dynamic_styles' );
