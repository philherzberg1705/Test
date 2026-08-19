<?php
/**
 * Minimalistisches, selbst gehostetes Icon-Set (kein Icon-Font, keine
 * externe Library — §26). Stroke-basiert, erbt Farbe über currentColor,
 * damit Icons automatisch zur §2.2-Kontrastfarbe des umgebenden
 * Content-Elements passen.
 *
 * Alle Strings sind statisches, von uns kontrolliertes Markup ohne
 * dynamische Interpolation — bewusst ungefiltert zurückgegeben.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_icon( string $name ): string {
	$icons = array(
		'menu'   => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'  => '<path d="M6 6l12 12M18 6L6 18"/>',
		'search' => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
		'globe'  => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.7-3.8-9s1.3-6.4 3.8-9Z"/>',
		'heart'  => '<path d="M12 20.5s-7.5-4.6-10-9.3C.4 8 1.8 4.5 5.2 3.6c2-.5 4 .3 5.2 2 .3.4.9.4 1.2 0 1.2-1.7 3.2-2.5 5.2-2 3.4.9 4.8 4.4 3.2 7.6-2.5 4.7-10 9.3-10 9.3Z"/>',
		'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.6-4 4.8-6 8-6s6.4 2 8 6"/>',
		'bag'    => '<path d="M6 8h12l-1 12.5a1 1 0 0 1-1 .9H8a1 1 0 0 1-1-.9L6 8Z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/>',
		'truck'  => '<path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17.5" cy="18" r="1.6"/>',
		'chevron-left'  => '<path d="M15 5l-7 7 7 7"/>',
		'chevron-right' => '<path d="M9 5l7 7-7 7"/>',
		'chevron-down'  => '<path d="M5 9l7 7 7-7"/>',
		'star'   => '<path d="M12 3.5l2.6 5.4 5.9.8-4.3 4.2 1 5.9-5.2-2.8-5.2 2.8 1-5.9-4.3-4.2 5.9-.8Z"/>',
		'check'  => '<path d="M4 12.5l5 5L20 6"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="bw-icon bw-icon--%1$s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		esc_attr( $name ),
		$icons[ $name ]
	);
}
