<?php
/**
 * Rendering-Helfer für Filter-Panel, Produkt-Grid und Pagination — von
 * archive-product.php (SSR) und inc/ajax/filter.php (AJAX-Refresh)
 * gemeinsam genutzt (§33).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

add_action( 'bodywings_shop_before_grid', 'bodywings_render_filter_panel' );

function bodywings_render_filter_panel(): void {
	$categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
	) );

	if ( is_wp_error( $categories ) ) {
		$categories = array();
	}

	$attribute_groups = array();

	foreach ( bodywings_get_filter_attributes() as $taxonomy ) {
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );

		if ( is_wp_error( $terms ) || ! $terms ) {
			continue;
		}

		$attribute = wc_get_attribute( wc_attribute_id( str_replace( 'pa_', '', $taxonomy ) ) );

		$attribute_groups[] = array(
			'taxonomy' => $taxonomy,
			'label'    => $attribute ? $attribute->name : $taxonomy,
			'terms'    => $terms,
		);
	}

	$params  = bodywings_get_filter_request_params();
	$context = array();

	if ( is_product_taxonomy() ) {
		$queried = get_queried_object();

		if ( $queried instanceof WP_Term ) {
			$context = array( 'taxonomy' => $queried->taxonomy, 'term_id' => $queried->term_id );
		}
	}

	get_template_part( 'template-parts/woocommerce/filter-panel', null, array(
		'categories'        => $categories,
		'attribute_groups'  => $attribute_groups,
		'params'            => $params,
		'context'           => $context,
	) );
}

/**
 * @return string HTML der <li>-Produktkarten (ohne umschließendes <ul>).
 */
function bodywings_render_products_grid_html( WP_Query $query ): string {
	if ( ! $query->have_posts() ) {
		ob_start();
		wc_get_template( 'loop/no-products-found.php' );
		return ob_get_clean();
	}

	ob_start();

	while ( $query->have_posts() ) {
		$query->the_post();
		wc_get_template_part( 'content', 'product' );
	}

	wp_reset_postdata();

	return ob_get_clean();
}

function bodywings_render_pagination_html( WP_Query $query, int $paged ): string {
	if ( $query->max_num_pages <= 1 ) {
		return '';
	}

	$links = paginate_links( array(
		'total'     => $query->max_num_pages,
		'current'   => $paged,
		'prev_text' => __( '‹', 'bodywings' ),
		'next_text' => __( '›', 'bodywings' ),
		'type'      => 'array',
	) );

	if ( ! $links ) {
		return '';
	}

	$html = '<nav class="woocommerce-pagination" aria-label="' . esc_attr__( 'Seiten', 'bodywings' ) . '"><ul>';

	foreach ( $links as $link ) {
		$html .= '<li>' . str_replace( '<a ', '<a data-bw-filter-page ', $link ) . '</li>';
	}

	$html .= '</ul></nav>';

	return $html;
}
