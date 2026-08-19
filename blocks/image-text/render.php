<?php
/**
 * Bild & Text (z.B. „Über uns“).
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading  = $attributes['heading'] ?? '';
$bw_text     = $attributes['text'] ?? '';
$bw_image    = $attributes['imageUrl'] ?? '';
$bw_alt      = $attributes['imageAlt'] ?? '';
$bw_position = 'right' === ( $attributes['imagePosition'] ?? 'left' ) ? 'right' : 'left';
$bw_btn_text = $attributes['buttonText'] ?? '';
$bw_btn_url  = $attributes['buttonUrl'] ?? '';
$bw_tag      = ! empty( $attributes['asH1'] ) ? 'h1' : 'h2';

if ( '' === trim( wp_strip_all_tags( $bw_heading ) ) && '' === trim( wp_strip_all_tags( $bw_text ) ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-image-text bw-image-text--' . $bw_position ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner bw-image-text__inner">
		<?php if ( $bw_image ) : ?>
			<div class="bw-image-text__media">
				<img src="<?php echo esc_url( $bw_image ); ?>" alt="<?php echo esc_attr( $bw_alt ); ?>" loading="lazy" decoding="async" />
			</div>
		<?php endif; ?>

		<div class="bw-image-text__content">
			<?php if ( $bw_heading ) : ?>
				<?php echo sprintf( '<%1$s class="bw-image-text__heading">%2$s</%1$s>', esc_html( $bw_tag ), wp_kses_post( $bw_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>

			<?php if ( $bw_text ) : ?>
				<div class="bw-image-text__text"><?php echo wp_kses_post( $bw_text ); ?></div>
			<?php endif; ?>

			<?php if ( $bw_btn_text && $bw_btn_url ) : ?>
				<a class="bw-btn" href="<?php echo esc_url( $bw_btn_url ); ?>"><?php echo esc_html( $bw_btn_text ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
