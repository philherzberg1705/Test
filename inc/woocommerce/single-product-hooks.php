<?php
/**
 * Reihenfolge/Inhalt der Produktseiten-Summary an §12 anpassen, ohne die
 * bewährten WooCommerce-Funktionen selbst nachzubauen (§31): Titel,
 * Bewertung, Preis, Add-to-Cart, Tabs (Beschreibung/Zusatzinfo/
 * Bewertungen) und verwandte Produkte bleiben WooCommerce-Kernfunktionen,
 * hier wird nur Reihenfolge/Ergänzung gesteuert.
 *
 * Ziel-Reihenfolge: Titel → Kategorie → Bewertung → Kurzbeschreibung →
 * Preis → Varianten/Add-to-Cart → Wishlist → Versandinfo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

add_action( 'init', 'bodywings_adjust_single_product_summary_hooks' );

function bodywings_adjust_single_product_summary_hooks(): void {
	// Preis stand default vor der Kurzbeschreibung — §12 will die
	// Kurzbeschreibung zuerst.
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );

	// Meta-Zeile (SKU/Kategorien/Tags) entfällt — Kategorie wird bewusst
	// separat und prominenter dargestellt (§1: keine unnötigen UI-Teile).
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

	add_action( 'woocommerce_single_product_summary', 'bodywings_template_single_category', 6 );
	add_action( 'woocommerce_single_product_summary', 'bodywings_template_single_wishlist_button', 32 );
	add_action( 'woocommerce_single_product_summary', 'bodywings_template_single_shipping_info', 45 );
}

function bodywings_template_single_category(): void {
	global $product;

	$categories = wc_get_product_category_list( $product->get_id(), ', ', '<p class="bw-product-page__category">', '</p>' );

	if ( $categories ) {
		echo wp_kses_post( $categories ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_get_product_category_list() escaped intern.
	}
}

function bodywings_template_single_wishlist_button(): void {
	global $product;

	get_template_part( 'template-parts/woocommerce/wishlist-button', null, array(
		'product_id' => $product->get_id(),
	) );
}

function bodywings_template_single_shipping_info(): void {
	$text = apply_filters(
		'bodywings_shipping_info_text',
		__( 'Versandkostenfrei ab 80 € · Lieferzeit 2–4 Werktage', 'bodywings' )
	);

	if ( ! $text ) {
		return;
	}
	?>
	<p class="bw-product-page__shipping-info">
		<?php echo bodywings_icon( 'truck' ); // phpcs:ignore -- statisches SVG als dezentes Versand-Icon ?>
		<?php echo esc_html( $text ); ?>
	</p>
	<?php
}
