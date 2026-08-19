<?php
/**
 * Override von WooCommerce/templates/archive-product.php.
 * Eigenes Grid statt Standard-WooCommerce-Loop-Markup (§8/§13-Vorbereitung).
 * Der Filter-Sidebar-Slot ("bodywings_shop_before_grid") wird in Task 10
 * befüllt, ohne dass diese Datei nochmal angefasst werden muss (§38).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="bw-container bw-shop">
	<header class="bw-shop__header">
		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
			<h1 class="bw-shop__title"><?php woocommerce_page_title(); ?></h1>
		<?php endif; ?>

		<?php do_action( 'woocommerce_archive_description' ); ?>
	</header>

	<?php do_action( 'bodywings_shop_before_grid' ); ?>

	<?php if ( woocommerce_product_loop() ) : ?>

		<div class="bw-shop__toolbar">
			<p class="bw-shop__result-count bw-text-small bw-color-muted">
				<?php woocommerce_result_count(); ?>
			</p>
			<div class="bw-shop__ordering">
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		</div>

		<ul class="bw-product-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile;
			?>
		</ul>

		<?php do_action( 'woocommerce_after_shop_loop' ); ?>

	<?php else : ?>

		<?php do_action( 'woocommerce_no_products_found' ); ?>

	<?php endif; ?>
</div>

<?php
get_footer();
