<?php
/**
 * USP-Icons: Container für bw/icon-list-item-Kinder.
 *
 * @var array  $attributes
 * @var string $content Bereits gerenderte InnerBlocks (icon-list-item).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading = $attributes['heading'] ?? '';
$bw_columns = (int) ( $attributes['columns'] ?? 3 );
$bw_columns = in_array( $bw_columns, array( 2, 3, 4 ), true ) ? $bw_columns : 3;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-icon-list' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-icon-list__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>

		<div class="bw-icon-list__grid bw-icon-list__grid--<?php echo esc_attr( (string) $bw_columns ); ?>">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- InnerBlocks-Output, von WordPress selbst gerendert. ?>
		</div>
	</div>
</div>
