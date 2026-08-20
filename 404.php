<?php
/**
 * 404-Template (§39 Premium/Minimal statt WordPress-Standard-Fehlerseite).
 *
 * Eigene Such-Eingabe statt eines Verweises auf die Offcanvas-Suche (§14):
 * funktioniert auch ganz ohne JS/bei deaktiviertem JavaScript vollständig
 * (§25 — Funktionalität darf nicht von Animation/JS abhängen).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="bw-container bw-404">
	<p class="bw-404__eyebrow"><?php esc_html_e( 'Fehler 404', 'bodywings' ); ?></p>
	<h1 class="bw-404__code" aria-hidden="true">404</h1>
	<h2 class="bw-404__heading"><?php esc_html_e( 'Diese Seite gibt es leider nicht.', 'bodywings' ); ?></h2>
	<p class="bw-404__text">
		<?php esc_html_e( 'Der Link ist veraltet oder die Seite wurde verschoben. Nutze die Suche oder eine der folgenden Seiten.', 'bodywings' ); ?>
	</p>

	<form class="bw-404__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="bw-visually-hidden" for="bw-404-search"><?php esc_html_e( 'Suchbegriff', 'bodywings' ); ?></label>
		<input
			type="search"
			id="bw-404-search"
			name="s"
			class="bw-input bw-404__search-input"
			placeholder="<?php esc_attr_e( 'Wonach suchst du?', 'bodywings' ); ?>"
			required
		/>
		<button type="submit" class="bw-btn bw-404__search-submit">
			<?php esc_html_e( 'Suchen', 'bodywings' ); ?>
		</button>
	</form>

	<div class="bw-404__links">
		<a class="bw-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Zur Startseite', 'bodywings' ); ?>
		</a>
		<?php if ( bodywings_is_woocommerce_active() ) : ?>
			<a class="bw-btn bw-btn--outline" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Zum Shop', 'bodywings' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
