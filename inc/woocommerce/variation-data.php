<?php
/**
 * Liefert Attributbilder für die als "visuell" konfigurierten Attribute
 * (§8.2 product_page_attributes) an das Frontend, damit variations.js die
 * nativen WooCommerce-<select>-Varianten-Felder progressiv zu
 * Bild-Swatches erweitern kann, ohne WC-Templates zu überschreiben (§31).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

/**
 * @return array<string,array<string,string>> Taxonomie => [Term-Slug => Bild-URL]
 */
function bodywings_get_visual_attribute_images_map(): array {
	$map = array();

	foreach ( bodywings_get_product_page_attributes() as $taxonomy ) {
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );

		if ( is_wp_error( $terms ) || ! $terms ) {
			continue;
		}

		$map[ $taxonomy ] = array();

		foreach ( $terms as $term ) {
			$url = bodywings_get_attribute_term_image_url( $term->term_id, 'thumbnail' );

			if ( $url ) {
				$map[ $taxonomy ][ $term->slug ] = $url;
			}
		}
	}

	return $map;
}
