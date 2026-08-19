<?php
/**
 * Vertrag für Newsletter-Provider (§19: austauschbare Provider-Abstraktion,
 * Theme darf nicht vollständig von Brevo abhängen).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Bodywings_Newsletter_Provider_Interface {

	/**
	 * @param string $email       Bereits validierte E-Mail-Adresse.
	 * @param bool   $has_consent DSGVO-Einwilligung (muss true sein, Aufrufer prüft das bereits vorher zusätzlich).
	 * @param string $source      Kontext, z.B. "footer".
	 *
	 * @return true|WP_Error
	 */
	public function subscribe( string $email, bool $has_consent, string $source );
}
