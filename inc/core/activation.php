<?php
/**
 * Einmalige Einrichtung bei Theme-Aktivierung — "gute Defaults" statt
 * manueller Einrichtung durch den Nutzer (§29).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', 'bodywings_maybe_create_wishlist_page' );

function bodywings_maybe_create_wishlist_page(): void {
	if ( ! bodywings_is_woocommerce_active() ) {
		return;
	}

	if ( get_page_by_path( 'wunschliste' ) ) {
		return;
	}

	wp_insert_post( array(
		'post_title'   => __( 'Wunschliste', 'bodywings' ),
		'post_name'    => 'wunschliste',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => '',
		'page_template' => 'template-wishlist.php',
	) );
}
