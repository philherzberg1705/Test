<?php
/**
 * Adress-Geocoding über die öffentliche Nominatim-API (OpenStreetMap) —
 * kostenlos, kein API-Key, passend zur Leaflet/OSM-Kartenlösung (§18-Analogie:
 * austauschbare, klar abgegrenzte API-Schicht statt fest verdrahteter Aufrufe
 * quer durchs Theme). Wird ausschließlich beim Speichern des Geschäftsprofils
 * aufgerufen (inc/business-search/profile.php), nicht bei jedem Seitenaufruf.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array{lat:float,lng:float}|null null bei Fehler/keinem Treffer —
 *   der Aufrufer behält dann die zuletzt bekannten Koordinaten (kein
 *   Datenverlust durch einen einzelnen fehlgeschlagenen API-Aufruf).
 */
function bodywings_geocode_address( string $address ): ?array {
	if ( '' === trim( $address ) ) {
		return null;
	}

	$url = add_query_arg(
		array(
			'format' => 'json',
			'limit'  => 1,
			'q'      => $address,
		),
		'https://nominatim.openstreetmap.org/search'
	);

	// Nominatims Nutzungsbedingungen verlangen einen aussagekräftigen
	// User-Agent (keine generischen HTTP-Client-Strings).
	$response = wp_remote_get( $url, array(
		'timeout' => 8,
		'headers' => array(
			'User-Agent' => 'BODYWINGS-Theme/' . BODYWINGS_VERSION . ' (' . home_url( '/' ) . ')',
		),
	) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}

	$results = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $results ) || empty( $results[0]['lat'] ) || empty( $results[0]['lon'] ) ) {
		return null;
	}

	return array(
		'lat' => (float) $results[0]['lat'],
		'lng' => (float) $results[0]['lon'],
	);
}
