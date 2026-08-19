<?php
/**
 * WooCommerce-Template-Overrides (§30/§31).
 *
 * WooCommerce sucht Templates automatisch zuerst unter
 * get_stylesheet_directory() . '/' . woocommerce_template_path() (Default
 * "woocommerce/") — durch add_theme_support('woocommerce') in
 * inc/core/setup.php ist das bereits aktiv. Dieser Filter macht den Pfad
 * nur explizit dokumentiert, statt sich auf den WC-internen Default zu
 * verlassen, und ist der zentrale Ort, falls der Pfad je geändert werden
 * müsste.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

add_filter( 'woocommerce_template_path', function (): string {
	return 'woocommerce/';
} );

/**
 * WooCommerce rendert Breadcrumbs/Sidebar standardmäßig über den entfernten
 * Content-Wrapper (siehe inc/core/setup.php). Eigene Platzierung erfolgt
 * gezielt in den jeweiligen Templates (Product Page, Shop-Archiv), daher
 * hier keine globale Wiederherstellung.
 */

// Kein klassischer Widget-Sidebar-Bereich im minimalistischen Layout (§1).
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );
