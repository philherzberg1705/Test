<?php
/**
 * Kategorien-Akkordeon. Erste Kategorie serverseitig offen gerendert
 * (funktioniert dadurch auch, bevor JS geladen ist), Klick- und
 * Scroll-Steuerung kommt aus assets/js/block-category-accordion.js.
 *
 * Geschlossene Panels bekommen inert + aria-hidden (gleiches Prinzip wie
 * die Offcanvas-Panels, §33) statt nur visuell über CSS zusammengefaltet
 * zu werden — sonst würden Screenreader/Tab-Reihenfolge Inhalte "sehen",
 * die visuell gar nicht da sind.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<p class="bw-block bw-block__admin-notice">' . esc_html__( 'Kategorien-Akkordeon: WooCommerce ist nicht aktiv.', 'bodywings' ) . '</p>';
	}
	return;
}

$bw_heading = $attributes['heading'] ?? '';
$bw_ids     = array_filter( array_map( 'absint', (array) ( $attributes['categoryIds'] ?? array() ) ) );
$bw_count   = max( 2, min( 10, (int) ( $attributes['count'] ?? 5 ) ) );

$bw_term_args = array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'parent'     => $bw_ids ? '' : 0,
	'number'     => $bw_count,
);

if ( $bw_ids ) {
	$bw_term_args['include'] = $bw_ids;
	$bw_term_args['orderby'] = 'include';
}

$bw_terms = get_terms( $bw_term_args );

if ( is_wp_error( $bw_terms ) || count( $bw_terms ) < 2 ) {
	return; // Ein Akkordeon mit nur einem Eintrag ergibt keinen Sinn.
}

$bw_uid = wp_unique_id( 'bw-cat-acc-' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-category-accordion' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-category-accordion__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>

		<div class="bw-accordion" data-bw-category-accordion>
			<?php foreach ( array_values( $bw_terms ) as $bw_index => $bw_term ) : ?>
				<?php
				$bw_is_open   = 0 === $bw_index;
				$bw_thumb_id  = get_term_meta( $bw_term->term_id, 'thumbnail_id', true );
				$bw_image_url = $bw_thumb_id ? wp_get_attachment_image_url( $bw_thumb_id, 'large' ) : '';
				$bw_trigger_id = $bw_uid . '-trigger-' . $bw_index;
				$bw_panel_id   = $bw_uid . '-panel-' . $bw_index;
				?>
				<div
					class="bw-accordion__item<?php echo $bw_is_open ? ' is-open' : ''; ?>"
					data-bw-accordion-item
					<?php if ( $bw_image_url ) : ?>
						style="background-image:url('<?php echo esc_url( $bw_image_url ); ?>');"
					<?php endif; ?>
				>
					<h3 class="bw-accordion__header">
						<button
							type="button"
							class="bw-accordion__trigger"
							id="<?php echo esc_attr( $bw_trigger_id ); ?>"
							aria-expanded="<?php echo $bw_is_open ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $bw_panel_id ); ?>"
							data-bw-accordion-trigger
						>
							<span class="bw-accordion__title"><?php echo esc_html( $bw_term->name ); ?></span>
						</button>
					</h3>

					<div
						class="bw-accordion__panel"
						id="<?php echo esc_attr( $bw_panel_id ); ?>"
						aria-labelledby="<?php echo esc_attr( $bw_trigger_id ); ?>"
						<?php echo $bw_is_open ? '' : 'inert aria-hidden="true"'; ?>
					>
						<span class="bw-accordion__line" aria-hidden="true"></span>
						<div class="bw-accordion__body">
							<?php if ( $bw_term->description ) : ?>
								<p class="bw-accordion__text"><?php echo esc_html( wp_trim_words( $bw_term->description, 30 ) ); ?></p>
							<?php endif; ?>
							<a class="bw-accordion__link" href="<?php echo esc_url( get_term_link( $bw_term ) ); ?>">
								<?php esc_html_e( 'Kategorie ansehen', 'bodywings' ); ?>
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
