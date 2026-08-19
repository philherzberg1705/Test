<?php
/**
 * Hero (klein) — kompakter Seiten-Header für Unterseiten.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading  = $attributes['heading'] ?? '';
$bw_sub      = $attributes['subheading'] ?? '';
$bw_bg_image = $attributes['backgroundImageUrl'] ?? '';
$bw_tag      = ! empty( $attributes['asH1'] ) ? 'h1' : 'h2';

if ( '' === trim( wp_strip_all_tags( $bw_heading ) ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-hero-small' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php if ( $bw_bg_image ) : ?>
		<div class="bw-hero-small__media" style="background-image:url('<?php echo esc_url( $bw_bg_image ); ?>');" role="img" aria-label="<?php echo esc_attr( wp_strip_all_tags( $bw_heading ) ); ?>"></div>
	<?php endif; ?>

	<div class="bw-block__inner bw-hero-small__inner">
		<?php echo sprintf( '<%1$s class="bw-hero-small__heading">%2$s</%1$s>', esc_html( $bw_tag ), wp_kses_post( $bw_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php if ( $bw_sub ) : ?>
			<p class="bw-hero-small__subheading"><?php echo wp_kses_post( $bw_sub ); ?></p>
		<?php endif; ?>
	</div>
</div>
