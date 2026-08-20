<?php
/**
 * Geschäftssuche: Karte + Suchfunktion über alle Geschäftspartner:innen der
 * konfigurierten Rolle (inc/business-search/settings.php). Die Liste wird
 * immer serverseitig gerendert (§25 — Kontaktdaten bleiben ohne JS
 * erreichbar), die Karte selbst ist zwangsläufig JS-abhängig (Leaflet,
 * assets/js/block-business-search.js), Suche filtert Liste+Karte clientseitig
 * statt per AJAX-Roundtrip — bei einer realistischen Zahl von
 * Geschäftspartner:innen (Verzeichnis, keine Massendaten) reicht ein einmal
 * geladener Datensatz völlig aus (§15 "kein Selbstzweck").
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_get_business_search_role() ) {
	return;
}

$bw_heading  = $attributes['heading'] ?? '';
$bw_text     = $attributes['text'] ?? '';
$bw_profiles = bodywings_get_business_search_profiles();
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-business-search' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner bw-business-search__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-business-search__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>
		<?php if ( $bw_text ) : ?>
			<div class="bw-business-search__text"><?php echo wp_kses_post( $bw_text ); ?></div>
		<?php endif; ?>

		<?php if ( empty( $bw_profiles ) ) : ?>
			<p class="bw-business-search__empty bw-color-muted">
				<?php esc_html_e( 'Aktuell sind noch keine Einträge auf der Karte hinterlegt.', 'bodywings' ); ?>
			</p>
		<?php else : ?>
			<div
				class="bw-business-search__app"
				data-bw-business-search
				data-bw-leaflet-images="<?php echo esc_url( BODYWINGS_URI . '/assets/js/vendor/leaflet/images/' ); ?>"
				data-bw-business-search-profiles='<?php echo wp_json_encode( $bw_profiles ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode escaped, esc_attr direkt danach ?>'
			>
				<div class="bw-business-search__panel">
					<label class="bw-visually-hidden" for="bw-business-search-input"><?php esc_html_e( 'Geschäfte durchsuchen', 'bodywings' ); ?></label>
					<input
						type="search"
						id="bw-business-search-input"
						class="bw-input bw-business-search__input"
						placeholder="<?php esc_attr_e( 'Name oder Ort …', 'bodywings' ); ?>"
						data-bw-business-search-input
					/>

					<ul class="bw-business-search__list" data-bw-business-search-list>
						<?php foreach ( $bw_profiles as $bw_profile ) : ?>
							<li class="bw-business-search__item" data-bw-business-search-item data-id="<?php echo esc_attr( (string) $bw_profile['id'] ); ?>">
								<button type="button" class="bw-business-search__item-trigger" data-bw-business-search-item-trigger>
									<span class="bw-business-search__item-name"><?php echo esc_html( $bw_profile['name'] ); ?></span>
									<span class="bw-business-search__item-address"><?php echo esc_html( $bw_profile['address'] ); ?></span>
								</button>
								<p class="bw-business-search__item-contact">
									<?php if ( $bw_profile['phone'] ) : ?>
										<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $bw_profile['phone'] ) ); ?>"><?php echo esc_html( $bw_profile['phone'] ); ?></a>
									<?php endif; ?>
									<?php if ( $bw_profile['website'] ) : ?>
										<a href="<?php echo esc_url( $bw_profile['website'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Website', 'bodywings' ); ?></a>
									<?php endif; ?>
								</p>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<?php
				/*
				 * Bewusst kein role="application": Alle Informationen (Name,
				 * Adresse, Telefon, Website) stehen bereits vollständig und
				 * mit echten Links in der Liste daneben — die Karte ist eine
				 * visuelle Ergänzung, kein eigenständig zu bedienendes
				 * Widget, das den Screenreader-Modus umschalten müsste (§25).
				 */
				?>
				<div class="bw-business-search__map" data-bw-business-search-map role="region" aria-label="<?php esc_attr_e( 'Karte der Standorte', 'bodywings' ); ?>"></div>
			</div>
		<?php endif; ?>
	</div>
</div>
