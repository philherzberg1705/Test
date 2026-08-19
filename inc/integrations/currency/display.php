<?php
/**
 * Eigener Anzeige-Fallback, falls (noch) kein WooCommerce-Mehrwährungs-
 * plugin aktiv ist (§18/§28). Bewusst NUR kosmetisch: zeigt einen
 * ca.-Umrechnungswert neben dem echten EUR-Preis an. Cart/Checkout/
 * tatsächlicher Zahlungsbetrag bleiben unverändert EUR — eine echte
 * Fremdwährungs-Abwicklung (Steuern, Zahlungsanbieter-Unterstützung,
 * Rundung) gehört ins dedizierte Mehrwährungsplugin, nicht in einen
 * selbstgebauten Fallback.
 *
 * Sobald ein bekanntes Mehrwährungsplugin erkannt wird, zieht sich dieser
 * gesamte Mechanismus zurück, um keine doppelte/konkurrierende
 * Umrechnung zu erzeugen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	return;
}

function bodywings_has_multicurrency_plugin(): bool {
	return class_exists( 'WOOCS' )
		|| defined( 'WCML_VERSION' )
		|| defined( 'WC_AELIA_CURRENCYSWITCHER_VERSION' );
}

function bodywings_get_selected_currency(): string {
	if ( bodywings_has_multicurrency_plugin() ) {
		return get_woocommerce_currency();
	}

	$currency  = isset( $_COOKIE['bw_currency'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_COOKIE['bw_currency'] ) ) ) : 'EUR';
	$available = array_merge( array( 'EUR' ), bodywings_get_supported_target_currencies() );

	return in_array( $currency, $available, true ) ? $currency : 'EUR';
}

add_action( 'template_redirect', 'bodywings_handle_currency_switch_request' );

function bodywings_handle_currency_switch_request(): void {
	if ( bodywings_has_multicurrency_plugin() || empty( $_GET['bw_currency'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$currency  = strtoupper( sanitize_text_field( wp_unslash( $_GET['bw_currency'] ) ) );
	$available = array_merge( array( 'EUR' ), bodywings_get_supported_target_currencies() );

	if ( in_array( $currency, $available, true ) ) {
		setcookie( 'bw_currency', $currency, time() + MONTH_IN_SECONDS, ( defined( 'COOKIEPATH' ) ? COOKIEPATH : '/' ) ?: '/', defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '', is_ssl(), true );
	}

	wp_safe_redirect( remove_query_arg( 'bw_currency' ) );
	exit;
}

add_filter( 'woocommerce_get_price_html', 'bodywings_maybe_append_converted_price', 20, 2 );

function bodywings_maybe_append_converted_price( string $price_html, $product ): string {
	if ( bodywings_has_multicurrency_plugin() || ! $price_html ) {
		return $price_html;
	}

	$currency = bodywings_get_selected_currency();

	if ( 'EUR' === $currency ) {
		return $price_html;
	}

	$amount = (float) $product->get_price();

	if ( $amount <= 0 ) {
		return $price_html;
	}

	$converted = bodywings_convert_price_from_eur( $amount, $currency );

	if ( null === $converted ) {
		return $price_html;
	}

	$formatted = wc_price( $converted, array( 'currency' => $currency ) );

	return $price_html . ' <span class="bw-price-converted">(' . sprintf(
		/* translators: %s: converted, approximate price in the selected currency */
		esc_html__( 'ca. %s', 'bodywings' ),
		wp_kses_post( $formatted )
	) . ')</span>';
}
