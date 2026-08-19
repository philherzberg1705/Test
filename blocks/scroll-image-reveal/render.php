<?php
/**
 * Scroll-Bild-Reveal: Bild wächst beim Scrollen bis auf volle
 * Bildschirmbreite, danach blenden Text + Headline-Verschiebung ein
 * (§21/§23 — komplexe Scroll-Sequenz, siehe assets/js/block-scroll-reveal.js
 * für die GSAP-ScrollTrigger-Pin/Scrub-Logik).
 *
 * Ohne JS bzw. bei prefers-reduced-motion bleibt es bei der hier
 * gerenderten statischen Ausgangsansicht — Bild bereits volle Breite,
 * Headline darauf, Text direkt darunter sichtbar (§25: volle
 * Funktionalität ohne Animation).
 *
 * Das Bild liegt bewusst AUSSERHALB von .bw-block__inner (keine max-
 * width-Beschränkung) statt über 100vw/negative Margins zu gehen — so
 * wird es automatisch genauso breit wie sein nicht beschränkter
 * Elternbereich, ganz ohne die bekannte 100vw-Scrollbar-Falle.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading = $attributes['heading'] ?? '';
$bw_text    = $attributes['text'] ?? '';
$bw_image   = $attributes['imageUrl'] ?? '';
$bw_alt     = $attributes['imageAlt'] ?? '';
$bw_tag     = ! empty( $attributes['asH1'] ) ? 'h1' : 'h2';

if ( ! $bw_image || '' === trim( wp_strip_all_tags( $bw_heading ) ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-scroll-reveal' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-scroll-reveal__scroller" data-bw-scroll-reveal>
		<div class="bw-scroll-reveal__stage" data-bw-scroll-reveal-stage>
			<div class="bw-scroll-reveal__media" data-bw-scroll-reveal-media>
				<img
					class="bw-scroll-reveal__image"
					src="<?php echo esc_url( $bw_image ); ?>"
					alt="<?php echo esc_attr( $bw_alt ); ?>"
					loading="lazy"
					decoding="async"
				/>
				<div class="bw-scroll-reveal__scrim" aria-hidden="true"></div>
				<?php echo sprintf( '<%1$s class="bw-scroll-reveal__heading" data-bw-scroll-reveal-heading>%2$s</%1$s>', esc_html( $bw_tag ), wp_kses_post( $bw_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php if ( $bw_text ) : ?>
				<div class="bw-block__inner">
					<div class="bw-scroll-reveal__text" data-bw-scroll-reveal-text>
						<?php echo wp_kses_post( $bw_text ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
