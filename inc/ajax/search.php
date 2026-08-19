<?php
/**
 * AJAX: Live-Suche im Header-Offcanvas (§14). Sucht Titel/Inhalt sowie
 * optional SKU, liefert kompakte Produktdaten für die Vorschau.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

const BODYWINGS_LIVE_SEARCH_LIMIT = 6;

add_action( 'wp_ajax_bodywings_live_search', 'bodywings_ajax_live_search' );
add_action( 'wp_ajax_nopriv_bodywings_live_search', 'bodywings_ajax_live_search' );

function bodywings_ajax_live_search(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	$term = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';

	if ( mb_strlen( $term ) < 2 ) {
		wp_send_json_success( array( 'results' => array(), 'term' => $term ) );
	}

	$ids = bodywings_find_product_ids_for_search( $term, BODYWINGS_LIVE_SEARCH_LIMIT );

	$results = array();

	foreach ( $ids as $id ) {
		$product = wc_get_product( $id );

		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}

		$results[] = array(
			'id'        => $product->get_id(),
			'name'      => $product->get_name(),
			'permalink' => get_permalink( $product->get_id() ),
			'image'     => wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) ?: wc_placeholder_img_src( 'thumbnail' ),
			'priceHtml' => wp_strip_all_tags( $product->get_price_html() ),
			'sku'       => $product->get_sku(),
		);
	}

	wp_send_json_success( array(
		'results'  => $results,
		'term'     => $term,
		'moreUrl'  => add_query_arg( array( 's' => rawurlencode( $term ), 'post_type' => 'product' ), home_url( '/' ) ),
	) );
}

/**
 * Titel/Inhalt-Suche (WP-Standard) kombiniert mit optionaler SKU-Suche
 * (§14 "optional SKU") — beide Ergebnismengen zusammengeführt und auf
 * $limit begrenzt.
 *
 * @return int[]
 */
function bodywings_find_product_ids_for_search( string $term, int $limit ): array {
	global $wpdb;

	$title_query = new WP_Query( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		's'              => $term,
		'posts_per_page' => $limit,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );

	$ids = $title_query->posts;

	if ( count( $ids ) < $limit ) {
		$sku_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value LIKE %s LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				'%' . $wpdb->esc_like( $term ) . '%',
				$limit
			)
		);

		$ids = array_unique( array_merge( $ids, array_map( 'absint', $sku_ids ) ) );
	}

	return array_slice( $ids, 0, $limit );
}
