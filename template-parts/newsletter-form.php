<?php
/**
 * Newsletter-Formular (§19), von Footer UND dem Newsletter-CTA-Block
 * (blocks/newsletter-cta) gemeinsam genutzt (§33) — identisches Markup,
 * damit assets/js/ajax/newsletter.js unverändert für beide funktioniert.
 *
 * $args['intro'] (optional): überschreibt den Standard-Intro-Text.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_privacy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
$bw_intro       = ( $args['intro'] ?? '' ) ?: __( 'Neuigkeiten und Angebote direkt in dein Postfach.', 'bodywings' );
?>
<p class="bw-newsletter__intro">
	<?php echo esc_html( $bw_intro ); ?>
</p>

<form class="bw-newsletter-form" data-bw-newsletter-form novalidate>
	<div class="bw-newsletter-form__row">
		<label class="bw-visually-hidden" for="bw-newsletter-email">
			<?php esc_html_e( 'E-Mail-Adresse', 'bodywings' ); ?>
		</label>
		<input
			type="email"
			id="bw-newsletter-email"
			name="email"
			class="bw-input bw-newsletter-form__input"
			placeholder="<?php esc_attr_e( 'deine@email.de', 'bodywings' ); ?>"
			autocomplete="email"
			required
		/>
		<button type="submit" class="bw-btn bw-newsletter-form__submit" data-bw-newsletter-submit>
			<?php esc_html_e( 'Anmelden', 'bodywings' ); ?>
		</button>
	</div>

	<label class="bw-newsletter-form__consent">
		<input type="checkbox" name="consent" required />
		<span>
			<?php
			printf(
				/* translators: %s: link to privacy policy */
				esc_html__( 'Ich stimme der Verarbeitung meiner E-Mail-Adresse zum Newsletter-Versand zu. Abmeldung jederzeit möglich. %s', 'bodywings' ),
				$bw_privacy_url
					? '<a href="' . esc_url( $bw_privacy_url ) . '">' . esc_html__( 'Datenschutz', 'bodywings' ) . '</a>'
					: ''
			);
			?>
		</span>
	</label>

	<p class="bw-newsletter-form__status" data-bw-newsletter-status role="status" aria-live="polite"></p>
</form>
