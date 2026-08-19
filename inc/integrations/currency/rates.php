<?php
/**
 * Lese-Helfer für gecachte Wechselkurse (§18). Kein direkter Options-
 * Zugriff außerhalb dieser Datei.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string,float>
 */
function bodywings_get_exchange_rates(): array {
	$rates = get_option( BODYWINGS_EXCHANGE_RATES_OPTION, array() );

	return apply_filters( 'bodywings_exchange_rates', is_array( $rates ) ? $rates : array() );
}

function bodywings_get_exchange_rate( string $currency ): ?float {
	$currency = strtoupper( $currency );

	if ( 'EUR' === $currency ) {
		return 1.0;
	}

	$rates = bodywings_get_exchange_rates();

	return isset( $rates[ $currency ] ) ? (float) $rates[ $currency ] : null;
}

function bodywings_get_exchange_rates_updated_at(): int {
	return (int) get_option( BODYWINGS_EXCHANGE_RATES_UPDATED, 0 );
}

function bodywings_convert_price_from_eur( float $amount_eur, string $currency ): ?float {
	$rate = bodywings_get_exchange_rate( $currency );

	return null === $rate ? null : round( $amount_eur * $rate, 2 );
}
