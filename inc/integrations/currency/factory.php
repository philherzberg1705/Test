<?php
/**
 * Aktiver Wechselkurs-Provider — austauschbar per Filter (§18), damit ein
 * Wechsel der API keine Codeänderung an anderer Stelle erfordert.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_get_exchange_rate_provider(): Bodywings_Exchange_Rate_Provider_Interface {
	$provider = apply_filters( 'bodywings_exchange_rate_provider', new Bodywings_Exchange_Rate_Provider_Frankfurter() );

	return $provider instanceof Bodywings_Exchange_Rate_Provider_Interface
		? $provider
		: new Bodywings_Exchange_Rate_Provider_Frankfurter();
}

/**
 * Ziel-Währungen (ohne EUR selbst) — Standard bewusst schlank, per Filter
 * erweiterbar statt als weitere Backend-Option (§29 "gute Defaults").
 *
 * @return string[]
 */
function bodywings_get_supported_target_currencies(): array {
	return apply_filters( 'bodywings_supported_currencies', array( 'USD', 'GBP', 'CHF' ) );
}
