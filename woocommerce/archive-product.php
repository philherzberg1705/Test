<?php
/**
 * Override von WooCommerce/templates/archive-product.php.
 * Eigenes Grid + Filter-Sidebar-Layout statt Standard-WooCommerce-Loop-
 * Markup (§8/§13). Grid, Ergebniszähler und Pagination tragen IDs, über
 * die assets/js/ajax/filter.js sie nach einem AJAX-Filterwechsel ersetzt
 * — identisches Markup bei SSR und AJAX-Refresh (§33).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bw_paged = max( 1, (int) get_query_var( 'paged' ) );

// Eigene, AJAX-kompatible Pagination statt woocommerce_pagination() (§13).
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
?>

<div class="bw-container bw-shop">
	<header class="bw-shop__header">
		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
			<h1 class="bw-shop__title"><?php woocommerce_page_title(); ?></h1>
		<?php endif; ?>

		<?php do_action( 'woocommerce_archive_description' ); ?>
	</header>

	<div class="bw-shop__layout">
		<?php do_action( 'bodywings_shop_before_grid' ); ?>

		<div class="bw-shop__content">
			<?php if ( woocommerce_product_loop() ) : ?>

				<div class="bw-shop__toolbar">
					<p class="bw-shop__result-count bw-text-small bw-color-muted" data-bw-shop-result-count aria-live="polite">
						<?php woocommerce_result_count(); ?>
					</p>
					<div class="bw-shop__ordering">
						<?php woocommerce_catalog_ordering(); ?>
					</div>
				</div>

				<ul class="bw-product-grid" data-bw-shop-grid>
					<?php
					while ( have_posts() ) :
						the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
					?>
				</ul>

				<?php do_action( 'woocommerce_after_shop_loop' ); ?>

				<div data-bw-shop-pagination>
					<?php echo bodywings_render_pagination_html( $GLOBALS['wp_query'], $bw_paged ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

			<?php else : ?>

				<?php do_action( 'woocommerce_no_products_found' ); ?>

			<?php endif; ?>
		</div>
	</div>
</div>

<?php
get_footer();
