<?php
/**
 * Zentrale WooCommerce-Verfügbarkeitsprüfung + abgeleitete Helfer.
 * §28: Theme muss auch ohne WooCommerce sinnvoll funktionieren.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_is_woocommerce_active(): bool {
	return class_exists( 'WooCommerce' );
}

function bodywings_get_cart_url(): string {
	return bodywings_is_woocommerce_active() ? wc_get_cart_url() : home_url( '/' );
}

function bodywings_get_cart_count(): int {
	if ( ! bodywings_is_woocommerce_active() || ! WC()->cart ) {
		return 0;
	}

	return (int) WC()->cart->get_cart_contents_count();
}

function bodywings_get_account_url(): string {
	return bodywings_is_woocommerce_active()
		? wc_get_page_permalink( 'myaccount' )
		: wp_login_url();
}

/**
 * Wunschlisten-Seite: eigene Theme-Funktion (§7, kein Plugin), finale
 * Implementierung folgt in Task 13. Filterbar, falls das URL-Schema
 * projektspezifisch angepasst werden muss.
 */
function bodywings_get_wishlist_url(): string {
	return apply_filters( 'bodywings_wishlist_url', home_url( '/wunschliste/' ) );
}

function bodywings_get_search_url(): string {
	return home_url( '/' );
}

/**
 * Platzhalter bis Task 13 (globale Wishlist) die echte Speicherung liefert.
 */
function bodywings_get_wishlist_count(): int {
	return (int) apply_filters( 'bodywings_wishlist_count', 0 );
}
