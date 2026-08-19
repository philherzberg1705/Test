<?php
/**
 * Offcanvas Sprache/Währung. Zeigt vorerst die Basiswährung EUR (§18) und
 * bindet WPML-Sprachen ein, falls aktiv (§28: Plugin vorhanden → Integration
 * aktivieren, sonst sinnvoller Fallback). Das echte Umrechnungssystem
 * (Task 15) hängt sich später an .bw-currency-switcher an.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bw-offcanvas bw-offcanvas--locale" data-bw-offcanvas="locale" id="bw-offcanvas-locale" aria-hidden="true" inert>
	<div class="bw-offcanvas__backdrop" data-bw-offcanvas-close></div>
	<div class="bw-offcanvas__panel bw-offcanvas__panel--locale" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Sprache und Währung', 'bodywings' ); ?>">
		<button type="button" class="bw-icon-btn bw-offcanvas__close" data-bw-offcanvas-close>
			<?php echo bodywings_icon( 'close' ); // phpcs:ignore ?>
			<span class="bw-visually-hidden"><?php esc_html_e( 'Schließen', 'bodywings' ); ?></span>
		</button>

		<?php if ( function_exists( 'icl_get_languages' ) ) : ?>
			<section class="bw-offcanvas__section">
				<h2 class="bw-offcanvas__section-title"><?php esc_html_e( 'Sprache', 'bodywings' ); ?></h2>
				<ul class="bw-locale-list">
					<?php foreach ( (array) icl_get_languages( 'skip_missing=0' ) as $bw_language ) : ?>
						<li>
							<a
								class="bw-locale-list__item<?php echo ! empty( $bw_language['active'] ) ? ' is-active' : ''; ?>"
								href="<?php echo esc_url( $bw_language['url'] ); ?>"
							>
								<?php echo esc_html( $bw_language['native_name'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<section class="bw-offcanvas__section bw-currency-switcher" data-bw-currency-switcher>
			<h2 class="bw-offcanvas__section-title"><?php esc_html_e( 'Währung', 'bodywings' ); ?></h2>
			<p class="bw-currency-switcher__current">
				<?php esc_html_e( 'EUR €', 'bodywings' ); ?>
				<span class="bw-text-small bw-color-muted"><?php esc_html_e( '(Basiswährung)', 'bodywings' ); ?></span>
			</p>
		</section>
	</div>
</div>
