<?php
/**
 * Kontext-Helfer für bedingtes Asset-Laden (§26: nur benötigte Assets).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True auf jeder Seite, die Produktkarten im Grid zeigt: Shop, Kategorie/
 * Tag-Archiv, Suche, sowie die Produktseite selbst (verwandte Produkte).
 */
function bodywings_is_product_grid_context(): bool {
	if ( ! bodywings_is_woocommerce_active() ) {
		return false;
	}

	return is_shop() || is_product_taxonomy() || is_product() || is_search();
}
