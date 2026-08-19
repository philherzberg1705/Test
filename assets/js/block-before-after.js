/**
 * Vorher/Nachher-Bild (§21/§23): die eigentliche Bedienung ist ein
 * natives <input type="range"> (volle Tastatur-/Touch-/Maus-Unterstützung
 * ohne eigene Pointer-Event-Logik, §25) — dieses Skript spiegelt dessen
 * Wert nur in eine CSS-Custom-Property, die clip-path steuert (reine
 * Compositor-Eigenschaft, kein Layout-Thrash beim Ziehen, §26).
 */
( function () {
	'use strict';

	function initBeforeAfter( el ) {
		var range = el.querySelector( '[data-bw-ba-range]' );
		if ( ! range ) {
			return;
		}

		function update() {
			el.style.setProperty( '--bw-ba-position', range.value + '%' );
		}

		range.addEventListener( 'input', update );
		update();
	}

	function initAll() {
		document.querySelectorAll( '[data-bw-before-after]' ).forEach( initBeforeAfter );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}

	document.addEventListener( 'bodywings:grid-updated', initAll );
} )();
