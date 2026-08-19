/**
 * Generische Offcanvas-Steuerung: Menü, Suche, Sprache/Währung heute,
 * Filter/Side-Cart in späteren Tasks. Eine Implementierung statt
 * Copy-Paste pro Feature (§33).
 *
 * Markup-Vertrag:
 *   [data-bw-offcanvas-trigger="name"]  öffnet [data-bw-offcanvas="name"]
 *   [data-bw-offcanvas-close]           innerhalb eines Panels schließt es
 *   Panel startet mit inert + aria-hidden="true" im Markup.
 */
( function () {
	'use strict';

	var FOCUSABLE_SELECTOR =
		'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

	var activePanel = null;
	var activeTrigger = null;

	function getTriggerFor( panel ) {
		var name = panel.getAttribute( 'data-bw-offcanvas' );
		return document.querySelector( '[data-bw-offcanvas-trigger="' + name + '"]' );
	}

	function trapFocus( event ) {
		if ( event.key !== 'Tab' || ! activePanel ) {
			return;
		}

		var focusables = Array.prototype.slice.call( activePanel.querySelectorAll( FOCUSABLE_SELECTOR ) );
		if ( ! focusables.length ) {
			return;
		}

		var first = focusables[ 0 ];
		var last = focusables[ focusables.length - 1 ];

		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	}

	function onKeydown( event ) {
		if ( event.key === 'Escape' && activePanel ) {
			closeOffcanvas( activePanel );
		} else {
			trapFocus( event );
		}
	}

	function openOffcanvas( panel, trigger ) {
		if ( activePanel && activePanel !== panel ) {
			closeOffcanvas( activePanel );
		}

		activePanel = panel;
		activeTrigger = trigger || getTriggerFor( panel );

		panel.removeAttribute( 'inert' );
		panel.setAttribute( 'aria-hidden', 'false' );
		panel.classList.add( 'is-open' );
		document.documentElement.classList.add( 'bw-offcanvas-open' );

		if ( activeTrigger ) {
			activeTrigger.setAttribute( 'aria-expanded', 'true' );
		}

		var focusables = panel.querySelectorAll( FOCUSABLE_SELECTOR );
		if ( focusables.length ) {
			focusables[ 0 ].focus();
		}

		document.addEventListener( 'keydown', onKeydown );
	}

	function closeOffcanvas( panel ) {
		panel.classList.remove( 'is-open' );
		panel.setAttribute( 'aria-hidden', 'true' );
		panel.setAttribute( 'inert', '' );
		document.documentElement.classList.remove( 'bw-offcanvas-open' );
		document.removeEventListener( 'keydown', onKeydown );

		var trigger = getTriggerFor( panel );
		if ( trigger ) {
			trigger.setAttribute( 'aria-expanded', 'false' );
			trigger.focus();
		}

		if ( activePanel === panel ) {
			activePanel = null;
			activeTrigger = null;
		}
	}

	function initOffcanvas() {
		document.querySelectorAll( '[data-bw-offcanvas-trigger]' ).forEach( function ( trigger ) {
			trigger.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				var name = trigger.getAttribute( 'data-bw-offcanvas-trigger' );
				var panel = document.querySelector( '[data-bw-offcanvas="' + name + '"]' );
				if ( panel ) {
					openOffcanvas( panel, trigger );
				}
			} );
		} );

		document.querySelectorAll( '[data-bw-offcanvas]' ).forEach( function ( panel ) {
			panel.querySelectorAll( '[data-bw-offcanvas-close]' ).forEach( function ( closeEl ) {
				closeEl.addEventListener( 'click', function () {
					closeOffcanvas( panel );
				} );
			} );
		} );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'offcanvas', initOffcanvas );
	}
} )();
