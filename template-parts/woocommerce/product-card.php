<?php
/**
 * Produktkarten-Markup (§8). $args kommt aus bodywings_render_product_card().
 * Wishlist-Icon liegt als Overlay auf dem Bild (§7), zählt bewusst nicht
 * als zusätzliche "Information" im Sinne von §8 — Bild/Varianten/Titel/
 * Preis bleiben die einzigen Inhalts-Elemente der Karte.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var WC_Product $product */
$product = $args['product'];
/** @var array $card */
$card = $args['card'];

if ( ! $card['primary_image_src'] ) {
	$card['primary_image_src'] = wc_placeholder_img_src( 'bodywings-product-card' );
}
?>
<article class="bw-product-card" data-bw-product-card data-bw-reveal>
	<div class="bw-product-card__media">
		<a class="bw-product-card__media-link" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
			<img
				class="bw-product-card__image bw-product-card__image--primary<?php echo $card['primary_transparent'] ? ' bw-product-card__image--transparent' : ''; ?>"
				src="<?php echo esc_url( $card['primary_image_src'] ); ?>"
				data-bw-card-image="primary"
				loading="lazy"
				decoding="async"
				alt="<?php echo esc_attr( $product->get_name() ); ?>"
			/>
			<?php if ( $card['secondary_image_src'] ) : ?>
				<img
					class="bw-product-card__image bw-product-card__image--secondary"
					src="<?php echo esc_url( $card['secondary_image_src'] ); ?>"
					loading="lazy"
					decoding="async"
					alt=""
				/>
			<?php endif; ?>
		</a>

		<?php
		get_template_part( 'template-parts/woocommerce/wishlist-button', null, array(
			'product_id' => $product->get_id(),
			'variant'    => 'icon',
		) );
		?>
	</div>

	<?php if ( ! empty( $card['variants'] ) ) : ?>
		<div class="bw-product-card__variants" role="group" aria-label="<?php esc_attr_e( 'Variante wählen', 'bodywings' ); ?>">
			<?php foreach ( $card['variants'] as $variant ) : ?>
				<button
					type="button"
					class="bw-swatch<?php echo $variant['is_active'] ? ' is-active' : ''; ?>"
					data-bw-variant-image="<?php echo esc_url( $variant['variant_image'] ); ?>"
					aria-label="<?php echo esc_attr( $variant['name'] ); ?>"
					aria-pressed="<?php echo $variant['is_active'] ? 'true' : 'false'; ?>"
					<?php if ( $variant['swatch_image'] ) : ?>
						style="background-image:url('<?php echo esc_url( $variant['swatch_image'] ); ?>');"
					<?php endif; ?>
				></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<a class="bw-product-card__title" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
		<?php echo esc_html( $product->get_name() ); ?>
	</a>

	<span class="bw-product-card__price">
		<?php echo wp_kses_post( $product->get_price_html() ); ?>
	</span>
</article>
