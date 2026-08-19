/**
 * Scroll-Bild-Reveal (§21/§23): Bild+Headline wachsen als eine Einheit
 * per gescrubbtem transform:scale() bis auf volle Bildschirmbreite,
 * danach blenden Text + eine leichte Headline-Verschiebung ein — eine
 * einzige an die Scrollposition gekoppelte GSAP-Timeline (komplexe
 * Scroll-Sequenz, klar §23-Fall für GSAP statt CSS).
 *
 * transform:scale() statt tatsächlicher Breiten-/Größenänderung: reine
 * Compositor-Eigenschaft, kein Layout-Reflow bei jedem Scroll-Frame
 * (§26) — UND die Headline (Kindelement von .media) skaliert dadurch
 * korrekt mit dem Bild mit, bleibt also die ganze Zeit sichtbar "auf"
 * dem Bild statt separat positioniert zu werden.
 *
 * Bei prefers-reduced-motion bzw. ohne GSAP bleibt die serverseitig
 * gerenderte statische Ansicht (Bild bereits normal groß, Text direkt
 * darunter) unverändert stehen — volle Funktionalität ohne Bewegung (§25).
 */
( function () {
	'use strict';

	function initScrollReveal( scroller ) {
		var stage = scroller.querySelector( '[data-bw-scroll-reveal-stage]' );
		var media = scroller.querySelector( '[data-bw-scroll-reveal-media]' );
		var heading = scroller.querySelector( '[data-bw-scroll-reveal-heading]' );
		var text = scroller.querySelector( '[data-bw-scroll-reveal-text]' );

		if ( ! stage || ! media || ! heading ) {
			return;
		}

		var reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		if ( reducedMotion || ! window.gsap || ! window.ScrollTrigger ) {
			return;
		}

		window.gsap.registerPlugin( window.ScrollTrigger );
		scroller.classList.add( 'is-enhanced' );

		window.gsap.set( media, { scale: 0.62, borderRadius: 28 } );
		if ( text ) {
			window.gsap.set( text, { opacity: 0, y: 24 } );
		}

		var tl = window.gsap.timeline( {
			defaults: { ease: 'none' },
			scrollTrigger: {
				trigger: scroller,
				start: 'top top',
				end: 'bottom bottom',
				scrub: true,
				pin: stage,
				anticipatePin: 1,
			},
		} );

		tl.to( media, { scale: 1, borderRadius: 0, duration: 0.7 }, 0 );
		tl.to( heading, { y: -32, duration: 0.15 }, 0.62 );

		if ( text ) {
			tl.to( text, { opacity: 1, y: 0, duration: 0.2 }, 0.68 );
		}
	}

	function initAll() {
		document.querySelectorAll( '[data-bw-scroll-reveal]' ).forEach( initScrollReveal );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}
} )();
