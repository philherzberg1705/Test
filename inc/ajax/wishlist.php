<?php
/**
 * AJAX: Wishlist (§7). Nur für eingeloggte Nutzer:innen — Gäste togglen
 * rein clientseitig über localStorage (assets/js/ajax/wishlist.js), ein
 * Server-Roundtrip wäre dafür unnötig (§26).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

add_action( 'wp_ajax_bodywings_wishlist_toggle', 'bodywings_ajax_wishlist_toggle' );
add_action( 'wp_ajax_bodywings_wishlist_merge', 'bodywings_ajax_wishlist_merge' );
add_action( 'wp_ajax_bodywings_wishlist_get_products', 'bodywings_ajax_wishlist_get_products' );
add_action( 'wp_ajax_nopriv_bodywings_wishlist_get_products', 'bodywings_ajax_wishlist_get_products' );

function bodywings_ajax_wishlist_toggle(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Bitte einloggen.', 'bodywings' ) ), 401 );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

	if ( ! $product_id || ! get_post( $product_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Produkt nicht gefunden.', 'bodywings' ) ), 400 );
	}

	$user_id = get_current_user_id();
	$ids     = bodywings_get_user_wishlist_ids( $user_id );
	$index   = array_search( $product_id, $ids, true );

	if ( false === $index ) {
		$ids[]     = $product_id;
		$in_wishlist = true;
	} else {
		unset( $ids[ $index ] );
		$in_wishlist = false;
	}

	bodywings_set_user_wishlist_ids( $user_id, $ids );

	wp_send_json_success( array(
		'inWishlist' => $in_wishlist,
		'count'      => count( $ids ),
	) );
}

/**
 * Führt eine localStorage-Wishlist (Gast) nach dem Login mit der
 * serverseitigen zusammen (§7).
 */
function bodywings_ajax_wishlist_merge(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array(), 401 );
	}

	$incoming = isset( $_POST['ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) : array();
	$user_id  = get_current_user_id();
	$existing = bodywings_get_user_wishlist_ids( $user_id );

	$merged = array_values( array_unique( array_merge( $existing, array_filter( $incoming ) ) ) );

	bodywings_set_user_wishlist_ids( $user_id, $merged );

	wp_send_json_success( array( 'count' => count( $merged ) ) );
}

function bodywings_ajax_wishlist_get_products(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	$ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) : array();
	$ids = array_filter( $ids );

	if ( ! $ids ) {
		wp_send_json_success( array( 'html' => '' ) );
	}

	$query = new WP_Query( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'post__in'       => $ids,
		'orderby'        => 'post__in',
		'posts_per_page' => count( $ids ),
	) );

	wp_send_json_success( array( 'html' => bodywings_render_products_grid_html( $query ) ) );
}
