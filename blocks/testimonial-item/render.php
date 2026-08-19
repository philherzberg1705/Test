<?php
/**
 * Eine Bewertung innerhalb des Testimonial-Sliders.
 *
 * Bewusst OHNE Review/AggregateRating-Structured-Data: Google wertet
 * selbst eingestellte Kund:innen-Stimmen auf der eigenen Website nicht als
 * rich-result-fähige Bewertungen (Spam-Richtlinien für "self-serving"
 * Reviews) — hier reicht die rein visuelle Sternedarstellung.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_quote  = $attributes['quote'] ?? '';
$bw_name   = $attributes['name'] ?? '';
$bw_role   = $attributes['role'] ?? '';
$bw_avatar = $attributes['avatarUrl'] ?? '';
$bw_rating = max( 0, min( 5, (int) ( $attributes['rating'] ?? 5 ) ) );

if ( '' === trim( wp_strip_all_tags( $bw_quote ) ) ) {
	return;
}
?>
<li <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-testimonial-slider__item' ) ); ?>>
	<?php if ( $bw_rating > 0 ) : ?>
		<div class="bw-testimonial-slider__rating" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating out of 5 */ __( '%d von 5 Sternen', 'bodywings' ), $bw_rating ) ); ?>">
			<?php for ( $bw_i = 0; $bw_i < 5; $bw_i++ ) : ?>
				<span class="<?php echo $bw_i < $bw_rating ? 'is-filled' : ''; ?>"><?php echo bodywings_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php endfor; ?>
		</div>
	<?php endif; ?>

	<blockquote class="bw-testimonial-slider__quote"><?php echo wp_kses_post( $bw_quote ); ?></blockquote>

	<div class="bw-testimonial-slider__author">
		<?php if ( $bw_avatar ) : ?>
			<img class="bw-testimonial-slider__avatar" src="<?php echo esc_url( $bw_avatar ); ?>" alt="" loading="lazy" decoding="async" />
		<?php endif; ?>
		<div>
			<?php if ( $bw_name ) : ?>
				<p class="bw-testimonial-slider__name"><?php echo esc_html( $bw_name ); ?></p>
			<?php endif; ?>
			<?php if ( $bw_role ) : ?>
				<p class="bw-testimonial-slider__role"><?php echo esc_html( $bw_role ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</li>
