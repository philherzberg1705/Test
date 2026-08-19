<?php
/**
 * Legt die lokale Subscriber-Tabelle an (Default-Provider, kein Plugin/API
 * nötig). Läuft nur bei Theme-Aktivierung, nicht auf jedem Request.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_newsletter_table_name(): string {
	global $wpdb;

	return $wpdb->prefix . 'bw_newsletter_subscribers';
}

add_action( 'after_switch_theme', 'bodywings_newsletter_install_table' );

function bodywings_newsletter_install_table(): void {
	global $wpdb;

	$table_name      = bodywings_newsletter_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table_name} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		email VARCHAR(190) NOT NULL,
		source VARCHAR(50) NOT NULL DEFAULT 'footer',
		consent_at DATETIME NOT NULL,
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}
