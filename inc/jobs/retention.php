<?php
/**
 * DSGVO-Löschfrist für Bewerbungen (§32). Das Bewerbungsformular
 * (template-parts/jobs/application-form.php) sagt zu, dass die Daten nach
 * Abschluss des Verfahrens gelöscht werden — dieser Cron setzt das
 * tatsächlich um, statt es nur zu behaupten.
 *
 * Die Frist läuft ab dem Zeitpunkt, an dem eine Bewerbung auf "Abgelehnt"
 * oder "Eingestellt" gesetzt wurde (_bw_status_concluded_at, siehe
 * inc/jobs/application-post-type.php), NICHT ab dem Eingangsdatum — eine
 * noch unbearbeitete Bewerbung ("Neu"/"In Prüfung") wird nie automatisch
 * gelöscht, das würde unbearbeitete Bewerbungen verschwinden lassen statt
 * ein Prozessproblem sichtbar zu machen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_JOB_APPLICATION_CLEANUP_HOOK = 'bodywings_job_applications_cleanup';

/**
 * Zentrale Quelle für die Löschfrist — Cron-Job UND die Einwilligungs-
 * Texte (Bewerbungsformular, Bestätigungsmail) lesen denselben Wert, damit
 * das Formular niemals eine andere Frist verspricht als der Cron
 * tatsächlich umsetzt (§33). Standard: 180 Tage/6 Monate nach Abschluss
 * des Verfahrens, ein gängiger Richtwert für Bewerberdaten. Per Filter
 * anpassbar, 0/negativ deaktiviert die Auto-Löschung vollständig.
 */
function bodywings_get_job_application_retention_days(): int {
	return (int) apply_filters( 'bodywings_job_application_retention_days', 180 );
}

add_action( 'init', 'bodywings_schedule_job_application_cleanup' );

function bodywings_schedule_job_application_cleanup(): void {
	if ( ! wp_next_scheduled( BODYWINGS_JOB_APPLICATION_CLEANUP_HOOK ) ) {
		wp_schedule_event( time(), 'daily', BODYWINGS_JOB_APPLICATION_CLEANUP_HOOK );
	}
}

// Theme-Wechsel: geplanten Cron-Job nicht verwaist weiterlaufen lassen.
add_action( 'switch_theme', function (): void {
	wp_clear_scheduled_hook( BODYWINGS_JOB_APPLICATION_CLEANUP_HOOK );
} );

add_action( BODYWINGS_JOB_APPLICATION_CLEANUP_HOOK, 'bodywings_run_job_application_cleanup' );

/**
 * Löscht abgeschlossene Bewerbungen (rejected/hired), deren Frist
 * abgelaufen ist — Post UND die zugehörige Lebenslauf-Datei (siehe
 * inc/jobs/cv-storage.php). Verarbeitet je Lauf maximal 200 Bewerbungen,
 * ausreichend für den täglichen Cron bei normalem Bewerbungsaufkommen;
 * ein größerer Rückstand wird einfach am nächsten Tag weiter abgearbeitet.
 */
function bodywings_run_job_application_cleanup(): void {
	$retention_days = bodywings_get_job_application_retention_days();

	if ( $retention_days <= 0 ) {
		return; // 0/negativ = Auto-Löschung explizit deaktiviert.
	}

	$cutoff = current_datetime()->modify( '-' . $retention_days . ' days' )->format( 'Y-m-d H:i:s' );

	$query = new WP_Query( array(
		'post_type'      => 'bw_job_application',
		'post_status'    => 'any',
		'posts_per_page' => 200,
		'fields'         => 'ids',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'     => '_bw_status_concluded_at',
				'value'   => $cutoff,
				'compare' => '<=',
				'type'    => 'DATETIME',
			),
		),
	) );

	foreach ( $query->posts as $bw_application_id ) {
		bodywings_delete_job_application( (int) $bw_application_id );
	}
}

/**
 * Löscht eine einzelne Bewerbung vollständig: Lebenslauf-Datei + -Ordner
 * und den Post selbst (kein Papierkorb — personenbezogene Daten sollen
 * wirklich weg sein, nicht nur versteckt).
 */
function bodywings_delete_job_application( int $application_id ): void {
	if ( 'bw_job_application' !== get_post_type( $application_id ) ) {
		return;
	}

	bodywings_delete_job_application_cv_files( $application_id );

	wp_delete_post( $application_id, true );
}
