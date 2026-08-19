<?php
/**
 * Newsletter-CTA-Block — nutzt exakt dasselbe Formular-Markup und denselben
 * AJAX-Handler wie der Footer (§19/§33), damit assets/js/ajax/newsletter.js
 * unverändert für beide funktioniert.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading = $attributes['heading'] ?? '';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-newsletter-block' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner bw-newsletter-block__inner">
		<div class="bw-newsletter">
			<?php if ( $bw_heading ) : ?>
				<h2 class="bw-newsletter__title"><?php echo wp_kses_post( $bw_heading ); ?></h2>
			<?php endif; ?>
			<?php get_template_part( 'template-parts/newsletter-form', null, array( 'intro' => $attributes['intro'] ?? '' ) ); ?>
		</div>
	</div>
</div>
