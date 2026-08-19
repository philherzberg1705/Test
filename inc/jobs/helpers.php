<?php
/**
 * Lese-Helfer für Stellendaten — von Archiv-Karte, Detailseite UND den
 * structured data (§27) gemeinsam genutzt (§33), damit Anzeige und Schema
 * nie auseinanderlaufen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_get_job_notification_email( int $job_id ): string {
	$email = get_post_meta( $job_id, '_bw_job_contact_email', true );

	return $email ? $email : get_option( 'admin_email' );
}

/**
 * @return array{min:int,max:int,unit:string}|null
 */
function bodywings_get_job_salary( int $job_id ): ?array {
	$min = get_post_meta( $job_id, '_bw_job_salary_min', true );
	$max = get_post_meta( $job_id, '_bw_job_salary_max', true );

	if ( '' === $min && '' === $max ) {
		return null;
	}

	$unit = get_post_meta( $job_id, '_bw_job_salary_unit', true ) ?: 'YEAR';

	return array(
		'min'  => '' !== $min ? (int) $min : (int) $max,
		'max'  => '' !== $max ? (int) $max : (int) $min,
		'unit' => $unit,
	);
}

function bodywings_get_job_salary_unit_label( string $unit ): string {
	$labels = array(
		'YEAR'  => __( 'Jahr', 'bodywings' ),
		'MONTH' => __( 'Monat', 'bodywings' ),
		'HOUR'  => __( 'Stunde', 'bodywings' ),
	);

	return $labels[ $unit ] ?? '';
}

/**
 * Menschenlesbarer Gehaltstext, z.B. "40.000 € – 55.000 € / Jahr" oder
 * "ab 3.500 € / Monat" wenn nur ein Wert gepflegt ist.
 */
function bodywings_get_job_salary_text( int $job_id ): string {
	$salary = bodywings_get_job_salary( $job_id );

	if ( ! $salary ) {
		return '';
	}

	$unit_label = bodywings_get_job_salary_unit_label( $salary['unit'] );

	if ( $salary['min'] === $salary['max'] ) {
		return sprintf(
			/* translators: 1: amount, 2: unit (e.g. Jahr) */
			__( '%1$s € / %2$s', 'bodywings' ),
			number_format_i18n( $salary['min'] ),
			$unit_label
		);
	}

	return sprintf(
		/* translators: 1: minimum amount, 2: maximum amount, 3: unit (e.g. Jahr) */
		__( '%1$s € – %2$s € / %3$s', 'bodywings' ),
		number_format_i18n( $salary['min'] ),
		number_format_i18n( $salary['max'] ),
		$unit_label
	);
}

function bodywings_get_job_start_text( int $job_id ): string {
	if ( get_post_meta( $job_id, '_bw_job_start_asap', true ) ) {
		return __( 'Ab sofort', 'bodywings' );
	}

	$date = get_post_meta( $job_id, '_bw_job_start_date', true );

	return $date ? date_i18n( get_option( 'date_format' ), strtotime( $date ) ) : '';
}

function bodywings_get_job_deadline_text( int $job_id ): string {
	$date = get_post_meta( $job_id, '_bw_job_application_deadline', true );

	return $date ? date_i18n( get_option( 'date_format' ), strtotime( $date ) ) : '';
}

function bodywings_job_is_remote( int $job_id ): bool {
	return (bool) get_post_meta( $job_id, '_bw_job_is_remote', true );
}

/**
 * @return string[]
 */
function bodywings_get_job_list_field( int $job_id, string $meta_key ): array {
	$values = get_post_meta( $job_id, $meta_key, true );

	return is_array( $values ) ? array_filter( $values ) : array();
}

/**
 * @return WP_Term[]
 */
function bodywings_get_job_terms( int $job_id, string $taxonomy ): array {
	$terms = get_the_terms( $job_id, $taxonomy );

	return is_array( $terms ) ? $terms : array();
}

function bodywings_get_job_type_names( int $job_id ): string {
	return implode( ', ', wp_list_pluck( bodywings_get_job_terms( $job_id, 'bw_job_type' ), 'name' ) );
}

function bodywings_get_job_location_names( int $job_id ): string {
	$names = wp_list_pluck( bodywings_get_job_terms( $job_id, 'bw_job_location' ), 'name' );

	if ( ! $names && bodywings_job_is_remote( $job_id ) ) {
		return __( 'Remote', 'bodywings' );
	}

	return implode( ', ', $names );
}

/**
 * Aktive Ausschreibung = keine Bewerbungsfrist gesetzt oder Frist noch
 * nicht abgelaufen. Steuert sowohl die Anzeige des Bewerbungsformulars als
 * auch die JobPosting-Structured-Data-Warnung bei abgelaufenen Fristen.
 */
function bodywings_job_is_open( int $job_id ): bool {
	$deadline = get_post_meta( $job_id, '_bw_job_application_deadline', true );

	if ( ! $deadline ) {
		return true;
	}

	return strtotime( $deadline ) >= strtotime( 'today' );
}
