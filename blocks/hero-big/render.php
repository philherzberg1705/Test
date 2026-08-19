<?php
/**
 * Hero (groß). $attributes kommt aus dem Block-Editor, siehe block.json.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading   = $attributes['heading'] ?? '';
$bw_sub       = $attributes['subheading'] ?? '';
$bw_bg_image  = $attributes['backgroundImageUrl'] ?? '';
$bw_btn_text  = $attributes['buttonText'] ?? '';
$bw_btn_url   = $attributes['buttonUrl'] ?? '';
$bw_btn2_text = $attributes['buttonText2'] ?? '';
$bw_btn2_url  = $attributes['buttonUrl2'] ?? '';
$bw_align     = 'center' === ( $attributes['contentAlign'] ?? 'left' ) ? 'center' : 'left';
$bw_tag       = ! empty( $attributes['asH1'] ) ? 'h1' : 'h2';

if ( '' === trim( wp_strip_all_tags( $bw_heading ) ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-hero-big bw-hero-big--' . $bw_align ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- eigene, escapte Helper-Ausgabe. ?>
	<?php if ( $bw_bg_image ) : ?>
		<div class="bw-hero-big__media" style="background-image:url('<?php echo esc_url( $bw_bg_image ); ?>');" role="img" aria-label="<?php echo esc_attr( wp_strip_all_tags( $bw_heading ) ); ?>"></div>
		<div class="bw-hero-big__overlay"></div>
	<?php endif; ?>

	<div class="bw-block__inner bw-hero-big__inner">
		<div class="bw-hero-big__content">
			<?php echo sprintf( '<%1$s class="bw-hero-big__heading">%2$s</%1$s>', esc_html( $bw_tag ), wp_kses_post( $bw_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Tag/Inhalt oben escaped. ?>

			<?php if ( $bw_sub ) : ?>
				<p class="bw-hero-big__subheading"><?php echo wp_kses_post( $bw_sub ); ?></p>
			<?php endif; ?>

			<?php if ( $bw_btn_text && $bw_btn_url || $bw_btn2_text && $bw_btn2_url ) : ?>
				<div class="bw-hero-big__actions">
					<?php if ( $bw_btn_text && $bw_btn_url ) : ?>
						<a class="bw-btn" href="<?php echo esc_url( $bw_btn_url ); ?>"><?php echo esc_html( $bw_btn_text ); ?></a>
					<?php endif; ?>
					<?php if ( $bw_btn2_text && $bw_btn2_url ) : ?>
						<a class="bw-btn bw-btn--outline" href="<?php echo esc_url( $bw_btn2_url ); ?>"><?php echo esc_html( $bw_btn2_text ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
