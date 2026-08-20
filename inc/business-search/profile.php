<?php
/**
 * Geschäftsprofil-Daten (User-Meta) für die Geschäftssuche. Bewusst als
 * User-Meta statt eigenem Post-Type: es gibt genau ein Profil pro Nutzer:in
 * mit der konfigurierten Rolle, kein eigenständiger Lebenszyklus nötig (§33).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array{name:string,street:string,zip:string,city:string,phone:string,email:string,website:string,lat:?float,lng:?float}
 */
function bodywings_get_business_profile( int $user_id ): array {
	$lat = get_user_meta( $user_id, '_bw_business_lat', true );
	$lng = get_user_meta( $user_id, '_bw_business_lng', true );

	return array(
		'name'    => (string) get_user_meta( $user_id, '_bw_business_name', true ),
		'street'  => (string) get_user_meta( $user_id, '_bw_business_street', true ),
		'zip'     => (string) get_user_meta( $user_id, '_bw_business_zip', true ),
		'city'    => (string) get_user_meta( $user_id, '_bw_business_city', true ),
		'phone'   => (string) get_user_meta( $user_id, '_bw_business_phone', true ),
		'email'   => (string) get_user_meta( $user_id, '_bw_business_email', true ),
		'website' => (string) get_user_meta( $user_id, '_bw_business_website', true ),
		'lat'     => '' !== $lat ? (float) $lat : null,
		'lng'     => '' !== $lng ? (float) $lng : null,
	);
}

function bodywings_business_profile_address( array $profile ): string {
	return trim( sprintf( '%s, %s %s', $profile['street'], $profile['zip'], $profile['city'] ), ', ' );
}

/**
 * Sanitisiert und speichert das Geschäftsprofil. Adresse wird nur dann neu
 * geocodiert, wenn sich Straße/PLZ/Ort tatsächlich geändert haben (§26 —
 * keine unnötigen API-Aufrufe bei jedem Speichern).
 *
 * @param array $raw Rohdaten, z.B. aus $_POST (wird hier vollständig sanitisiert).
 * @return array{geocoded:bool} geocoded=false heißt: Adresse unverändert
 *   ODER Geocoding fehlgeschlagen (bisherige Koordinaten bleiben erhalten).
 */
function bodywings_save_business_profile( int $user_id, array $raw ): array {
	$before = bodywings_get_business_profile( $user_id );

	$clean = array(
		'name'    => sanitize_text_field( $raw['name'] ?? '' ),
		'street'  => sanitize_text_field( $raw['street'] ?? '' ),
		'zip'     => sanitize_text_field( $raw['zip'] ?? '' ),
		'city'    => sanitize_text_field( $raw['city'] ?? '' ),
		'phone'   => sanitize_text_field( $raw['phone'] ?? '' ),
		'email'   => sanitize_email( $raw['email'] ?? '' ),
		'website' => esc_url_raw( $raw['website'] ?? '' ),
	);

	foreach ( $clean as $key => $value ) {
		update_user_meta( $user_id, '_bw_business_' . $key, $value );
	}

	$address_before = bodywings_business_profile_address( $before );
	$address_after  = bodywings_business_profile_address( $clean );
	$geocoded       = false;

	if ( $address_after && $address_after !== $address_before ) {
		$coords = bodywings_geocode_address( $address_after );

		if ( $coords ) {
			update_user_meta( $user_id, '_bw_business_lat', $coords['lat'] );
			update_user_meta( $user_id, '_bw_business_lng', $coords['lng'] );
			$geocoded = true;
		}
	} elseif ( ! $address_after ) {
		delete_user_meta( $user_id, '_bw_business_lat' );
		delete_user_meta( $user_id, '_bw_business_lng' );
	}

	return array( 'geocoded' => $geocoded );
}

/**
 * Alle Geschäftsprofile mit vollständig geocodierter Adresse, für den
 * Geschäftssuche-Block (blocks/business-search). Ohne konfigurierte Rolle
 * (siehe inc/business-search/settings.php) immer leer.
 *
 * @return array<int,array{id:int,name:string,address:string,phone:string,email:string,website:string,lat:float,lng:float}>
 */
function bodywings_get_business_search_profiles(): array {
	$role = bodywings_get_business_search_role();

	if ( ! $role ) {
		return array();
	}

	$users = get_users( array(
		'role'       => $role,
		'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'AND',
			array( 'key' => '_bw_business_lat', 'compare' => 'EXISTS' ),
			array( 'key' => '_bw_business_lng', 'compare' => 'EXISTS' ),
			array( 'key' => '_bw_business_name', 'value' => '', 'compare' => '!=' ),
		),
	) );

	$profiles = array();

	foreach ( $users as $user ) {
		$profile = bodywings_get_business_profile( $user->ID );

		if ( null === $profile['lat'] || null === $profile['lng'] ) {
			continue;
		}

		$profiles[] = array(
			'id'      => $user->ID,
			'name'    => $profile['name'],
			'address' => bodywings_business_profile_address( $profile ),
			'phone'   => $profile['phone'],
			'email'   => $profile['email'],
			'website' => $profile['website'],
			'lat'     => $profile['lat'],
			'lng'     => $profile['lng'],
		);
	}

	return $profiles;
}
