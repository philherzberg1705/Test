<?php
/**
 * Override von WooCommerce/templates/content-single-product.php.
 * Zwei-Spalten-Layout (Galerie | Summary) statt Standard-Markup; die
 * einzelnen Bausteine (Titel, Preis, Add-to-Cart, Tabs, verwandte
 * Produkte) bleiben WooCommerce-Kernfunktionen (§31), nur Anordnung und
 * Ergänzungen kommen aus inc/woocommerce/single-product-hooks.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>

<div class="bw-container bw-product-page">
	<?php woocommerce_breadcrumb(); ?>

	<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'bw-product-page__layout', $product ); ?>>
		<div class="bw-product-page__gallery">
			<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
		</div>

		<div class="bw-product-page__summary summary entry-summary">
			<?php do_action( 'woocommerce_single_product_summary' ); ?>
		</div>
	</div>

	<div class="bw-product-page__details">
		<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
