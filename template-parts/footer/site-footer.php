<?php
/**
 * Site-Footer: Newsletter-Modul (§19) + Footer-Navigation + Copyright.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_privacy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
?>
<footer class="bw-site-footer" data-bw-bg="green">
	<div class="bw-container bw-site-footer__inner">
		<div class="bw-site-footer__top">
			<div class="bw-newsletter">
				<h2 class="bw-newsletter__title"><?php esc_html_e( 'Newsletter', 'bodywings' ); ?></h2>
				<p class="bw-newsletter__intro">
					<?php esc_html_e( 'Neuigkeiten und Angebote direkt in dein Postfach.', 'bodywings' ); ?>
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
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav class="bw-site-footer__nav" aria-label="<?php esc_attr_e( 'Footer-Navigation', 'bodywings' ); ?>">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'bw-site-footer__nav-list',
						'fallback_cb'    => false,
					) );
					?>
				</nav>
			<?php endif; ?>
		</div>

		<div class="bw-site-footer__bottom">
			<p class="bw-text-small">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</p>
		</div>
	</div>
</footer>
