<?php
/**
 * Offcanvas-Hauptmenü. Mobile: fullscreen. Desktop: seitliches Panel
 * (siehe assets/css/components/offcanvas.css). Enthält auf kleinen
 * Screens zusätzlich Suche/Wishlist/Konto/Sprache, da die Header-Bar dort
 * reduziert ist (§6 "Mobile ist keine bloße verkleinerte Desktop-Version").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bw-offcanvas bw-offcanvas--menu" data-bw-offcanvas="menu" id="bw-offcanvas-menu" aria-hidden="true" inert>
	<div class="bw-offcanvas__backdrop" data-bw-offcanvas-close></div>
	<div class="bw-offcanvas__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Hauptmenü', 'bodywings' ); ?>">
		<button type="button" class="bw-icon-btn bw-offcanvas__close" data-bw-offcanvas-close>
			<?php echo bodywings_icon( 'close' ); // phpcs:ignore ?>
			<span class="bw-visually-hidden"><?php esc_html_e( 'Menü schließen', 'bodywings' ); ?></span>
		</button>

		<nav class="bw-offcanvas__nav" aria-label="<?php esc_attr_e( 'Hauptnavigation', 'bodywings' ); ?>">
			<?php
			if ( has_nav_menu( 'mobile' ) || has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => has_nav_menu( 'mobile' ) ? 'mobile' : 'primary',
					'container'      => false,
					'menu_class'     => 'bw-offcanvas__nav-list',
					'fallback_cb'    => false,
				) );
			}
			?>
		</nav>

		<div class="bw-offcanvas__secondary">
			<button type="button" class="bw-offcanvas__secondary-link" data-bw-offcanvas-trigger="search">
				<?php echo bodywings_icon( 'search' ); // phpcs:ignore ?>
				<?php esc_html_e( 'Suche', 'bodywings' ); ?>
			</button>
			<a class="bw-offcanvas__secondary-link" href="<?php echo esc_url( bodywings_get_wishlist_url() ); ?>">
				<?php echo bodywings_icon( 'heart' ); // phpcs:ignore ?>
				<?php esc_html_e( 'Wunschliste', 'bodywings' ); ?>
			</a>
			<a class="bw-offcanvas__secondary-link" href="<?php echo esc_url( bodywings_get_account_url() ); ?>">
				<?php echo bodywings_icon( 'user' ); // phpcs:ignore ?>
				<?php esc_html_e( 'Konto', 'bodywings' ); ?>
			</a>
			<button type="button" class="bw-offcanvas__secondary-link" data-bw-offcanvas-trigger="locale">
				<?php echo bodywings_icon( 'globe' ); // phpcs:ignore ?>
				<?php esc_html_e( 'Sprache / Währung', 'bodywings' ); ?>
			</button>
		</div>
	</div>
</div>
