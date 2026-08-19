<?php
/**
 * Bewertungen-Slider: Container für bw/testimonial-item-Kinder.
 *
 * @var array  $attributes
 * @var string $content Bereits gerenderte InnerBlocks (testimonial-item).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
	return;
}

$bw_heading = $attributes['heading'] ?? '';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-testimonial-slider' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-testimonial-slider__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>

		<div class="bw-slider" data-bw-slider>
			<ul class="bw-slider__track bw-testimonial-slider__track" data-bw-slider-track tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Bewertungen, horizontal scrollbar', 'bodywings' ); ?>">
				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- InnerBlocks-Output. ?>
			</ul>

			<div class="bw-slider__nav">
				<button type="button" data-bw-slider-prev aria-label="<?php esc_attr_e( 'Zurück', 'bodywings' ); ?>"><?php echo bodywings_icon( 'chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<button type="button" data-bw-slider-next aria-label="<?php esc_attr_e( 'Weiter', 'bodywings' ); ?>"><?php echo bodywings_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</div>
		</div>
	</div>
</div>
