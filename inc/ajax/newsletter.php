<?php
/**
 * AJAX: Newsletter-Anmeldung im Footer (§15/§19/§20).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_bodywings_newsletter_subscribe', 'bodywings_ajax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_bodywings_newsletter_subscribe', 'bodywings_ajax_newsletter_subscribe' );

function bodywings_ajax_newsletter_subscribe(): void {
	if ( ! bodywings_ajax_verify_nonce() ) {
		bodywings_ajax_send_invalid_nonce();
	}

	$email       = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$has_consent = ! empty( $_POST['consent'] );

	if ( ! $email || ! is_email( $email ) ) {
		wp_send_json_error( array(
			'field'   => 'email',
			'message' => __( 'Bitte eine gültige E-Mail-Adresse angeben.', 'bodywings' ),
		), 400 );
	}

	if ( ! $has_consent ) {
		wp_send_json_error( array(
			'field'   => 'consent',
			'message' => __( 'Bitte der Einwilligung zur Datenverarbeitung zustimmen.', 'bodywings' ),
		), 400 );
	}

	$result = bodywings_get_newsletter_provider()->subscribe( $email, true, 'footer' );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 500 );
	}

	wp_send_json_success( array(
		'message' => __( 'Danke! Bitte bestätige deine Anmeldung, falls eine E-Mail von uns kommt.', 'bodywings' ),
	) );
}
