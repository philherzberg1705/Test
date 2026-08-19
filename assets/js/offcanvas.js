/**
 * Generische Offcanvas-Steuerung: Menü, Suche, Sprache/Währung, Filter,
 * Side-Cart. Eine Implementierung statt Copy-Paste pro Feature (§33).
 *
 * Markup-Vertrag:
 *   [data-bw-offcanvas-trigger="name"]  öffnet [data-bw-offcanvas="name"]
 *   [data-bw-offcanvas-close]           innerhalb eines Panels schließt es
 *   Panel startet mit inert + aria-hidden="true" im Markup.
 *
 * Optionaler "responsive-static"-Modus (§13: Desktop klarer Filterbereich,
 * Mobile Offcanvas — ein Panel für beides statt zweier Implementierungen):
 *   [data-bw-offcanvas-responsive="960"] macht das Panel ab 960px als
 *   normale, immer sichtbare/interaktive Fläche verfügbar (kein inert,
 *   kein Overlay); darunter verhält es sich wie jedes andere Offcanvas.
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

	function isResponsiveStatic( panel ) {
		var minWidth = panel.getAttribute( 'data-bw-offcanvas-responsive' );
		return !! minWidth && window.matchMedia( '(min-width: ' + minWidth + 'px)' ).matches;
	}

	function syncResponsivePanel( panel ) {
		if ( isResponsiveStatic( panel ) ) {
			panel.removeAttribute( 'inert' );
			panel.setAttribute( 'aria-hidden', 'false' );
			panel.classList.add( 'is-static' );
			panel.classList.remove( 'is-open' );
		} else {
			panel.classList.remove( 'is-static' );
			if ( panel !== activePanel ) {
				panel.setAttribute( 'inert', '' );
				panel.setAttribute( 'aria-hidden', 'true' );
			}
		}
	}

	function initOffcanvas() {
		document.querySelectorAll( '[data-bw-offcanvas-trigger]' ).forEach( function ( trigger ) {
			trigger.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				var name = trigger.getAttribute( 'data-bw-offcanvas-trigger' );
				var panel = document.querySelector( '[data-bw-offcanvas="' + name + '"]' );
				if ( panel && ! isResponsiveStatic( panel ) ) {
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

		var responsivePanels = document.querySelectorAll( '[data-bw-offcanvas-responsive]' );

		if ( responsivePanels.length ) {
			responsivePanels.forEach( syncResponsivePanel );

			var resizeTimer;
			window.addEventListener( 'resize', function () {
				window.clearTimeout( resizeTimer );
				resizeTimer = window.setTimeout( function () {
					responsivePanels.forEach( syncResponsivePanel );
				}, 150 );
			} );
		}
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'offcanvas', initOffcanvas );

		// Öffentliche API, damit andere Module (z.B. Side-Cart nach
		// Add-to-Cart) ein Panel programmatisch öffnen können.
		window.bodywings.openOffcanvas = function ( name ) {
			var panel = document.querySelector( '[data-bw-offcanvas="' + name + '"]' );
			if ( panel && ! isResponsiveStatic( panel ) ) {
				openOffcanvas( panel );
			}
		};
	}
} )();
