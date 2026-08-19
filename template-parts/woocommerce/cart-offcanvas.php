<?php
/**
 * Side-Cart-Panel (§16). Nutzt denselben Offcanvas-Controller wie Menü/
 * Suche/Sprache/Filter (§33).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}
?>
<div class="bw-offcanvas bw-offcanvas--cart" data-bw-offcanvas="cart" id="bw-offcanvas-cart" aria-hidden="true" inert>
	<div class="bw-offcanvas__backdrop" data-bw-offcanvas-close></div>
	<div class="bw-offcanvas__panel bw-mini-cart" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Warenkorb', 'bodywings' ); ?>">
		<button type="button" class="bw-icon-btn bw-offcanvas__close" data-bw-offcanvas-close>
			<?php echo bodywings_icon( 'close' ); // phpcs:ignore ?>
			<span class="bw-visually-hidden"><?php esc_html_e( 'Warenkorb schließen', 'bodywings' ); ?></span>
		</button>

		<h2 class="bw-mini-cart__title"><?php esc_html_e( 'Warenkorb', 'bodywings' ); ?></h2>

		<div data-bw-cart-content>
			<?php echo bodywings_render_mini_cart_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- selbst erzeugtes, escaptes Markup ?>
		</div>
	</div>
</div>
