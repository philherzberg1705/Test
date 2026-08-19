<?php
/**
 * Wishlist-Toggle-Button (§7). Zwei Varianten über $args['variant']:
 * "text" (Produktseite, mit Label) und "icon" (Produktkarten-Overlay,
 * nur Icon). Serverseitiger Aktiv-Zustand gilt nur für eingeloggte
 * Nutzer:innen — für Gäste korrigiert assets/js/ajax/wishlist.js den
 * Zustand nach dem Laden anhand von localStorage (§7).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_product_id = (int) ( $args['product_id'] ?? 0 );

if ( ! $bw_product_id ) {
	return;
}

$bw_variant = $args['variant'] ?? 'text';
$bw_active  = function_exists( 'bodywings_is_product_in_wishlist' ) && bodywings_is_product_in_wishlist( $bw_product_id );
?>
<button
	type="button"
	class="bw-wishlist-btn bw-wishlist-btn--<?php echo esc_attr( $bw_variant ); ?><?php echo $bw_active ? ' is-active' : ''; ?>"
	data-bw-wishlist-toggle
	data-product-id="<?php echo esc_attr( (string) $bw_product_id ); ?>"
	aria-pressed="<?php echo $bw_active ? 'true' : 'false'; ?>"
>
	<?php echo bodywings_icon( 'heart' ); // phpcs:ignore ?>
	<?php if ( 'text' === $bw_variant ) : ?>
		<span><?php esc_html_e( 'Zur Wunschliste hinzufügen', 'bodywings' ); ?></span>
	<?php else : ?>
		<span class="bw-visually-hidden"><?php esc_html_e( 'Zur Wunschliste hinzufügen', 'bodywings' ); ?></span>
	<?php endif; ?>
</button>
