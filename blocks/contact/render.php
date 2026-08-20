<?php
/**
 * Kontakt-Block: Headline/Text + eingebundenes CF7-Formular (§28 — CF7 muss
 * aktiv sein, sonst rendert der Block nichts im Frontend; im Editor gibt
 * es dafür einen Hinweis, siehe edit.js). Formularverarbeitung bleibt
 * vollständig bei CF7 (§31), Optik kommt aus assets/css/components/forms-cf7.css.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading = $attributes['heading'] ?? '';
$bw_text    = $attributes['text'] ?? '';
$bw_form_id = (int) ( $attributes['formId'] ?? 0 );

if ( ! $bw_form_id || ! bodywings_is_cf7_active() || 'wpcf7_contact_form' !== get_post_type( $bw_form_id ) || 'publish' !== get_post_status( $bw_form_id ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-contact-block' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner bw-contact-block__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-contact-block__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>
		<?php if ( $bw_text ) : ?>
			<div class="bw-contact-block__text"><?php echo wp_kses_post( $bw_text ); ?></div>
		<?php endif; ?>
		<div class="bw-contact-block__form">
			<?php echo do_shortcode( '[contact-form-7 id="' . $bw_form_id . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</div>
