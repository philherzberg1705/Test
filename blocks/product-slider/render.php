<?php
/**
 * Produkt-Slider. Nutzt dieselbe Produktkarte wie Shop/Filter/Suche (§33).
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<p class="bw-block bw-block__admin-notice">' . esc_html__( 'Produkt-Slider: WooCommerce ist nicht aktiv.', 'bodywings' ) . '</p>';
	}
	return;
}

$bw_heading = $attributes['heading'] ?? '';
$bw_source  = $attributes['source'] ?? 'recent';
$bw_count   = max( 1, min( 20, (int) ( $attributes['count'] ?? 8 ) ) );

$bw_query_args = array(
	'status'  => 'publish',
	'limit'   => $bw_count,
	'orderby' => 'date',
	'order'   => 'DESC',
	'return'  => 'objects',
);

switch ( $bw_source ) {
	case 'featured':
		$bw_query_args['featured'] = true;
		break;

	case 'onsale':
		$bw_query_args['include'] = wc_get_product_ids_on_sale();
		if ( empty( $bw_query_args['include'] ) ) {
			return;
		}
		break;

	case 'category':
		$bw_category_id = (int) ( $attributes['categoryId'] ?? 0 );
		if ( ! $bw_category_id ) {
			return;
		}
		$bw_query_args['tax_query'] = array( array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => $bw_category_id,
		) );
		break;

	case 'recent':
	default:
		break;
}

$bw_products = wc_get_products( $bw_query_args );

if ( ! $bw_products ) {
	return;
}

$bw_autoplay = ! empty( $attributes['autoplay'] ) && count( $bw_products ) > 1;
// Lauftempo skaliert mit der Produktanzahl, damit ein einzelnes Produkt
// im Loop immer gleich lang zu sehen ist, egal wie viele es insgesamt
// sind — keine zusätzliche Backend-Einstellung nötig (§29).
$bw_marquee_seconds = max( 20, count( $bw_products ) * 4 );
?>
<div
	<?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-product-slider' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
	<div class="bw-block__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-product-slider__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>

		<div
			class="bw-slider"
			data-bw-slider
			<?php echo $bw_autoplay ? 'data-bw-autoplay' : ''; ?>
			<?php echo $bw_autoplay ? 'style="--bw-marquee-duration:' . esc_attr( (string) $bw_marquee_seconds ) . 's;"' : ''; ?>
		>
			<ul
				class="bw-slider__track bw-product-slider__track"
				data-bw-slider-track
				<?php echo $bw_autoplay ? '' : 'tabindex="0" role="region" aria-label="' . esc_attr__( 'Produkte, horizontal scrollbar', 'bodywings' ) . '"'; ?>
			>
				<?php foreach ( $bw_products as $bw_product ) : ?>
					<li class="bw-product-slider__item"><?php bodywings_render_product_card( $bw_product ); ?></li>
				<?php endforeach; ?>

				<?php if ( $bw_autoplay ) : ?>
					<?php // Zweiter, für Tastatur/Screenreader ausgeblendeter Satz für den nahtlosen Loop (§25) — die "echten" Karten oben bleiben die einzigen erreichbaren. ?>
					<?php foreach ( $bw_products as $bw_product ) : ?>
						<li class="bw-product-slider__item" inert aria-hidden="true"><?php bodywings_render_product_card( $bw_product ); ?></li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>

			<div class="bw-slider__nav">
				<?php if ( $bw_autoplay ) : ?>
					<button
						type="button"
						class="bw-slider__playpause"
						data-bw-slider-playpause
						data-label-pause="<?php echo esc_attr__( 'Automatisches Weiterscrollen pausieren', 'bodywings' ); ?>"
						data-label-play="<?php echo esc_attr__( 'Automatisches Weiterscrollen fortsetzen', 'bodywings' ); ?>"
						aria-label="<?php esc_attr_e( 'Automatisches Weiterscrollen pausieren', 'bodywings' ); ?>"
					>
						<span data-bw-slider-playpause-pause-icon><?php echo bodywings_icon( 'pause' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span data-bw-slider-playpause-play-icon hidden><?php echo bodywings_icon( 'play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</button>
				<?php endif; ?>
				<button type="button" data-bw-slider-prev aria-label="<?php esc_attr_e( 'Zurück', 'bodywings' ); ?>"><?php echo bodywings_icon( 'chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<button type="button" data-bw-slider-next aria-label="<?php esc_attr_e( 'Weiter', 'bodywings' ); ?>"><?php echo bodywings_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</div>
		</div>
	</div>
</div>
