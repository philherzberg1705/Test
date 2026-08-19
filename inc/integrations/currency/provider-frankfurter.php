<?php
/**
 * Default-Provider: frankfurter.dev (Europäische Zentralbank, kostenlos,
 * kein API-Key nötig). Austauschbar über den Filter in factory.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bodywings_Exchange_Rate_Provider_Frankfurter implements Bodywings_Exchange_Rate_Provider_Interface {

	public function fetch_rates( array $target_currencies ): array|WP_Error {
		if ( ! $target_currencies ) {
			return array();
		}

		$url = add_query_arg(
			array(
				'base' => 'EUR',
				'symbols' => implode( ',', array_map( 'strtoupper', $target_currencies ) ),
			),
			'https://api.frankfurter.dev/v1/latest'
		);

		$response = wp_remote_get( $url, array( 'timeout' => 8 ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'bodywings_exchange_rate_http_error', __( 'Wechselkurs-API antwortete mit einem Fehler.', 'bodywings' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['rates'] ) || ! is_array( $body['rates'] ) ) {
			return new WP_Error( 'bodywings_exchange_rate_invalid_response', __( 'Wechselkurs-API lieferte ein unerwartetes Format.', 'bodywings' ) );
		}

		$rates = array();

		foreach ( $body['rates'] as $currency => $rate ) {
			$rates[ strtoupper( $currency ) ] = (float) $rate;
		}

		return $rates;
	}
}
