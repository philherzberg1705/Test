<?php
/**
 * Site-Header: Logo + 6 zentrale Funktionen (§6).
 * Layout: [Menü] [Logo] [Suche | Sprache/Währung | Wishlist | Konto | Warenkorb]
 * Mobile bekommt eine reduzierte Bar (Menü, Logo, Warenkorb); Suche,
 * Sprache/Währung, Wishlist und Konto wandern dort ins Offcanvas-Menü
 * (siehe assets/css/components/header.css) — bewusst keine reine
 * Verkleinerung der Desktop-Ansicht (§6).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_wishlist_count = bodywings_get_wishlist_count();
$bw_cart_count     = bodywings_get_cart_count();
?>
<header class="bw-site-header" id="bw-site-header">
	<div class="bw-container bw-site-header__bar">
		<button
			type="button"
			class="bw-icon-btn bw-site-header__menu-toggle"
			data-bw-offcanvas-trigger="menu"
			aria-expanded="false"
			aria-controls="bw-offcanvas-menu"
		>
			<?php echo bodywings_icon( 'menu' ); // phpcs:ignore -- statisches, eigenes SVG-Markup ?>
			<span class="bw-visually-hidden"><?php esc_html_e( 'Menü öffnen', 'bodywings' ); ?></span>
		</button>

		<a class="bw-site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				bloginfo( 'name' );
			}
			?>
		</a>

		<div class="bw-site-header__actions">
			<button
				type="button"
				class="bw-icon-btn bw-site-header__action bw-site-header__action--search"
				data-bw-offcanvas-trigger="search"
				aria-expanded="false"
				aria-controls="bw-offcanvas-search"
			>
				<?php echo bodywings_icon( 'search' ); // phpcs:ignore ?>
				<span class="bw-visually-hidden"><?php esc_html_e( 'Suche öffnen', 'bodywings' ); ?></span>
			</button>

			<button
				type="button"
				class="bw-icon-btn bw-site-header__action bw-site-header__action--locale"
				data-bw-offcanvas-trigger="locale"
				aria-expanded="false"
				aria-controls="bw-offcanvas-locale"
			>
				<?php echo bodywings_icon( 'globe' ); // phpcs:ignore ?>
				<span class="bw-visually-hidden"><?php esc_html_e( 'Sprache und Währung', 'bodywings' ); ?></span>
			</button>

			<a
				class="bw-icon-btn bw-site-header__action bw-site-header__action--wishlist"
				href="<?php echo esc_url( bodywings_get_wishlist_url() ); ?>"
			>
				<?php echo bodywings_icon( 'heart' ); // phpcs:ignore ?>
				<span class="bw-visually-hidden"><?php esc_html_e( 'Wunschliste', 'bodywings' ); ?></span>
				<span class="bw-badge" data-bw-wishlist-badge <?php echo 0 === $bw_wishlist_count ? 'hidden' : ''; ?>>
					<?php echo esc_html( (string) $bw_wishlist_count ); ?>
				</span>
			</a>

			<a
				class="bw-icon-btn bw-site-header__action bw-site-header__action--account"
				href="<?php echo esc_url( bodywings_get_account_url() ); ?>"
			>
				<?php echo bodywings_icon( 'user' ); // phpcs:ignore ?>
				<span class="bw-visually-hidden"><?php esc_html_e( 'Konto', 'bodywings' ); ?></span>
			</a>

			<a
				class="bw-icon-btn bw-site-header__action bw-site-header__action--cart"
				href="<?php echo esc_url( bodywings_get_cart_url() ); ?>"
				data-bw-cart-trigger
				data-bw-offcanvas-trigger="cart"
				aria-expanded="false"
				aria-controls="bw-offcanvas-cart"
			>
				<?php echo bodywings_icon( 'bag' ); // phpcs:ignore ?>
				<span class="bw-visually-hidden"><?php esc_html_e( 'Warenkorb', 'bodywings' ); ?></span>
				<span class="bw-badge" data-bw-cart-badge <?php echo 0 === $bw_cart_count ? 'hidden' : ''; ?>>
					<?php echo esc_html( (string) $bw_cart_count ); ?>
				</span>
			</a>
		</div>
	</div>
</header>

<?php
get_template_part( 'template-parts/header/offcanvas-menu' );
get_template_part( 'template-parts/header/offcanvas-search' );
get_template_part( 'template-parts/header/offcanvas-locale' );
get_template_part( 'template-parts/woocommerce/cart-offcanvas' );
