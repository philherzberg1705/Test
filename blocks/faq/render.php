<?php
/**
 * FAQ: Container für bw/faq-item-Kinder + FAQPage-Structured-Data (§27) —
 * Google-Rich-Results für FAQs, aus denselben Kindattributen erzeugt wie
 * die sichtbare Ausgabe (§33: eine Datenquelle statt zwei).
 *
 * @var array    $attributes
 * @var string   $content Bereits gerenderte InnerBlocks (faq-item).
 * @var WP_Block $block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
	return;
}

$bw_heading = $attributes['heading'] ?? '';

$bw_faq_entities = array();
foreach ( $block->inner_blocks ?? array() as $bw_inner ) {
	if ( 'bw/faq-item' !== $bw_inner->name ) {
		continue;
	}

	$bw_question = trim( wp_strip_all_tags( $bw_inner->attributes['question'] ?? '' ) );
	$bw_answer   = trim( wp_strip_all_tags( $bw_inner->attributes['answer'] ?? '' ) );

	if ( ! $bw_question || ! $bw_answer ) {
		continue;
	}

	$bw_faq_entities[] = array(
		'@type'          => 'Question',
		'name'           => $bw_question,
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $bw_answer,
		),
	);
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-faq' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner bw-faq__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-faq__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>

		<div class="bw-faq__list">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- InnerBlocks-Output. ?>
		</div>
	</div>

	<?php if ( $bw_faq_entities ) : ?>
		<script type="application/ld+json"><?php echo wp_json_encode( array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $bw_faq_entities,
		), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() escaped bereits. ?></script>
	<?php endif; ?>
</div>
