<?php
/**
 * CTA-Banner: schmales Call-to-Action-Band.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading    = $attributes['heading'] ?? '';
$bw_text       = $attributes['text'] ?? '';
$bw_btn_text   = $attributes['buttonText'] ?? '';
$bw_btn_url    = $attributes['buttonUrl'] ?? '';

if ( '' === trim( wp_strip_all_tags( $bw_heading ) ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-cta-banner' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner bw-cta-banner__inner">
		<div class="bw-cta-banner__text">
			<h2 class="bw-cta-banner__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
			<?php if ( $bw_text ) : ?>
				<p class="bw-cta-banner__body"><?php echo wp_kses_post( $bw_text ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $bw_btn_text && $bw_btn_url ) : ?>
			<a class="bw-btn bw-cta-banner__button" href="<?php echo esc_url( $bw_btn_url ); ?>"><?php echo esc_html( $bw_btn_text ); ?></a>
		<?php endif; ?>
	</div>
</div>
