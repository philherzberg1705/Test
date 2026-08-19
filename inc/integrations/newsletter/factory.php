<?php
/**
 * Wählt den aktiven Newsletter-Provider. Brevo nur, wenn ein API-Key
 * hinterlegt ist — sonst lokaler Fallback (§19).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_get_newsletter_provider(): Bodywings_Newsletter_Provider_Interface {
	if ( bodywings_newsletter_brevo_api_key() ) {
		return new Bodywings_Newsletter_Provider_Brevo();
	}

	return new Bodywings_Newsletter_Provider_Local();
}
