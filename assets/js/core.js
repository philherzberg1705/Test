/**
 * BODYWINGS core.js — lädt auf jeder Seite, bleibt bewusst minimal (§26).
 * Enthält nur: reduced-motion-Erkennung + einen leichten Modul-Registrator,
 * damit spätere Features (Wishlist, Cart, Filter, ...) sich hier andocken,
 * ohne dass diese Datei wachsen muss.
 */
( function () {
	'use strict';

	var reducedMotionQuery = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	function applyReducedMotion( matches ) {
		document.documentElement.classList.toggle( 'bw-reduced-motion', matches );
		if ( window.bodywingsData ) {
			window.bodywingsData.reducedMotion = matches;
		}
	}

	applyReducedMotion( reducedMotionQuery.matches );
	reducedMotionQuery.addEventListener( 'change', function ( event ) {
		applyReducedMotion( event.matches );
	} );

	/**
	 * Registry für Feature-Module. Jedes Modul ruft
	 * `window.bodywings.register('name', initFn)` auf; initFn läuft sofort,
	 * sobald der DOM bereit ist.
	 */
	var modules = [];

	window.bodywings = window.bodywings || {
		register: function ( name, initFn ) {
			modules.push( { name: name, init: initFn } );
		},
	};

	document.addEventListener( 'DOMContentLoaded', function () {
		modules.forEach( function ( mod ) {
			try {
				mod.init();
			} catch ( error ) {
				// Ein fehlerhaftes Modul darf nicht die ganze Seite blockieren.
				console.error( '[bodywings] Modul "' + mod.name + '" fehlgeschlagen:', error );
			}
		} );
	} );
} )();
