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

		var tabs = Array.prototype.slice.call( root.querySelectorAll( '[data-bw-auth-tab]' ) );
		var panels = root.querySelectorAll( '[data-bw-auth-panel]' );

		function activate( name ) {
			tabs.forEach( function ( tab ) {
				var isActive = tab.getAttribute( 'data-bw-auth-tab' ) === name;
				tab.classList.toggle( 'is-active', isActive );
				tab.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
				// Roving Tabindex (WAI-ARIA APG Tabs-Pattern, §25): nur der
				// aktive Tab ist per Tab-Taste erreichbar, Pfeiltasten wechseln.
				tab.setAttribute( 'tabindex', isActive ? '0' : '-1' );
			} );

			panels.forEach( function ( panel ) {
				var isActive = panel.getAttribute( 'data-bw-auth-panel' ) === name;
				panel.classList.toggle( 'is-active', isActive );
				panel.hidden = ! isActive;
			} );
		}

		tabs.forEach( function ( tab, index ) {
			tab.addEventListener( 'click', function () {
				var name = tab.getAttribute( 'data-bw-auth-tab' );
				if ( name ) {
					activate( name );
				}
			} );

			tab.addEventListener( 'keydown', function ( event ) {
				var targetIndex = null;

				if ( 'ArrowRight' === event.key || 'ArrowDown' === event.key ) {
					targetIndex = ( index + 1 ) % tabs.length;
				} else if ( 'ArrowLeft' === event.key || 'ArrowUp' === event.key ) {
					targetIndex = ( index - 1 + tabs.length ) % tabs.length;
				} else if ( 'Home' === event.key ) {
					targetIndex = 0;
				} else if ( 'End' === event.key ) {
					targetIndex = tabs.length - 1;
				}

				if ( null === targetIndex ) {
					return;
				}

				event.preventDefault();
				var targetTab = tabs[ targetIndex ];
				var name = targetTab.getAttribute( 'data-bw-auth-tab' );
				if ( name ) {
					activate( name );
				}
				targetTab.focus();
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
