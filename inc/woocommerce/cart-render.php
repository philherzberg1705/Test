<?php
/**
 * Mini-Cart-Rendering (§16), von archive-product.php/header unabhängig
 * aufrufbar und von allen drei Cart-AJAX-Aktionen (add/update/remove)
 * gemeinsam genutzt (§33) — ein Response-Format statt drei.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

function bodywings_render_mini_cart_html(): string {
	$cart = WC()->cart;

	ob_start();

	if ( $cart->is_empty() ) {
		?>
		<p class="bw-mini-cart__empty"><?php esc_html_e( 'Dein Warenkorb ist leer.', 'bodywings' ); ?></p>
		<?php
		return ob_get_clean();
	}
	?>
	<ul class="bw-mini-cart__items">
		<?php foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) : ?>
			<?php
			/** @var WC_Product $product */
			$product = $cart_item['data'];

			if ( ! $product ) {
				continue;
			}
			?>
			<li class="bw-mini-cart__item">
				<a class="bw-mini-cart__image" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
					<?php echo wp_kses_post( $product->get_image( 'thumbnail' ) ); ?>
				</a>
				<div class="bw-mini-cart__details">
					<a class="bw-mini-cart__name" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
						<?php echo esc_html( $product->get_name() ); ?>
					</a>
					<?php
					$variation_text = bodywings_get_cart_item_variation_text( $product, $cart_item );
					if ( $variation_text ) :
						?>
						<span class="bw-mini-cart__variation"><?php echo esc_html( $variation_text ); ?></span>
					<?php endif; ?>

					<span class="bw-mini-cart__price"><?php echo wp_kses_post( wc_price( (float) $cart_item['line_total'] ) ); ?></span>

					<div class="bw-mini-cart__row">
						<input
							type="number"
							class="bw-mini-cart__qty"
							min="1"
							value="<?php echo esc_attr( (string) $cart_item['quantity'] ); ?>"
							data-bw-cart-qty
							data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>"
							aria-label="<?php esc_attr_e( 'Menge', 'bodywings' ); ?>"
						/>
						<button
							type="button"
							class="bw-mini-cart__remove"
							data-bw-cart-remove
							data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>"
						>
							<?php esc_html_e( 'Entfernen', 'bodywings' ); ?>
						</button>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="bw-mini-cart__subtotal">
		<span><?php esc_html_e( 'Zwischensumme', 'bodywings' ); ?></span>
		<span><?php echo wp_kses_post( wc_price( (float) $cart->get_subtotal() ) ); ?></span>
	</div>

	<div class="bw-mini-cart__actions">
		<a class="bw-btn bw-btn--outline" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<?php esc_html_e( 'Warenkorb ansehen', 'bodywings' ); ?>
		</a>
		<a class="bw-btn" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
			<?php esc_html_e( 'Zur Kasse', 'bodywings' ); ?>
		</a>
	</div>
	<?php
	return ob_get_clean();
}

function bodywings_get_cart_item_variation_text( WC_Product $product, array $cart_item ): string {
	if ( empty( $cart_item['variation'] ) || ! is_array( $cart_item['variation'] ) ) {
		return '';
	}

	$parts = array();

	foreach ( $cart_item['variation'] as $attribute_key => $value ) {
		if ( ! $value ) {
			continue;
		}

		$taxonomy = str_replace( 'attribute_', '', $attribute_key );
		$term     = taxonomy_exists( $taxonomy ) ? get_term_by( 'slug', $value, $taxonomy ) : false;

		$parts[] = $term ? $term->name : $value;
	}

	return implode( ' · ', $parts );
}
