<?php
/**
 * Default-Provider ohne externe Abhängigkeit: speichert Abonnent:innen in
 * einer eigenen Tabelle. Aktiv, solange kein Brevo-API-Key hinterlegt ist
 * (siehe factory.php) — Theme funktioniert dadurch komplett ohne Brevo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bodywings_Newsletter_Provider_Local implements Bodywings_Newsletter_Provider_Interface {

	public function subscribe( string $email, bool $has_consent, string $source ) {
		global $wpdb;

		if ( ! $has_consent ) {
			return new WP_Error( 'bodywings_newsletter_consent_required', __( 'Bitte der Einwilligung zustimmen.', 'bodywings' ) );
		}

		$table = bodywings_newsletter_table_name();
		$now   = current_time( 'mysql' );

		$existing = $wpdb->get_var(
			$wpdb->prepare( "SELECT id FROM {$table} WHERE email = %s", $email ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);

		if ( $existing ) {
			return true;
		}

		$inserted = $wpdb->insert(
			$table,
			array(
				'email'      => $email,
				'source'     => $source,
				'consent_at' => $now,
				'created_at' => $now,
			),
			array( '%s', '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			return new WP_Error( 'bodywings_newsletter_db_error', __( 'Anmeldung konnte nicht gespeichert werden.', 'bodywings' ) );
		}

		return true;
	}
}
