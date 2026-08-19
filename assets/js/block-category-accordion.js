/**
 * Kategorien-Akkordeon: Klick UND Scrollposition steuern, welches Panel
 * offen ist (§21/§23). Öffnen/Schließen selbst ist ein einfacher State-
 * Wechsel → CSS-Transition (flex-basis). Die Kopplung an die Scrollposition
 * ist die "komplexe Scroll-Sequenz" aus §23 → GSAP ScrollTrigger, genau wie
 * im übrigen Motion-System (assets/js/motion.js) nur dafür eingesetzt.
 *
 * Bei prefers-reduced-motion bleibt nur die Klick-Steuerung aktiv — die
 * Funktionalität (jede Kategorie erreichbar) bleibt vollständig erhalten,
 * nur das automatische Scroll-Umschalten entfällt (§25).
 */
( function () {
	'use strict';

	function openItem( items, target ) {
		items.forEach( function ( item ) {
			var isTarget = item === target;
			var trigger = item.querySelector( '[data-bw-accordion-trigger]' );
			var panel = item.querySelector( '.bw-accordion__panel' );

			item.classList.toggle( 'is-open', isTarget );

			if ( trigger ) {
				trigger.setAttribute( 'aria-expanded', isTarget ? 'true' : 'false' );
			}

			if ( panel ) {
				if ( isTarget ) {
					panel.removeAttribute( 'inert' );
					panel.removeAttribute( 'aria-hidden' );
				} else {
					panel.setAttribute( 'inert', '' );
					panel.setAttribute( 'aria-hidden', 'true' );
				}
			}
		} );
	}

	function initAccordion( root ) {
		var items = Array.prototype.slice.call( root.querySelectorAll( '[data-bw-accordion-item]' ) );
		if ( items.length < 2 ) {
			return;
		}

		items.forEach( function ( item ) {
			var trigger = item.querySelector( '[data-bw-accordion-trigger]' );
			if ( trigger ) {
				trigger.addEventListener( 'click', function () {
					openItem( items, item );
				} );
			}
		} );

		var reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		if ( reducedMotion || ! window.gsap || ! window.ScrollTrigger ) {
			return;
		}

		window.gsap.registerPlugin( window.ScrollTrigger );

		items.forEach( function ( item ) {
			// Nach Abschluss der Öffnen/Schließen-Transition neu berechnen —
			// sonst driften die Trigger-Positionen auseinander, weil sich
			// die Höhe des gerade geöffneten Panels ändert.
			item.addEventListener( 'transitionend', function ( event ) {
				if ( 'flex-basis' === event.propertyName ) {
					window.ScrollTrigger.refresh();
				}
			} );

			window.ScrollTrigger.create( {
				trigger: item,
				start: 'top center',
				end: 'bottom center',
				onToggle: function ( self ) {
					if ( self.isActive ) {
						openItem( items, item );
					}
				},
			} );
		} );
	}

	function initAll() {
		document.querySelectorAll( '[data-bw-category-accordion]' ).forEach( initAccordion );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}
} )();
