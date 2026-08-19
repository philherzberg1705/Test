<?php
/**
 * Ein Eintrag der USP-Icons.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_icon  = $attributes['icon'] ?? 'check';
$bw_title = $attributes['title'] ?? '';
$bw_text  = $attributes['text'] ?? '';

if ( '' === trim( wp_strip_all_tags( $bw_title ) ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-icon-list__item' ) ); ?>>
	<?php echo bodywings_icon( $bw_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<h3 class="bw-icon-list__item-title"><?php echo wp_kses_post( $bw_title ); ?></h3>
	<?php if ( $bw_text ) : ?>
		<p class="bw-icon-list__item-text"><?php echo wp_kses_post( $bw_text ); ?></p>
	<?php endif; ?>
</div>
