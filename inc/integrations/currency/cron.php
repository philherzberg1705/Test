<?php
/**
 * Stündliches Update der Wechselkurse via WP-Cron (§18). Kurse werden in
 * einer Option gecacht (nicht als Transient, damit sie bei einem
 * abgelaufenen Cache nicht einfach verschwinden — bei API-Ausfall bleibt
 * so garantiert der letzte erfolgreiche Kurs erhalten).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_EXCHANGE_RATES_OPTION    = 'bodywings_exchange_rates';
const BODYWINGS_EXCHANGE_RATES_UPDATED   = 'bodywings_exchange_rates_updated_at';
const BODYWINGS_EXCHANGE_RATES_CRON_HOOK = 'bodywings_update_exchange_rates';

add_action( 'after_switch_theme', 'bodywings_schedule_exchange_rate_cron' );
add_action( BODYWINGS_EXCHANGE_RATES_CRON_HOOK, 'bodywings_update_exchange_rates' );

function bodywings_schedule_exchange_rate_cron(): void {
	if ( ! wp_next_scheduled( BODYWINGS_EXCHANGE_RATES_CRON_HOOK ) ) {
		wp_schedule_event( time(), 'hourly', BODYWINGS_EXCHANGE_RATES_CRON_HOOK );
	}
}

add_action( 'switch_theme', 'bodywings_unschedule_exchange_rate_cron' );

function bodywings_unschedule_exchange_rate_cron(): void {
	$timestamp = wp_next_scheduled( BODYWINGS_EXCHANGE_RATES_CRON_HOOK );

	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, BODYWINGS_EXCHANGE_RATES_CRON_HOOK );
	}
}

/**
 * Holt frische Kurse und cacht sie NUR bei Erfolg — bei einem Fehler
 * bleibt die zuletzt erfolgreich gecachte Option unverändert (§18
 * "letzten erfolgreichen Kurs bei API-Ausfall verwenden").
 */
function bodywings_update_exchange_rates(): void {
	$targets = bodywings_get_supported_target_currencies();

	if ( ! $targets ) {
		return;
	}

	$rates = bodywings_get_exchange_rate_provider()->fetch_rates( $targets );

	if ( is_wp_error( $rates ) || ! $rates ) {
		return;
	}

	update_option( BODYWINGS_EXCHANGE_RATES_OPTION, $rates, false );
	update_option( BODYWINGS_EXCHANGE_RATES_UPDATED, time(), false );
}

/**
 * Falls beim allerersten Aufruf (z.B. direkt nach Theme-Aktivierung, bevor
 * der erste Cron-Lauf stattfand) noch kein Cache existiert, einmalig
 * synchron nachladen statt tagelang ohne Kurse dazustehen.
 */
add_action( 'init', 'bodywings_maybe_prime_exchange_rates' );

function bodywings_maybe_prime_exchange_rates(): void {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	if ( false !== get_option( BODYWINGS_EXCHANGE_RATES_OPTION, false ) ) {
		return;
	}

	bodywings_update_exchange_rates();
}
