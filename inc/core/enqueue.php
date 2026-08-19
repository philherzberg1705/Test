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

	$js_dir = BODYWINGS_DIR . '/assets/js';

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

	wp_localize_script( 'bodywings-core', 'bodywingsData', array(
		'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
		'nonce'          => wp_create_nonce( 'bodywings_ajax' ),
		'isUserLoggedIn' => is_user_logged_in(),
		'reducedMotion'  => false, // wird clientseitig per matchMedia überschrieben.
	) );
}

/**
 * Nutzt filemtime als Cache-Buster im Dev-Betrieb, fällt auf die
 * Theme-Version zurück, wenn die Datei (noch) fehlt.
 */
function bodywings_asset_version( string $absolute_path ): string {
	return file_exists( $absolute_path ) ? (string) filemtime( $absolute_path ) : BODYWINGS_VERSION;
}
