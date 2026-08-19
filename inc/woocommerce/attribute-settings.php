<?php
/**
 * §8.2: Im Backend konfigurierbar, welche WooCommerce-Attribute für
 * Produktkarten, Filter und die visuelle Darstellung auf der Produktseite
 * verwendet werden. Eine Option, drei Listen von Attribut-Taxonomie-Slugs
 * (z.B. "pa_farbe").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_ATTRIBUTE_SETTINGS_OPTION = 'bodywings_attribute_settings';

function bodywings_get_attribute_settings(): array {
	$defaults = array(
		'card_attributes'         => array(),
		'filter_attributes'       => array(),
		'product_page_attributes' => array(),
	);

	return wp_parse_args( get_option( BODYWINGS_ATTRIBUTE_SETTINGS_OPTION, array() ), $defaults );
}

function bodywings_get_card_attributes(): array {
	return bodywings_get_attribute_settings()['card_attributes'];
}

function bodywings_get_filter_attributes(): array {
	return bodywings_get_attribute_settings()['filter_attributes'];
}

function bodywings_get_product_page_attributes(): array {
	return bodywings_get_attribute_settings()['product_page_attributes'];
}

/**
 * @return array<string,string> taxonomy slug (pa_x) => Label
 */
function bodywings_get_available_attribute_taxonomies(): array {
	if ( ! bodywings_is_woocommerce_active() ) {
		return array();
	}

	$taxonomies = array();

	foreach ( wc_get_attribute_taxonomies() as $attribute ) {
		$taxonomies[ wc_attribute_taxonomy_name( $attribute->attribute_name ) ] = $attribute->attribute_label;
	}

	return $taxonomies;
}

add_action( 'admin_init', 'bodywings_register_attribute_settings' );

function bodywings_register_attribute_settings(): void {
	register_setting( 'bodywings_attribute_settings_group', BODYWINGS_ATTRIBUTE_SETTINGS_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'bodywings_sanitize_attribute_settings',
		'default'           => array(
			'card_attributes'         => array(),
			'filter_attributes'       => array(),
			'product_page_attributes' => array(),
		),
	) );
}

function bodywings_sanitize_attribute_settings( $value ): array {
	$available = array_keys( bodywings_get_available_attribute_taxonomies() );
	$clean     = array();

	foreach ( array( 'card_attributes', 'filter_attributes', 'product_page_attributes' ) as $key ) {
		$submitted        = isset( $value[ $key ] ) && is_array( $value[ $key ] ) ? $value[ $key ] : array();
		$clean[ $key ]    = array_values( array_intersect( $available, array_map( 'sanitize_text_field', $submitted ) ) );
	}

	return $clean;
}
