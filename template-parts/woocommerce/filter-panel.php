<?php
/**
 * Filter-Panel (§13). Ein Panel für Desktop (statische Fläche) UND Mobile
 * (Offcanvas) über data-bw-offcanvas-responsive, siehe assets/js/offcanvas.js.
 * Reine Checkbox-Formulare — Aktivzustand kommt aus CSS (:checked), AJAX-
 * Übermittlung aus assets/js/ajax/filter.js. Ohne JS bleibt das Formular
 * als normaler GET-Submit funktionsfähig (progressive enhancement).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var WP_Term[] $categories */
$categories = $args['categories'];
/** @var array $attribute_groups */
$attribute_groups = $args['attribute_groups'];
/** @var array $params */
$params = $args['params'];
/** @var array $context */
$context = $args['context'];
?>
<button
	type="button"
	class="bw-btn bw-filter__trigger"
	data-bw-offcanvas-trigger="filter"
	aria-expanded="false"
	aria-controls="bw-offcanvas-filter"
>
	<?php esc_html_e( 'Filter', 'bodywings' ); ?>
</button>

<aside
	class="bw-offcanvas bw-offcanvas--filter bw-filter"
	data-bw-offcanvas="filter"
	data-bw-offcanvas-responsive="960"
	id="bw-offcanvas-filter"
	aria-hidden="true"
	inert
>
	<div class="bw-offcanvas__backdrop" data-bw-offcanvas-close></div>
	<div class="bw-offcanvas__panel bw-filter__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Filter', 'bodywings' ); ?>">
		<button type="button" class="bw-icon-btn bw-offcanvas__close" data-bw-offcanvas-close>
			<?php echo bodywings_icon( 'close' ); // phpcs:ignore ?>
			<span class="bw-visually-hidden"><?php esc_html_e( 'Filter schließen', 'bodywings' ); ?></span>
		</button>

		<form method="get" data-bw-filter-form data-bw-filter-context='<?php echo esc_attr( wp_json_encode( $context ) ); ?>'>
			<?php if ( $categories ) : ?>
				<fieldset class="bw-filter__group">
					<legend><?php esc_html_e( 'Kategorie', 'bodywings' ); ?></legend>
					<?php foreach ( $categories as $category ) : ?>
						<label class="bw-filter__checkbox-label">
							<input
								type="checkbox"
								name="bw_cat[]"
								value="<?php echo esc_attr( $category->slug ); ?>"
								<?php checked( in_array( $category->slug, $params['cat'], true ) ); ?>
							/>
							<?php echo esc_html( $category->name ); ?>
						</label>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>

			<?php foreach ( $attribute_groups as $group ) : ?>
				<fieldset class="bw-filter__group">
					<legend><?php echo esc_html( $group['label'] ); ?></legend>
					<div class="bw-filter__swatches">
						<?php
						$selected = $params['attributes'][ $group['taxonomy'] ] ?? array();
						foreach ( $group['terms'] as $term ) :
							$image_url  = bodywings_get_attribute_term_image_url( $term->term_id, 'thumbnail' );
							$field_id   = 'bw-filter-' . $group['taxonomy'] . '-' . $term->term_id;
							?>
							<span class="bw-filter__swatch-item">
								<input
									type="checkbox"
									id="<?php echo esc_attr( $field_id ); ?>"
									name="<?php echo esc_attr( $group['taxonomy'] ); ?>[]"
									value="<?php echo esc_attr( $term->slug ); ?>"
									class="bw-visually-hidden bw-swatch-checkbox"
									<?php checked( in_array( $term->slug, $selected, true ) ); ?>
								/>
								<label
									for="<?php echo esc_attr( $field_id ); ?>"
									class="bw-swatch<?php echo $image_url ? '' : ' bw-swatch--text'; ?>"
									<?php if ( $image_url ) : ?>
										style="background-image:url('<?php echo esc_url( $image_url ); ?>');"
									<?php endif; ?>
									title="<?php echo esc_attr( $term->name ); ?>"
								>
									<?php if ( ! $image_url ) : ?>
										<?php echo esc_html( $term->name ); ?>
									<?php endif; ?>
									<span class="bw-visually-hidden"><?php echo esc_html( $term->name ); ?></span>
								</label>
							</span>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endforeach; ?>

			<fieldset class="bw-filter__group">
				<legend><?php esc_html_e( 'Preis', 'bodywings' ); ?></legend>
				<div class="bw-filter__price-row">
					<label class="bw-visually-hidden" for="bw-filter-min-price"><?php esc_html_e( 'Min. Preis', 'bodywings' ); ?></label>
					<input
						type="number"
						min="0"
						inputmode="numeric"
						id="bw-filter-min-price"
						name="bw_min_price"
						class="bw-input"
						placeholder="<?php esc_attr_e( 'Min €', 'bodywings' ); ?>"
						value="<?php echo esc_attr( null !== $params['min_price'] ? (string) $params['min_price'] : '' ); ?>"
					/>
					<span aria-hidden="true">–</span>
					<label class="bw-visually-hidden" for="bw-filter-max-price"><?php esc_html_e( 'Max. Preis', 'bodywings' ); ?></label>
					<input
						type="number"
						min="0"
						inputmode="numeric"
						id="bw-filter-max-price"
						name="bw_max_price"
						class="bw-input"
						placeholder="<?php esc_attr_e( 'Max €', 'bodywings' ); ?>"
						value="<?php echo esc_attr( null !== $params['max_price'] ? (string) $params['max_price'] : '' ); ?>"
					/>
				</div>
			</fieldset>

			<fieldset class="bw-filter__group">
				<label class="bw-filter__checkbox-label">
					<input type="checkbox" name="bw_in_stock" value="1" <?php checked( $params['in_stock'] ); ?> />
					<?php esc_html_e( 'Nur verfügbare Produkte', 'bodywings' ); ?>
				</label>
			</fieldset>

			<button type="button" class="bw-filter__reset" data-bw-filter-reset>
				<?php esc_html_e( 'Filter zurücksetzen', 'bodywings' ); ?>
			</button>
		</form>
	</div>
</aside>
