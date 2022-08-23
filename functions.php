<?php
/**
 * Narrative Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Narrative
 * @since Narrative 1.0
 */

if ( ! function_exists( 'wp_narrative_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since WP Narrative 1.0
	 *
	 * @return void
	 */
	function wp_narrative_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

		// Register WooCommerce theme features.
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 400,
				'single_image_width'    => 600,
			)
		);

	}

endif;

add_action( 'after_setup_theme', 'wp_narrative_support' );

if ( ! function_exists( 'wp_narrative_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since WP Narrative 1.0
	 *
	 * @return void
	 */
	function wp_narrative_styles() {

		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'wp-narrative-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'wp-narrative-style' );

	}

endif;

add_action( 'wp_enqueue_scripts', 'wp_narrative_styles' );

/**
 * Registers pattern categories.
 *
 * @since WP Narrative 1.0
 *
 * @return void
 */
function wp_narrative_register_pattern_categories() {
	$block_pattern_categories = array(
		'header' => array( 'label' => __( 'Headers', 'wp_narrative' ) ),
		'footer' => array( 'label' => __( 'Footers', 'wp_narrative' ) ),
	);

	/**
	 * Filters the theme block pattern categories.
	 *
	 * @since WP Narrative 1.0
	 *
	 * @param array[] $block_pattern_categories {
	 *     An associative array of block pattern categories, keyed by category name.
	 *
	 *     @type array[] $properties {
	 *         An array of block category properties.
	 *
	 *         @type string $label A human-readable label for the pattern category.
	 *     }
	 * }
	 */
	$block_pattern_categories = apply_filters( 'wp_narrative_block_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}

}
add_action( 'init', 'wp_narrative_register_pattern_categories', 9 );

if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	// This theme does not have a traditional sidebar.
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	/**
	 * Alter the queue for WooCommerce styles and scripts.
	 *
	 * @since WP Narrative 1.0.5
	 *
	 * @param array $styles Array of registered styles.
	 *
	 * @return array
	 */
	function wp_narrative_woocommerce_enqueue_styles( $styles ) {
		// Get a theme version for cache busting.
		$theme_version = wp_get_theme()->get( 'Version' );
		$version_string = is_string( $theme_version ) ? $theme_version : false;

		// Add WP Narrative's WooCommerce styles.
		$styles['wp-narrative-woocommerce'] = array(
			'src'     => get_template_directory_uri() . '/assets/css/woocommerce.css',
			'deps'    => '',
			'version' => $version_string,
			'media'   => 'all',
			'has_rtl' => true,
		);

		return apply_filters( 'woocommerce_wp_narrative_styles', $styles );
	}
	add_filter( 'woocommerce_enqueue_styles', 'wp_narrative_woocommerce_enqueue_styles' );
}
