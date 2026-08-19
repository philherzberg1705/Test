<?php
/**
 * BODYWINGS Theme bootstrap.
 *
 * Lädt ausschließlich Module aus inc/. Enthält selbst keine Logik,
 * siehe CLAUDE.md §30 (keine riesige functions.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BODYWINGS_VERSION', '0.1.0' );
define( 'BODYWINGS_DIR', get_template_directory() );
define( 'BODYWINGS_URI', get_template_directory_uri() );

/**
 * Lädt alle .php-Dateien in einem inc/-Unterverzeichnis (nicht rekursiv,
 * Reihenfolge alphabetisch). Fehlende Verzeichnisse werden übersprungen,
 * damit spätere Module ohne Änderungen an dieser Datei ergänzt werden können.
 */
function bodywings_load_module_dir( string $relative_dir ): void {
	$dir = BODYWINGS_DIR . '/' . trim( $relative_dir, '/' );

	if ( ! is_dir( $dir ) ) {
		return;
	}

	$files = glob( $dir . '/*.php' );

	if ( ! $files ) {
		return;
	}

	sort( $files );

	foreach ( $files as $file ) {
		require_once $file;
	}
}

$bodywings_modules = array(
	'inc/utils',
	'inc/core',
	'inc/admin',
	'inc/integrations',
	'inc/woocommerce',
	'inc/components',
	'inc/ajax',
);

foreach ( $bodywings_modules as $bodywings_module ) {
	bodywings_load_module_dir( $bodywings_module );
}

unset( $bodywings_modules, $bodywings_module );
