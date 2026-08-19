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

	return is_shop() || is_product_taxonomy() || is_product() || is_search() || is_page_template( 'template-wishlist.php' );
}
