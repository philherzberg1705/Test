<?php
/**
 * Theme-Setup: Support-Features, Menüs, Bildgrößen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'bodywings_setup' );

function bodywings_setup(): void {
	load_theme_textdomain( 'bodywings', BODYWINGS_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	// WooCommerce-Kernsupport; eigenes Template-Set folgt in inc/woocommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'Hauptmenü', 'bodywings' ),
		'mobile'  => __( 'Mobiles Menü', 'bodywings' ),
		'footer'  => __( 'Footer-Menü', 'bodywings' ),
	) );

	add_image_size( 'bodywings-product-card', 640, 640, true );
	add_image_size( 'bodywings-product-zoom', 1600, 1600, false );
}

/**
 * Deaktiviert WooCommerce-Standard-Layoutwrapper, da eigene
 * Template-Struktur (inc/woocommerce, woocommerce/) verwendet wird.
 */
add_action( 'after_setup_theme', function (): void {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
} );
