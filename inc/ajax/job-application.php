<?php
/**
 * AJAX: Bewerbungsformular auf der Job-Detailseite (§15). Multipart-Request
 * (Lebenslauf-Upload) statt des sonst üblichen x-www-form-urlencoded-
 * Formats — einziger AJAX-Handler des Themes mit Datei-Upload, siehe
 * inc/jobs/cv-storage.php für die eigentliche Speicherung/Validierung.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_bodywings_submit_job_application', 'bodywings_ajax_submit_job_application' );
add_action( 'wp_ajax_nopriv_bodywings_submit_job_application', 'bodywings_ajax_submit_job_application' );

function bodywings_ajax_submit_job_application(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	// Honeypot: für Menschen unsichtbares Feld (siehe CSS), Bots füllen es
	// häufig blind aus. Kein Captcha/keine Fremd-Library nötig (§33).
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => __( 'Danke für deine Bewerbung!', 'bodywings' ) ) );
	}

	if ( bodywings_job_application_rate_limited() ) {
		wp_send_json_error( array( 'message' => __( 'Zu viele Bewerbungen von dieser Verbindung. Bitte später erneut versuchen.', 'bodywings' ) ), 429 );
	}

	$job_id = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;

	if ( ! $job_id || 'bw_job' !== get_post_type( $job_id ) || 'publish' !== get_post_status( $job_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Diese Stelle wurde nicht gefunden.', 'bodywings' ) ), 400 );
	}

	if ( ! bodywings_job_is_open( $job_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Die Bewerbungsfrist für diese Stelle ist abgelaufen.', 'bodywings' ) ), 400 );
	}

	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$consent = ! empty( $_POST['consent'] );

	$errors = array();
	if ( '' === $name ) {
		$errors[] = __( 'Bitte einen Namen angeben.', 'bodywings' );
	}
	if ( '' === $email || ! is_email( $email ) ) {
		$errors[] = __( 'Bitte eine gültige E-Mail-Adresse angeben.', 'bodywings' );
	}
	if ( ! $consent ) {
		$errors[] = __( 'Bitte der Verarbeitung der Daten zustimmen.', 'bodywings' );
	}
	if ( empty( $_FILES['cv'] ) ) {
		$errors[] = __( 'Bitte einen Lebenslauf hochladen.', 'bodywings' );
	}

	if ( $errors ) {
		wp_send_json_error( array( 'message' => implode( ' ', $errors ) ), 400 );
	}

	$cv = bodywings_store_job_application_cv( $_FILES['cv'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- $_FILES wird nicht durch wp_unslash beeinflusst, Inhalt wird in bodywings_store_job_application_cv() geprüft.

	if ( is_wp_error( $cv ) ) {
		wp_send_json_error( array( 'message' => $cv->get_error_message() ), 400 );
	}

	$application_id = wp_insert_post( array(
		'post_type'   => 'bw_job_application',
		'post_status' => 'private',
		/* translators: 1: applicant name, 2: job title */
		'post_title'  => sprintf( __( 'Bewerbung: %1$s – %2$s', 'bodywings' ), $name, get_the_title( $job_id ) ),
		'post_content' => $message,
	), true );

	if ( is_wp_error( $application_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Bewerbung konnte nicht gespeichert werden.', 'bodywings' ) ), 500 );
	}

	update_post_meta( $application_id, '_bw_job_id', $job_id );
	update_post_meta( $application_id, '_bw_applicant_name', $name );
	update_post_meta( $application_id, '_bw_applicant_email', $email );
	update_post_meta( $application_id, '_bw_applicant_phone', $phone );
	update_post_meta( $application_id, '_bw_cv_relpath', $cv['relpath'] );
	update_post_meta( $application_id, '_bw_cv_filename', $cv['filename'] );
	update_post_meta( $application_id, '_bw_cv_scan_status', $cv['scan_status'] );
	update_post_meta( $application_id, '_bw_status', 'new' );
	update_post_meta( $application_id, '_bw_consent_at', current_time( 'mysql' ) );

	bodywings_send_job_application_notification( $application_id, $job_id, $name, $email );
	bodywings_send_job_application_confirmation( $job_id, $name, $email );

	wp_send_json_success( array(
		'message' => __( 'Danke für deine Bewerbung! Wir melden uns zeitnah bei dir.', 'bodywings' ),
	) );
}

function bodywings_job_application_rate_limited(): bool {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'bw_job_apply_' . md5( $ip );
	$count = (int) get_transient( $key );

	if ( $count >= apply_filters( 'bodywings_job_application_rate_limit', 5 ) ) {
		return true;
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	return false;
}

function bodywings_send_job_application_notification( int $application_id, int $job_id, string $name, string $email ): void {
	$to = bodywings_get_job_notification_email( $job_id );

	if ( ! $to ) {
		return;
	}

	$subject = sprintf(
		/* translators: %s: job title */
		__( 'Neue Bewerbung: %s', 'bodywings' ),
		get_the_title( $job_id )
	);

	// Bewusst kein Datei-Anhang der Bewerbungsunterlagen per E-Mail
	// (Datensparsamkeit, §32) — Zugriff erfolgt kontrolliert über den
	// geschützten Download-Link im Backend.
	$body = sprintf(
		/* translators: 1: applicant name, 2: applicant email, 3: edit link */
		__( "Neue Bewerbung von %1\$s (%2\$s).\n\nBewerbung ansehen: %3\$s", 'bodywings' ),
		$name,
		$email,
		get_edit_post_link( $application_id, 'raw' )
	);

	wp_mail( $to, $subject, $body );
}

/**
 * Eingangsbestätigung an die bewerbende Person selbst — bisher fehlte
 * diese komplett, nur der Arbeitgeber wurde benachrichtigt. Enthält auch
 * den tatsächlichen Löschzeitraum (bodywings_get_job_application_retention_days(),
 * siehe inc/jobs/retention.php), damit die Zusage aus der Einwilligung im
 * Formular hier noch einmal schriftlich bestätigt wird.
 */
function bodywings_send_job_application_confirmation( int $job_id, string $name, string $email ): void {
	$subject = sprintf(
		/* translators: %s: job title */
		__( 'Deine Bewerbung für „%s“ ist eingegangen', 'bodywings' ),
		get_the_title( $job_id )
	);

	$retention_months = (int) round( bodywings_get_job_application_retention_days() / 30 );

	$body = sprintf(
		/* translators: 1: applicant name, 2: job title, 3: site name, 4: retention period in months */
		__(
			"Hallo %1\$s,\n\n" .
			"vielen Dank für deine Bewerbung auf die Stelle „%2\$s“ bei %3\$s. Wir haben deine Unterlagen erhalten und melden uns, sobald wir sie geprüft haben.\n\n" .
			"Hinweis zum Datenschutz: Deine Angaben werden ausschließlich zur Bearbeitung dieser Bewerbung verwendet und spätestens %4\$d Monate nach Abschluss des Verfahrens automatisch gelöscht.\n\n" .
			"Diese Nachricht ist eine automatische Bestätigung, du musst nicht darauf antworten.",
			'bodywings'
		),
		$name,
		get_the_title( $job_id ),
		get_bloginfo( 'name' ),
		$retention_months
	);

	wp_mail( $email, $subject, $body );
}
