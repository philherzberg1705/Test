/**
 * Zentrales Motion-System für komplexe/Scroll-Animationen (§21/§23).
 * Einfache Hover-/Transition-Effekte laufen bewusst über reines CSS
 * (siehe *.css-Dateien) — GSAP kommt ausschließlich für Scroll-Reveals
 * zum Einsatz, damit kein zweites paralleles Animationssystem entsteht.
 *
 * Nur aktiv, wenn GSAP tatsächlich geladen wurde (bedingtes Enqueue, siehe
 * inc/core/enqueue.php) UND der Nutzer keine reduzierte Bewegung wünscht
 * (§25) — Inhalte bleiben in beiden Fällen vollständig sichtbar/nutzbar.
 */
( function () {
	'use strict';

	function initMotion() {
		var elements = document.querySelectorAll( '[data-bw-reveal]' );

		if ( ! elements.length ) {
			return;
		}

		var reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		if ( reducedMotion || ! window.gsap ) {
			// Keine Animation, aber Inhalt bleibt vollständig sichtbar (§25).
			elements.forEach( function ( el ) {
				el.style.opacity = '';
				el.style.transform = '';
			} );
			return;
		}

		if ( window.ScrollTrigger ) {
			window.gsap.registerPlugin( window.ScrollTrigger );
		}

		var duration = parseFloat( getComputedStyle( document.documentElement ).getPropertyValue( '--bw-duration-slow' ) ) / 1000 || 0.5;

		elements.forEach( function ( el, index ) {
			window.gsap.fromTo(
				el,
				{ opacity: 0, y: 24 },
				{
					opacity: 1,
					y: 0,
					duration: duration,
					ease: 'power2.out',
					delay: Math.min( index % 4, 3 ) * 0.06,
					scrollTrigger: {
						trigger: el,
						start: 'top 88%',
						once: true,
					},
				}
			);
		} );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'motion', initMotion );
	}
} )();
