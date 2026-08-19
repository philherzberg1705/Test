<?php
/**
 * Produktkarte (§8): Bild → Varianten-Auswahl → Titel → Preis, bewusst
 * ohne weitere Informationen. Datenaufbereitung hier, Markup in
 * template-parts/woocommerce/product-card.php — so kann die Karte auch
 * außerhalb des Shop-Loops (z.B. "ähnliche Produkte" in Task 8)
 * wiederverwendet werden.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_render_product_card( WC_Product $product ): void {
	get_template_part( 'template-parts/woocommerce/product-card', null, array(
		'product' => $product,
		'card'    => bodywings_get_product_card_data( $product ),
	) );
}

function bodywings_get_product_card_data( WC_Product $product ): array {
	$primary_image_id   = (int) $product->get_image_id();
	$secondary_image_id = bodywings_get_product_secondary_image_id( $product );

	return array(
		'primary_image_id'   => $primary_image_id,
		'primary_image_src'  => wp_get_attachment_image_url( $primary_image_id, 'bodywings-product-card' ),
		'primary_transparent' => bodywings_attachment_has_transparency( $primary_image_id ),
		'secondary_image_id' => $secondary_image_id,
		'secondary_image_src' => $secondary_image_id ? wp_get_attachment_image_url( $secondary_image_id, 'bodywings-product-card' ) : '',
		'variants'           => bodywings_get_product_card_variants( $product ),
	);
}

/**
 * Swatches für das erste konfigurierte Karten-Attribut (§8.1/§8.2/§9).
 * Nur EIN Attribut steuert den Bildwechsel auf der Karte — typischerweise
 * Farbe. Weitere in den Karten-Einstellungen gewählte Attribute sind für
 * Filter/Produktseite relevant, nicht für den Karten-Bildwechsel selbst.
 *
 * @return array<int,array{term_id:int,name:string,swatch_image:string,is_active:bool,variant_image:string}>
 */
function bodywings_get_product_card_variants( WC_Product $product ): array {
	if ( ! $product->is_type( 'variable' ) ) {
		return array();
	}

	$card_attributes = bodywings_get_card_attributes();

	if ( empty( $card_attributes ) ) {
		return array();
	}

	$taxonomy = $card_attributes[0];

	if ( ! $product->get_attribute( $taxonomy ) ) {
		return array();
	}

	$terms = wc_get_product_terms( $product->get_id(), $taxonomy, array( 'fields' => 'all' ) );

	if ( ! $terms ) {
		return array();
	}

	$variation_images = bodywings_get_variation_images_by_attribute( $product, $taxonomy );
	$fallback_image    = wp_get_attachment_image_url( $product->get_image_id(), 'bodywings-product-card' );
	$variants          = array();

	foreach ( $terms as $index => $term ) {
		$variants[] = array(
			'term_id'       => $term->term_id,
			'name'          => $term->name,
			'swatch_image'  => bodywings_get_attribute_term_image_url( $term->term_id, 'thumbnail' ),
			'is_active'     => 0 === $index,
			'variant_image' => $variation_images[ $term->slug ] ?? $fallback_image,
		);
	}

	return $variants;
}

/**
 * @return array<string,string> Attribut-Term-Slug => Variations-Bild-URL
 */
function bodywings_get_variation_images_by_attribute( WC_Product $product, string $taxonomy ): array {
	$map          = array();
	$attribute_key = 'attribute_' . $taxonomy;

	foreach ( $product->get_available_variations() as $variation_data ) {
		$term_slug = $variation_data['attributes'][ $attribute_key ] ?? '';

		if ( ! $term_slug || isset( $map[ $term_slug ] ) ) {
			continue;
		}

		$image_src = $variation_data['image']['src'] ?? '';

		if ( $image_src ) {
			$map[ $term_slug ] = $image_src;
		}
	}

	return $map;
}
