<?php
/**
 * Offcanvas Sprache/Währung. Bindet WPML-Sprachen ein, falls aktiv (§28:
 * Plugin vorhanden → Integration aktivieren). Währungsumschalter (§18):
 * überlässt die Anzeige einem erkannten Mehrwährungsplugin, sonst eigener
 * Cookie-basierter Fallback (inc/integrations/currency/display.php).
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

		<?php if ( function_exists( 'bodywings_get_selected_currency' ) && ! bodywings_has_multicurrency_plugin() ) : ?>
			<section class="bw-offcanvas__section bw-currency-switcher">
				<h2 class="bw-offcanvas__section-title"><?php esc_html_e( 'Währung', 'bodywings' ); ?></h2>
				<?php $bw_selected_currency = bodywings_get_selected_currency(); ?>
				<ul class="bw-locale-list">
					<li>
						<a
							class="bw-locale-list__item<?php echo 'EUR' === $bw_selected_currency ? ' is-active' : ''; ?>"
							href="<?php echo esc_url( remove_query_arg( 'bw_currency' ) ); ?>"
						>
							<?php esc_html_e( 'EUR €', 'bodywings' ); ?>
							<span class="bw-text-small bw-color-muted"><?php esc_html_e( '(Basiswährung)', 'bodywings' ); ?></span>
						</a>
					</li>
					<?php foreach ( bodywings_get_supported_target_currencies() as $bw_currency_code ) : ?>
						<li>
							<a
								class="bw-locale-list__item<?php echo $bw_currency_code === $bw_selected_currency ? ' is-active' : ''; ?>"
								href="<?php echo esc_url( add_query_arg( 'bw_currency', $bw_currency_code ) ); ?>"
							>
								<?php echo esc_html( $bw_currency_code . ' ' . get_woocommerce_currency_symbol( $bw_currency_code ) ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="bw-text-small bw-color-muted">
					<?php esc_html_e( 'Umgerechnete Preise sind unverbindliche Richtwerte, Abrechnung erfolgt in EUR.', 'bodywings' ); ?>
				</p>
			</section>
		<?php elseif ( function_exists( 'bodywings_has_multicurrency_plugin' ) ) : ?>
			<section class="bw-offcanvas__section bw-currency-switcher">
				<h2 class="bw-offcanvas__section-title"><?php esc_html_e( 'Währung', 'bodywings' ); ?></h2>
				<p class="bw-text-small bw-color-muted"><?php echo esc_html( get_woocommerce_currency() ); ?></p>
			</section>
		<?php endif; ?>
	</div>
</div>
