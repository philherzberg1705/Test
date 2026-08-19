<?php
/**
 * Vertrag für Wechselkurs-Provider (§18: API austauschbar halten).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Bodywings_Exchange_Rate_Provider_Interface {

	/**
	 * @return array<string,float>|WP_Error Kurse relativ zu EUR = 1.0, z.B. ['USD' => 1.08, 'GBP' => 0.86].
	 */
	public function fetch_rates( array $target_currencies ): array|WP_Error;
}
