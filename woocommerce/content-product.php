<?php
/**
 * Override von WooCommerce/templates/content-product.php.
 * Bewusst radikal vereinfacht (§8: "extrem simpel aufgebaut") — kein
 * Standard-WooCommerce-Loop-Markup, stattdessen die eigene Produktkarte.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'bw-product-grid__item', $product ); ?>>
	<?php bodywings_render_product_card( $product ); ?>
</li>
