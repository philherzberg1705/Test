<?php
/**
 * Ein FAQ-Eintrag — natives <details>/<summary> statt eigenem JS-
 * Akkordeon (§25/§26: barrierefrei und funktional ohne JavaScript).
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_question = $attributes['question'] ?? '';
$bw_answer   = $attributes['answer'] ?? '';

if ( '' === trim( wp_strip_all_tags( $bw_question ) ) ) {
	return;
}
?>
<details <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-faq__item' ) ); ?>>
	<summary class="bw-faq__question">
		<?php echo wp_kses_post( $bw_question ); ?>
		<?php echo bodywings_icon( 'chevron-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</summary>
	<div class="bw-faq__answer"><?php echo wp_kses_post( $bw_answer ); ?></div>
</details>
