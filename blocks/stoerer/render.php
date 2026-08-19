<?php
/**
 * Störer: schmales Hinweisband mit Icon + Text + optionalem Link.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_text      = $attributes['text'] ?? '';
$bw_icon      = $attributes['icon'] ?? 'star';
$bw_link_text = $attributes['linkText'] ?? '';
$bw_link_url  = $attributes['linkUrl'] ?? '';

if ( '' === trim( wp_strip_all_tags( $bw_text ) ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-stoerer' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner bw-stoerer__inner">
		<?php if ( 'none' !== $bw_icon ) : ?>
			<?php echo bodywings_icon( $bw_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- statisches SVG-Markup ?>
		<?php endif; ?>

		<span class="bw-stoerer__text"><?php echo wp_kses_post( $bw_text ); ?></span>

		<?php if ( $bw_link_text && $bw_link_url ) : ?>
			<a class="bw-stoerer__link" href="<?php echo esc_url( $bw_link_url ); ?>"><?php echo esc_html( $bw_link_text ); ?></a>
		<?php endif; ?>
	</div>
</div>
