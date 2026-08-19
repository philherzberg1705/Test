<?php
/**
 * AJAX: Add-to-Cart, Mengenänderung, Entfernen (§15/§16). Ein gemeinsames
 * Response-Format für alle drei Aktionen (§33) statt WC's separatem
 * natives Add-to-Cart-Ajax-Format zu mischen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

add_action( 'wp_ajax_bodywings_add_to_cart', 'bodywings_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_bodywings_add_to_cart', 'bodywings_ajax_add_to_cart' );
add_action( 'wp_ajax_bodywings_update_cart_item', 'bodywings_ajax_update_cart_item' );
add_action( 'wp_ajax_nopriv_bodywings_update_cart_item', 'bodywings_ajax_update_cart_item' );
add_action( 'wp_ajax_bodywings_remove_cart_item', 'bodywings_ajax_remove_cart_item' );
add_action( 'wp_ajax_nopriv_bodywings_remove_cart_item', 'bodywings_ajax_remove_cart_item' );

function bodywings_ajax_add_to_cart(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	$product_id = isset( $_POST['add-to-cart'] ) ? absint( $_POST['add-to-cart'] ) : ( isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0 );
	$quantity   = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'Produkt nicht gefunden.', 'bodywings' ) ), 400 );
	}

	$variation = array();

	foreach ( $_POST as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce oben bereits geprüft.
		if ( str_starts_with( $key, 'attribute_' ) ) {
			$variation[ sanitize_key( $key ) ] = sanitize_text_field( wp_unslash( $value ) );
		}
	}

	$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );

	if ( ! $cart_item_key ) {
		$errors = wc_get_notices( 'error' );
		wc_clear_notices();

		wp_send_json_error( array(
			'message' => $errors ? wp_strip_all_tags( $errors[0]['notice'] ) : __( 'Produkt konnte nicht hinzugefügt werden.', 'bodywings' ),
		), 400 );
	}

	do_action( 'woocommerce_ajax_added_to_cart', $product_id );

	bodywings_send_mini_cart_response( __( 'Zum Warenkorb hinzugefügt.', 'bodywings' ) );
}

function bodywings_ajax_update_cart_item(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
	$quantity      = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 0;

	if ( ! $cart_item_key || ! isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Artikel nicht im Warenkorb gefunden.', 'bodywings' ) ), 400 );
	}

	if ( $quantity <= 0 ) {
		WC()->cart->remove_cart_item( $cart_item_key );
	} else {
		WC()->cart->set_quantity( $cart_item_key, $quantity, true );
	}

	bodywings_send_mini_cart_response();
}

function bodywings_ajax_remove_cart_item(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';

	if ( $cart_item_key ) {
		WC()->cart->remove_cart_item( $cart_item_key );
	}

	bodywings_send_mini_cart_response();
}

function bodywings_send_mini_cart_response( string $message = '' ): void {
	wp_send_json_success( array(
		'miniCartHtml' => bodywings_render_mini_cart_html(),
		'cartCount'    => bodywings_get_cart_count(),
		'message'      => $message,
	) );
}
