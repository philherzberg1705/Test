<?php
/**
 * Kontext-Helfer für bedingtes Asset-Laden (§26: nur benötigte Assets).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True auf jeder Seite, die Produktkarten im Grid zeigt: Shop, Kategorie/
 * Tag-Archiv, Suche, Produktseite (verwandte Produkte) und die Wishlist-
 * Seite (§7 nutzt dieselbe Produktkarte).
 */
function bodywings_is_product_grid_context(): bool {
	if ( ! bodywings_is_woocommerce_active() ) {
		return false;
	}

	if ( is_shop() || is_product_taxonomy() || is_product() || is_search() || is_page_template( 'template-wishlist.php' ) ) {
		return true;
	}

	// Produktkarten kommen auch außerhalb der klassischen Shop-Kontexte vor,
	// sobald eine Seite den Produkt-Slider-Block enthält (§5/§26: nur dann
	// laden, wenn der Block tatsächlich auf der Seite vorkommt).
	return is_singular() && has_block( 'bw/product-slider' );
}
