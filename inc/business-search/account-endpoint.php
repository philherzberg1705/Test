<?php
/**
 * Mein-Konto-Punkt "Geschäftsdaten" (§29/§31 — WooCommerce-Endpoint-
 * Mechanismus nutzen statt eine eigene Konto-Unterseite drumherum bauen).
 * Erscheint nur für Nutzer:innen mit der in inc/business-search/settings.php
 * konfigurierten Rolle.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_BUSINESS_ENDPOINT = 'geschaeftsdaten';

add_action( 'init', 'bodywings_register_business_account_endpoint' );

function bodywings_register_business_account_endpoint(): void {
	add_rewrite_endpoint( BODYWINGS_BUSINESS_ENDPOINT, EP_ROOT | EP_PAGES );
}

add_action( 'init', 'bodywings_maybe_flush_business_endpoint_rewrite', 20 );

/**
 * Einmaliges Flush nach Deployment/Update (nicht bei jedem Seitenaufruf,
 * §26) — add_rewrite_endpoint() allein reicht nicht, WordPress muss die
 * Rewrite-Regeln einmal neu aufbauen, damit /mein-konto/geschaeftsdaten/
 * funktioniert. Ein bloßer Theme-Wechsel (after_switch_theme) deckt ein
 * bestehendes Live-Update nicht ab, daher zusätzlich versionsbasiert.
 */
function bodywings_maybe_flush_business_endpoint_rewrite(): void {
	if ( '1' !== get_option( 'bodywings_business_endpoint_flushed' ) ) {
		flush_rewrite_rules();
		update_option( 'bodywings_business_endpoint_flushed', '1' );
	}
}

add_filter( 'woocommerce_account_menu_items', 'bodywings_add_business_account_menu_item' );

function bodywings_add_business_account_menu_item( array $items ): array {
	if ( ! bodywings_current_user_is_business_partner() ) {
		return $items;
	}

	$logout = $items['customer-logout'] ?? null;
	unset( $items['customer-logout'] );

	$items[ BODYWINGS_BUSINESS_ENDPOINT ] = __( 'Geschäftsdaten', 'bodywings' );

	if ( $logout ) {
		$items['customer-logout'] = $logout;
	}

	return $items;
}

add_filter( 'woocommerce_endpoint_' . BODYWINGS_BUSINESS_ENDPOINT . '_title', static function (): string {
	return __( 'Geschäftsdaten', 'bodywings' );
} );

add_action( 'woocommerce_account_' . BODYWINGS_BUSINESS_ENDPOINT . '_endpoint', 'bodywings_render_business_account_endpoint' );

function bodywings_render_business_account_endpoint(): void {
	if ( ! bodywings_current_user_is_business_partner() ) {
		echo '<p>' . esc_html__( 'Dieser Bereich ist für dein Konto nicht verfügbar.', 'bodywings' ) . '</p>';
		return;
	}

	$user_id = get_current_user_id();
	bodywings_handle_business_profile_submission( $user_id );
	$profile = bodywings_get_business_profile( $user_id );

	$notice = null;
	if ( isset( $_GET['bw_saved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reines Anzeige-Flag nach Post-Redirect-Get, keine Zustandsänderung.
		$notice = 'geocoded' === $_GET['bw_saved']
			? __( 'Gespeichert. Die Adresse wurde erfolgreich auf der Karte lokalisiert.', 'bodywings' )
			: __( 'Gespeichert.', 'bodywings' );
	}

	get_template_part( 'template-parts/woocommerce/business-profile-form', null, array(
		'profile' => $profile,
		'notice'  => $notice,
	) );
}

/**
 * Post-Redirect-Get: nach erfolgreichem Speichern wird auf dieselbe
 * Endpoint-URL redirected (mit ?bw_saved= als reinem Anzeige-Flag), damit
 * ein Neuladen der Seite kein erneutes Absenden auslöst. Bei GET-Aufrufen
 * (kein Submit) passiert hier nichts.
 */
function bodywings_handle_business_profile_submission( int $user_id ): void {
	if ( ! isset( $_POST['bodywings_business_profile_submit'] ) ) {
		return;
	}

	check_admin_referer( 'bodywings_business_profile', 'bodywings_business_profile_nonce' );

	$result = bodywings_save_business_profile( $user_id, array(
		'name'    => wp_unslash( $_POST['name'] ?? '' ),
		'street'  => wp_unslash( $_POST['street'] ?? '' ),
		'zip'     => wp_unslash( $_POST['zip'] ?? '' ),
		'city'    => wp_unslash( $_POST['city'] ?? '' ),
		'phone'   => wp_unslash( $_POST['phone'] ?? '' ),
		'email'   => wp_unslash( $_POST['email'] ?? '' ),
		'website' => wp_unslash( $_POST['website'] ?? '' ),
	) );

	wp_safe_redirect( add_query_arg(
		'bw_saved',
		$result['geocoded'] ? 'geocoded' : '1',
		wc_get_endpoint_url( BODYWINGS_BUSINESS_ENDPOINT )
	) );
	exit;
}
