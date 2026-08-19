<?php
/**
 * AJAX: Produktfilter (§13/§15). Baut dieselbe Query wie die SSR-
 * pre_get_posts-Filterung (inc/woocommerce/filter-query.php), damit
 * gefilterte Links und AJAX-Ergebnis identisch sind.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

add_action( 'wp_ajax_bodywings_filter_products', 'bodywings_ajax_filter_products' );
add_action( 'wp_ajax_nopriv_bodywings_filter_products', 'bodywings_ajax_filter_products' );

function bodywings_ajax_filter_products(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	$params = bodywings_get_filter_request_params();
	$paged  = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;

	$tax_parts = bodywings_build_filter_tax_query_parts( $params );

	if ( ! empty( $_POST['term_id'] ) && ! empty( $_POST['taxonomy'] ) ) {
		$tax_parts[] = array(
			'taxonomy' => sanitize_key( wp_unslash( $_POST['taxonomy'] ) ),
			'field'    => 'term_id',
			'terms'    => absint( $_POST['term_id'] ),
		);
	}

	$orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : '';

	// Dieselbe Sortierauflösung wie WooCommerce selbst (Preis/Bewertung/
	// Neuheit brauchen z.B. einen meta_key) — §31 statt eigener Logik.
	$ordering_args = WC()->query->get_catalog_ordering_args( $orderby );

	$query_args = array_merge(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => wc_get_default_products_per_row() * wc_get_default_product_rows_per_page(),
			'paged'          => $paged,
		),
		$ordering_args
	);

	if ( $tax_parts ) {
		$query_args['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax_parts ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$meta_parts = bodywings_build_filter_meta_query_parts( $params );

	if ( $meta_parts ) {
		$query_args['meta_query'] = array_merge( array( 'relation' => 'AND' ), $meta_parts ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}

	$query = new WP_Query( $query_args );

	wp_send_json_success( array(
		'html'       => bodywings_render_products_grid_html( $query ),
		'pagination' => bodywings_render_pagination_html( $query, $paged ),
		'count'      => (int) $query->found_posts,
		'countText'  => sprintf(
			/* translators: %d: number of products found */
			_n( '%d Produkt', '%d Produkte', (int) $query->found_posts, 'bodywings' ),
			(int) $query->found_posts
		),
	) );
}
