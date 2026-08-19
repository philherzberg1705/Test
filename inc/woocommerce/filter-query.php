<?php
/**
 * Gemeinsame Filter-Query-Logik (§13) — von der SSR-Hauptabfrage
 * (pre_get_posts, für Deep-Links/SEO/URL-State) UND vom AJAX-Handler
 * genutzt, damit beide garantiert dieselben Ergebnisse liefern (§33).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

/**
 * Liest Filterwerte aus $_REQUEST — funktioniert damit gleichermaßen für
 * einen normalen Seitenaufruf mit Query-String (?bw_cat=...) und für den
 * AJAX-POST-Request.
 */
function bodywings_get_filter_request_params(): array {
	$params = array(
		'cat'        => array(),
		'min_price'  => null,
		'max_price'  => null,
		'in_stock'   => ! empty( $_REQUEST['bw_in_stock'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		'attributes' => array(),
	);

	if ( ! empty( $_REQUEST['bw_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$params['cat'] = array_map( 'sanitize_title', (array) wp_unslash( $_REQUEST['bw_cat'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( isset( $_REQUEST['bw_min_price'] ) && '' !== $_REQUEST['bw_min_price'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$params['min_price'] = (float) $_REQUEST['bw_min_price']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( isset( $_REQUEST['bw_max_price'] ) && '' !== $_REQUEST['bw_max_price'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$params['max_price'] = (float) $_REQUEST['bw_max_price']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	foreach ( bodywings_get_filter_attributes() as $taxonomy ) {
		if ( ! empty( $_REQUEST[ $taxonomy ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$params['attributes'][ $taxonomy ] = array_map( 'sanitize_title', (array) wp_unslash( $_REQUEST[ $taxonomy ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

	return $params;
}

function bodywings_filter_has_active_params( array $params ): bool {
	return ! empty( $params['cat'] )
		|| ! empty( $params['attributes'] )
		|| null !== $params['min_price']
		|| null !== $params['max_price']
		|| $params['in_stock'];
}

/**
 * @return array<int,array> tax_query-Teilbedingungen ohne "relation"-Wrapper.
 */
function bodywings_build_filter_tax_query_parts( array $params ): array {
	$parts = array();

	if ( ! empty( $params['cat'] ) ) {
		$parts[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $params['cat'],
		);
	}

	foreach ( $params['attributes'] as $taxonomy => $terms ) {
		if ( $terms ) {
			$parts[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $terms,
			);
		}
	}

	return $parts;
}

/**
 * @return array<int,array> meta_query-Teilbedingungen ohne "relation"-Wrapper.
 */
function bodywings_build_filter_meta_query_parts( array $params ): array {
	$parts = array();

	if ( null !== $params['min_price'] || null !== $params['max_price'] ) {
		$price_query = array(
			'key'  => '_price',
			'type' => 'DECIMAL(10,2)',
		);

		if ( null !== $params['min_price'] && null !== $params['max_price'] ) {
			$price_query['value']   = array( $params['min_price'], $params['max_price'] );
			$price_query['compare'] = 'BETWEEN';
		} elseif ( null !== $params['min_price'] ) {
			$price_query['value']   = $params['min_price'];
			$price_query['compare'] = '>=';
		} else {
			$price_query['value']   = $params['max_price'];
			$price_query['compare'] = '<=';
		}

		$parts[] = $price_query;
	}

	if ( $params['in_stock'] ) {
		$parts[] = array(
			'key'     => '_stock_status',
			'value'   => 'instock',
			'compare' => '=',
		);
	}

	return $parts;
}

add_action( 'pre_get_posts', 'bodywings_apply_filter_to_main_query' );

function bodywings_apply_filter_to_main_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}

	$params = bodywings_get_filter_request_params();

	$tax_parts = bodywings_build_filter_tax_query_parts( $params );

	if ( $tax_parts ) {
		$existing = $query->get( 'tax_query' );
		$combined = $existing ? array_merge( array( $existing ), $tax_parts ) : $tax_parts;
		$query->set( 'tax_query', array_merge( array( 'relation' => 'AND' ), $combined ) );
	}

	$meta_parts = bodywings_build_filter_meta_query_parts( $params );

	if ( $meta_parts ) {
		$existing_meta = $query->get( 'meta_query' );
		$combined_meta = $existing_meta ? array_merge( array( $existing_meta ), $meta_parts ) : $meta_parts;
		$query->set( 'meta_query', array_merge( array( 'relation' => 'AND' ), $combined_meta ) );
	}
}
