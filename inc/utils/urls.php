<?php
/**
 * Generische URL-Helfer, die an keinen bestimmten Bereich gebunden sind
 * (WooCommerce hat dafür bereits inc/utils/woocommerce-guards.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL des Beitrags-Archivs — respektiert die "Startseite zeigt: Statische
 * Seite"-Einstellung (Einstellungen → Lesen) statt home_url('/') fest
 * anzunehmen, das wäre bei gesetzter "Beiträge"-Seite falsch.
 */
function bodywings_get_blog_archive_url(): string {
	if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) {
		return get_permalink( (int) get_option( 'page_for_posts' ) );
	}

	return home_url( '/' );
}
