<?php
/**
 * Asset-Loading. Nur das Nötigste, siehe CLAUDE.md §26.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Eigenes Design-System ersetzt die WooCommerce-Standardstyles vollständig.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_action( 'wp_enqueue_scripts', 'bodywings_enqueue_assets' );

function bodywings_enqueue_assets(): void {
	$css_dir = BODYWINGS_DIR . '/assets/css';

	// Google Fonts — bewusst vorläufig, siehe TODO in base.css. Family-Namen
	// sind identisch zu tokens.css, damit der spätere Wechsel auf selbst
	// gehostete Dateien ohne CSS-Änderung auskommt.
	wp_enqueue_style(
		'bodywings-google-fonts',
		'https://fonts.googleapis.com/css2?' . implode( '&', array(
			'family=Special+Gothic+Condensed+One',
			'family=Open+Sans:wght@400;600;700',
			'display=swap',
		) ),
		array(),
		null
	);

	// tokens.css definiert ausschließlich Custom Properties, base.css die
	// Basiselemente. Beide sind auf jeder Seite nötig (Design-System-Pflicht §2).
	wp_enqueue_style(
		'bodywings-tokens',
		BODYWINGS_URI . '/assets/css/tokens.css',
		array(),
		bodywings_asset_version( $css_dir . '/tokens.css' )
	);

	wp_enqueue_style(
		'bodywings-base',
		BODYWINGS_URI . '/assets/css/base.css',
		array( 'bodywings-tokens' ),
		bodywings_asset_version( $css_dir . '/base.css' )
	);

	// Header + Offcanvas rendern auf jeder Seite (Theme-Shell), daher global.
	wp_enqueue_style(
		'bodywings-header',
		BODYWINGS_URI . '/assets/css/components/header.css',
		array( 'bodywings-base' ),
		bodywings_asset_version( $css_dir . '/components/header.css' )
	);

	wp_enqueue_style(
		'bodywings-offcanvas',
		BODYWINGS_URI . '/assets/css/components/offcanvas.css',
		array( 'bodywings-base' ),
		bodywings_asset_version( $css_dir . '/components/offcanvas.css' )
	);

	wp_enqueue_style(
		'bodywings-footer',
		BODYWINGS_URI . '/assets/css/components/footer.css',
		array( 'bodywings-base' ),
		bodywings_asset_version( $css_dir . '/components/footer.css' )
	);

	if ( bodywings_is_woocommerce_active() ) {
		wp_enqueue_style(
			'bodywings-cart',
			BODYWINGS_URI . '/assets/css/components/cart.css',
			array( 'bodywings-offcanvas' ),
			bodywings_asset_version( $css_dir . '/components/cart.css' )
		);
	}

	$js_dir = BODYWINGS_DIR . '/assets/js';

	// Nur auf Seiten mit Produktkarten laden (§26 — keine unnötigen Assets).
	if ( function_exists( 'bodywings_is_product_grid_context' ) && bodywings_is_product_grid_context() ) {
		wp_enqueue_style(
			'bodywings-product-card',
			BODYWINGS_URI . '/assets/css/components/product-card.css',
			array( 'bodywings-base' ),
			bodywings_asset_version( $css_dir . '/components/product-card.css' )
		);

		wp_enqueue_script(
			'bodywings-product-card',
			BODYWINGS_URI . '/assets/js/product-card.js',
			array( 'bodywings-core' ),
			bodywings_asset_version( $js_dir . '/product-card.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		// GSAP nur für komplexe Scroll-Reveals (§23) — hier selbst gehostet,
		// nicht per CDN, und ausschließlich dort geladen, wo [data-bw-reveal]
		// tatsächlich vorkommt.
		wp_enqueue_script(
			'bodywings-gsap',
			BODYWINGS_URI . '/assets/js/vendor/gsap/gsap.min.js',
			array(),
			'3.15.0',
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		wp_enqueue_script(
			'bodywings-gsap-scrolltrigger',
			BODYWINGS_URI . '/assets/js/vendor/gsap/ScrollTrigger.min.js',
			array( 'bodywings-gsap' ),
			'3.15.0',
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		wp_enqueue_script(
			'bodywings-motion',
			BODYWINGS_URI . '/assets/js/motion.js',
			array( 'bodywings-core', 'bodywings-gsap-scrolltrigger' ),
			bodywings_asset_version( $js_dir . '/motion.js' ),
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
	}

	if ( bodywings_is_woocommerce_active() && ( is_shop() || is_product_taxonomy() || is_search() || is_page_template( 'template-wishlist.php' ) ) ) {
		wp_enqueue_style(
			'bodywings-shop',
			BODYWINGS_URI . '/assets/css/components/shop.css',
			array( 'bodywings-base' ),
			bodywings_asset_version( $css_dir . '/components/shop.css' )
		);
	}

	if ( bodywings_is_woocommerce_active() && ( is_shop() || is_product_taxonomy() ) ) {
		wp_enqueue_style(
			'bodywings-filter',
			BODYWINGS_URI . '/assets/css/components/filter.css',
			array( 'bodywings-offcanvas' ),
			bodywings_asset_version( $css_dir . '/components/filter.css' )
		);

		wp_enqueue_script(
			'bodywings-filter',
			BODYWINGS_URI . '/assets/js/ajax/filter.js',
			array( 'bodywings-offcanvas' ),
			bodywings_asset_version( $js_dir . '/ajax/filter.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	if ( bodywings_is_woocommerce_active() && is_account_page() ) {
		wp_enqueue_style(
			'bodywings-auth',
			BODYWINGS_URI . '/assets/css/components/auth.css',
			array( 'bodywings-base' ),
			bodywings_asset_version( $css_dir . '/components/auth.css' )
		);

		wp_enqueue_script(
			'bodywings-auth-tabs',
			BODYWINGS_URI . '/assets/js/auth-tabs.js',
			array( 'bodywings-core' ),
			bodywings_asset_version( $js_dir . '/auth-tabs.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	if ( bodywings_is_woocommerce_active() && is_product() ) {
		wp_enqueue_style(
			'bodywings-product-page',
			BODYWINGS_URI . '/assets/css/components/product-page.css',
			array( 'bodywings-base' ),
			bodywings_asset_version( $css_dir . '/components/product-page.css' )
		);

		wp_enqueue_script(
			'bodywings-variations',
			BODYWINGS_URI . '/assets/js/variations.js',
			array( 'jquery', 'bodywings-core' ),
			bodywings_asset_version( $js_dir . '/variations.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		wp_localize_script( 'bodywings-variations', 'bodywingsProductData', array(
			'attributeImages' => function_exists( 'bodywings_get_visual_attribute_images_map' )
				? bodywings_get_visual_attribute_images_map()
				: array(),
		) );
	}

	wp_enqueue_script(
		'bodywings-core',
		BODYWINGS_URI . '/assets/js/core.js',
		array(),
		bodywings_asset_version( $js_dir . '/core.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	wp_enqueue_script(
		'bodywings-offcanvas',
		BODYWINGS_URI . '/assets/js/offcanvas.js',
		array( 'bodywings-core' ),
		bodywings_asset_version( $js_dir . '/offcanvas.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( bodywings_is_woocommerce_active() ) {
		wp_enqueue_script(
			'bodywings-wishlist',
			BODYWINGS_URI . '/assets/js/ajax/wishlist.js',
			array( 'bodywings-core' ),
			bodywings_asset_version( $js_dir . '/ajax/wishlist.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	wp_enqueue_script(
		'bodywings-search',
		BODYWINGS_URI . '/assets/js/ajax/search.js',
		array( 'bodywings-offcanvas' ),
		bodywings_asset_version( $js_dir . '/ajax/search.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( bodywings_is_woocommerce_active() ) {
		wp_enqueue_script(
			'bodywings-cart',
			BODYWINGS_URI . '/assets/js/ajax/cart.js',
			array( 'bodywings-offcanvas' ),
			bodywings_asset_version( $js_dir . '/ajax/cart.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	wp_enqueue_script(
		'bodywings-newsletter',
		BODYWINGS_URI . '/assets/js/ajax/newsletter.js',
		array( 'bodywings-core' ),
		bodywings_asset_version( $js_dir . '/ajax/newsletter.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	wp_localize_script( 'bodywings-core', 'bodywingsData', array(
		'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
		'nonce'          => wp_create_nonce( 'bodywings_ajax' ),
		'isUserLoggedIn' => is_user_logged_in(),
		'reducedMotion'  => false, // wird clientseitig per matchMedia überschrieben.
	) );
}

add_action( 'wp_enqueue_scripts', 'bodywings_dequeue_unused_woocommerce_scripts', 20 );

/**
 * Entfernt WooCommerce-Default-Skripte, die durch das eigene AJAX-System
 * ersetzt sind und sonst unnötige Requests/Polling verursachen (§26):
 * - wc-cart-fragments pollt periodisch admin-ajax.php für Fragment-Widgets,
 *   die dieses Theme nicht rendert (eigener Side-Cart, siehe inc/ajax/cart.php).
 * - wc-add-to-cart hängt an .ajax_add_to_cart-Loop-Buttons, die die eigene
 *   Produktkarte (§8) nie ausgibt.
 * wc-add-to-cart-variation bleibt aktiv — Variantenwechsel (§12) baut
 * bewusst auf WooCommerce-Kernlogik statt eigenem Nachbau (§31).
 */
function bodywings_dequeue_unused_woocommerce_scripts(): void {
	if ( ! bodywings_is_woocommerce_active() ) {
		return;
	}

	wp_dequeue_script( 'wc-cart-fragments' );
	wp_dequeue_script( 'wc-add-to-cart' );
}

add_filter( 'wp_resource_hints', 'bodywings_google_fonts_preconnect', 10, 2 );

/**
 * Preconnect für Google Fonts, solange die Schriften nicht selbst gehostet
 * werden (siehe TODO in base.css) — verkürzt die Ladezeit spürbar.
 */
function bodywings_google_fonts_preconnect( array $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => '',
		);
	}

	return $urls;
}

/**
 * Nutzt filemtime als Cache-Buster im Dev-Betrieb, fällt auf die
 * Theme-Version zurück, wenn die Datei (noch) fehlt.
 */
function bodywings_asset_version( string $absolute_path ): string {
	return file_exists( $absolute_path ) ? (string) filemtime( $absolute_path ) : BODYWINGS_VERSION;
}
