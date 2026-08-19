<?php
/**
 * Vorher/Nachher-Bild: Vergleichsregler zwischen zwei Bildern.
 *
 * Bedienelement ist ein natives <input type="range">, transparent über die
 * volle Bildfläche gelegt (§25: native Tastatur-/Touch-Unterstützung statt
 * eines selbst gebauten role="slider"-Widgets). Beide <img> bleiben im DOM
 * vollständig vorhanden — clip-path blendet visuell nur eines teilweise
 * aus, Screenreader bekommen beide Alt-Texte trotzdem mit.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_before_url = $attributes['beforeImageUrl'] ?? '';
$bw_after_url  = $attributes['afterImageUrl'] ?? '';

if ( ! $bw_before_url || ! $bw_after_url ) {
	return;
}

$bw_heading       = $attributes['heading'] ?? '';
$bw_before_alt    = $attributes['beforeImageAlt'] ?? '';
$bw_after_alt     = $attributes['afterImageAlt'] ?? '';
$bw_before_label  = $attributes['beforeLabel'] ?? '';
$bw_after_label   = $attributes['afterLabel'] ?? '';
$bw_start         = max( 0, min( 100, (int) ( $attributes['startPosition'] ?? 50 ) ) );
$bw_desc_id       = wp_unique_id( 'bw-ba-desc-' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-before-after' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-before-after__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>

		<div class="bw-before-after__frame" data-bw-before-after style="--bw-ba-position:<?php echo esc_attr( (string) $bw_start ); ?>%;">
			<p id="<?php echo esc_attr( $bw_desc_id ); ?>" class="bw-visually-hidden">
				<?php esc_html_e( 'Regler mit den Pfeiltasten bewegen, um Vorher- und Nachher-Bild zu vergleichen.', 'bodywings' ); ?>
			</p>

			<div class="bw-before-after__stage">
				<img
					class="bw-before-after__img bw-before-after__img--after"
					src="<?php echo esc_url( $bw_after_url ); ?>"
					alt="<?php echo esc_attr( $bw_after_alt ); ?>"
					loading="lazy"
					decoding="async"
				/>
				<div class="bw-before-after__before-wrap">
					<img
						class="bw-before-after__img bw-before-after__img--before"
						src="<?php echo esc_url( $bw_before_url ); ?>"
						alt="<?php echo esc_attr( $bw_before_alt ); ?>"
						loading="lazy"
						decoding="async"
					/>
				</div>

				<?php if ( $bw_before_label ) : ?>
					<span class="bw-before-after__label bw-before-after__label--before"><?php echo esc_html( $bw_before_label ); ?></span>
				<?php endif; ?>
				<?php if ( $bw_after_label ) : ?>
					<span class="bw-before-after__label bw-before-after__label--after"><?php echo esc_html( $bw_after_label ); ?></span>
				<?php endif; ?>

				<div class="bw-before-after__handle" aria-hidden="true">
					<?php echo bodywings_icon( 'chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo bodywings_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>

			<input
				type="range"
				class="bw-before-after__range"
				data-bw-ba-range
				min="0"
				max="100"
				value="<?php echo esc_attr( (string) $bw_start ); ?>"
				aria-label="<?php esc_attr_e( 'Vergleichsposition zwischen Vorher- und Nachher-Bild', 'bodywings' ); ?>"
				aria-describedby="<?php echo esc_attr( $bw_desc_id ); ?>"
			/>
		</div>
	</div>
</div>
