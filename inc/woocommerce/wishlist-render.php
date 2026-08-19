<?php
/**
 * Globale Wishlist (§7) — kein separates Plugin. Eingeloggt: dauerhaft in
 * User-Meta. Gast: LocalStorage (siehe assets/js/ajax/wishlist.js); der
 * Server kennt Gäste-Wishlists grundsätzlich nicht und rendert für sie
 * daher immer "nicht aktiv" — JS korrigiert das nach dem Laden.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

const BODYWINGS_WISHLIST_META_KEY = 'bodywings_wishlist';

/**
 * @return int[]
 */
function bodywings_get_user_wishlist_ids( int $user_id = 0 ): array {
	$user_id = $user_id ?: get_current_user_id();

	if ( ! $user_id ) {
		return array();
	}

	$ids = get_user_meta( $user_id, BODYWINGS_WISHLIST_META_KEY, true );

	return is_array( $ids ) ? array_values( array_unique( array_map( 'absint', $ids ) ) ) : array();
}

function bodywings_set_user_wishlist_ids( int $user_id, array $ids ): void {
	update_user_meta( $user_id, BODYWINGS_WISHLIST_META_KEY, array_values( array_unique( array_map( 'absint', $ids ) ) ) );
}

function bodywings_is_product_in_wishlist( int $product_id, int $user_id = 0 ): bool {
	if ( ! is_user_logged_in() && ! $user_id ) {
		return false;
	}

	return in_array( $product_id, bodywings_get_user_wishlist_ids( $user_id ), true );
}

add_filter( 'bodywings_wishlist_count', 'bodywings_filter_wishlist_count' );

function bodywings_filter_wishlist_count( int $count ): int {
	return is_user_logged_in() ? count( bodywings_get_user_wishlist_ids() ) : $count;
}
