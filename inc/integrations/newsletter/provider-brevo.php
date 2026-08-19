<?php
/**
 * Brevo-Provider (§19 "spätere Integration mit Brevo"). Wird von der
 * Factory automatisch aktiv, sobald ein API-Key hinterlegt ist — bis dahin
 * bleibt der lokale Provider aktiv, das Theme ist also nie hart von Brevo
 * abhängig.
 *
 * API-Key/List-ID werden bewusst nicht hart verdrahtet, sondern über
 * Filter bezogen, damit sie z.B. aus wp-config.php-Konstanten oder einer
 * zukünftigen Admin-Einstellung (Task: BodywingsTheme-Backend) kommen
 * können, ohne diese Datei zu ändern.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bodywings_Newsletter_Provider_Brevo implements Bodywings_Newsletter_Provider_Interface {

	public function subscribe( string $email, bool $has_consent, string $source ) {
		if ( ! $has_consent ) {
			return new WP_Error( 'bodywings_newsletter_consent_required', __( 'Bitte der Einwilligung zustimmen.', 'bodywings' ) );
		}

		$api_key = bodywings_newsletter_brevo_api_key();

		if ( ! $api_key ) {
			return new WP_Error( 'bodywings_newsletter_not_configured', __( 'Newsletter-Anbindung ist nicht konfiguriert.', 'bodywings' ) );
		}

		$body = array(
			'email'          => $email,
			'updateEnabled'  => true,
			'attributes'     => array(
				'BW_CONSENT_SOURCE' => $source,
			),
		);

		$list_id = (int) apply_filters( 'bodywings_brevo_list_id', 0 );

		if ( $list_id > 0 ) {
			$body['listIds'] = array( $list_id );
		}

		$response = wp_remote_post( 'https://api.brevo.com/v3/contacts', array(
			'timeout' => 10,
			'headers' => array(
				'api-key'      => $api_key,
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			),
			'body'    => wp_json_encode( $body ),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status = (int) wp_remote_retrieve_response_code( $response );

		// 201 = neu angelegt, 204 = bereits vorhanden & aktualisiert.
		if ( in_array( $status, array( 201, 204 ), true ) ) {
			return true;
		}

		return new WP_Error(
			'bodywings_newsletter_brevo_error',
			__( 'Newsletter-Anmeldung bei Brevo fehlgeschlagen.', 'bodywings' ),
			array( 'status' => $status, 'body' => wp_remote_retrieve_body( $response ) )
		);
	}
}

function bodywings_newsletter_brevo_api_key(): string {
	return (string) apply_filters( 'bodywings_brevo_api_key', defined( 'BODYWINGS_BREVO_API_KEY' ) ? BODYWINGS_BREVO_API_KEY : '' );
}
