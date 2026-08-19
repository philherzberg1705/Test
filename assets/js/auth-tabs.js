/**
 * Auth-Bereich Tab-Wechsel (§17): Login ↔ Registrieren ohne Reload,
 * animiert über CSS ([hidden] + .is-active). Verarbeitung der Formulare
 * bleibt vollständig WordPress/WooCommerce.
 */
( function () {
	'use strict';

	function initAuthTabs() {
		var root = document.querySelector( '[data-bw-auth]' );
		if ( ! root ) {
			return;
		}

		var tabs = root.querySelectorAll( '[data-bw-auth-tab]' );
		var panels = root.querySelectorAll( '[data-bw-auth-panel]' );

		function activate( name ) {
			tabs.forEach( function ( tab ) {
				var isActive = tab.getAttribute( 'data-bw-auth-tab' ) === name;
				tab.classList.toggle( 'is-active', isActive );
				tab.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
			} );

			panels.forEach( function ( panel ) {
				var isActive = panel.getAttribute( 'data-bw-auth-panel' ) === name;
				panel.classList.toggle( 'is-active', isActive );
				panel.hidden = ! isActive;
			} );
		}

		tabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				var name = tab.getAttribute( 'data-bw-auth-tab' );
				if ( name ) {
					activate( name );
				}
			} );
		} );

		if ( 'register' === window.location.hash.replace( '#', '' ) ) {
			activate( 'register' );
		}
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'auth-tabs', initAuthTabs );
	}
} )();
