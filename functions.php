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
 * Alle .php-Dateien unterhalb eines Verzeichnisses, rekursiv, alphabetisch
 * nach vollem Pfad sortiert.
 */
function bodywings_collect_php_files( string $dir ): array {
	if ( ! is_dir( $dir ) ) {
		return array();
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS )
	);

	$files = array();

	foreach ( $iterator as $file ) {
		if ( $file->isFile() && 'php' === $file->getExtension() ) {
			$files[] = $file->getPathname();
		}
	}

	sort( $files );

	return $files;
}

/**
 * Lädt rekursiv alle .php-Dateien in einem inc/-Unterverzeichnis. Fehlende
 * Verzeichnisse werden übersprungen, damit spätere Module ohne Änderungen
 * an dieser Datei ergänzt werden können.
 */
function bodywings_load_module_dir( string $relative_dir ): void {
	foreach ( bodywings_collect_php_files( BODYWINGS_DIR . '/' . trim( $relative_dir, '/' ) ) as $file ) {
		require_once $file;
	}
}

/*
 * Interfaces zuerst: einige Module (z.B. Newsletter-, später Currency-
 * Provider) trennen ein Interface von austauschbaren Implementierungen im
 * selben Unterordner. require_once macht ein doppeltes Laden beim
 * anschließenden regulären Modul-Durchlauf ungefährlich.
 */
foreach ( bodywings_collect_php_files( BODYWINGS_DIR . '/inc' ) as $bodywings_file ) {
	if ( str_starts_with( basename( $bodywings_file ), 'interface-' ) ) {
		require_once $bodywings_file;
	}
}
unset( $bodywings_file );

$bodywings_modules = array(
	'inc/utils',
	'inc/core',
	'inc/admin',
	'inc/integrations',
	'inc/woocommerce',
	'inc/components',
	'inc/ajax',
	// Eigener Bereich statt inc/components: Stellenausschreibungen sind ein
	// in sich abgeschlossenes Feature (Post-Type, Taxonomien, Meta-Boxen,
	// Structured Data, Bewerbungs-Handling) von vergleichbarem Umfang wie
	// inc/woocommerce, nicht bloß eine wiederverwendbare UI-Komponente.
	'inc/jobs',
	// Ebenfalls ein eigenständiges Feature (Rollen-Einstellung, User-Profil-
	// Meta, WooCommerce-Konto-Endpoint, Content-Block) statt einer bloßen
	// UI-Komponente in inc/components.
	'inc/business-search',
);

foreach ( $bodywings_modules as $bodywings_module ) {
	bodywings_load_module_dir( $bodywings_module );
}

unset( $bodywings_modules, $bodywings_module );
