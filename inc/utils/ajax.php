<?php
/**
 * Gemeinsame AJAX-Helfer (§15/§32). Jeder AJAX-Handler im Theme prüft die
 * Nonce über diese eine Funktion statt eigener Copy-Paste-Prüfung.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_ajax_verify_nonce(): bool {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- check_ajax_referer IST die Prüfung.
	return (bool) check_ajax_referer( 'bodywings_ajax', 'nonce', false );
}

/**
 * Einheitliche Fehlerantwort bei fehlgeschlagener Nonce-Prüfung.
 */
function bodywings_ajax_send_invalid_nonce(): void {
	wp_send_json_error(
		array( 'message' => __( 'Sicherheitsprüfung fehlgeschlagen. Bitte Seite neu laden.', 'bodywings' ) ),
		403
	);
}
