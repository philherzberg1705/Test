<?php
/**
 * Wishlist-Toggle-Button. Markup bereits AJAX-bereit (data-bw-wishlist-
 * toggle), die eigentliche Speicher-/Toggle-Logik kommt in Task 13.
 * Aktuell rein visuell + Deep-Link-fähig ohne JS (führt zur Wishlist-Seite).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_product_id = (int) ( $args['product_id'] ?? 0 );

if ( ! $bw_product_id ) {
	return;
}
?>
<button
	type="button"
	class="bw-wishlist-btn"
	data-bw-wishlist-toggle
	data-product-id="<?php echo esc_attr( (string) $bw_product_id ); ?>"
	aria-pressed="false"
>
	<?php echo bodywings_icon( 'heart' ); // phpcs:ignore ?>
	<span><?php esc_html_e( 'Zur Wunschliste hinzufügen', 'bodywings' ); ?></span>
</button>
