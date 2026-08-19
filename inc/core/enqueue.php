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
	}

	if ( bodywings_is_woocommerce_active() && ( is_shop() || is_product_taxonomy() || is_search() ) ) {
		wp_enqueue_style(
			'bodywings-shop',
			BODYWINGS_URI . '/assets/css/components/shop.css',
			array( 'bodywings-base' ),
			bodywings_asset_version( $css_dir . '/components/shop.css' )
		);
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
