<?php
/**
 * Offcanvas-Suche. Funktioniert als normales WP-Suchformular (progressive
 * enhancement); die Live-Suche mit AJAX-Ergebnissen (§14) kommt in Task 11
 * und hängt sich an den leeren Ergebnis-Container darunter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bw-offcanvas bw-offcanvas--search" data-bw-offcanvas="search" id="bw-offcanvas-search" aria-hidden="true" inert>
	<div class="bw-offcanvas__backdrop" data-bw-offcanvas-close></div>
	<div class="bw-offcanvas__panel bw-offcanvas__panel--search" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Suche', 'bodywings' ); ?>">
		<button type="button" class="bw-icon-btn bw-offcanvas__close" data-bw-offcanvas-close>
			<?php echo bodywings_icon( 'close' ); // phpcs:ignore ?>
			<span class="bw-visually-hidden"><?php esc_html_e( 'Suche schließen', 'bodywings' ); ?></span>
		</button>

		<form role="search" method="get" class="bw-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-bw-search-form>
			<label class="bw-visually-hidden" for="bw-search-input"><?php esc_html_e( 'Produkte durchsuchen', 'bodywings' ); ?></label>
			<input
				type="search"
				id="bw-search-input"
				class="bw-input bw-search-form__input"
				name="s"
				placeholder="<?php esc_attr_e( 'Produkte durchsuchen …', 'bodywings' ); ?>"
				autocomplete="off"
			/>
			<?php if ( bodywings_is_woocommerce_active() ) : ?>
				<input type="hidden" name="post_type" value="product" />
			<?php endif; ?>
			<button type="submit" class="bw-icon-btn bw-search-form__submit">
				<?php echo bodywings_icon( 'search' ); // phpcs:ignore ?>
				<span class="bw-visually-hidden"><?php esc_html_e( 'Suchen', 'bodywings' ); ?></span>
			</button>
		</form>

		<div class="bw-search-results" data-bw-search-results aria-live="polite"></div>
	</div>
</div>
