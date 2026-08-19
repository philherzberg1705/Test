/**
 * Geteilte Slider-Engine (§21/§23) für Produkt-/Team-/Testimonial-Slider.
 * CSS-Scroll-Snap statt einer JS-Karussell-Library — native Touch-/
 * Trackpad-Gesten, kein zusätzliches Animationssystem neben GSAP/CSS
 * (§23), funktioniert ohne JS bereits als horizontal scrollbarer Bereich
 * (progressive enhancement: Pfeil-Buttons sind nur das Komfort-Add-on).
 *
 * Markup-Vertrag:
 *   [data-bw-slider]        Wrapper
 *   [data-bw-slider-track]  scrollbarer Container, Kinder = Slides
 *   [data-bw-slider-prev]   optionaler "Zurück"-Button
 *   [data-bw-slider-next]   optionaler "Weiter"-Button
 */
( function () {
	'use strict';

	function scrollByPage( track, direction ) {
		var amount = track.clientWidth * 0.9 * direction;
		track.scrollBy( { left: amount, behavior: 'smooth' } );
	}

	function updateButtonState( slider, track ) {
		var prevBtn = slider.querySelector( '[data-bw-slider-prev]' );
		var nextBtn = slider.querySelector( '[data-bw-slider-next]' );
		var maxScroll = track.scrollWidth - track.clientWidth - 1;

		if ( prevBtn ) {
			prevBtn.disabled = track.scrollLeft <= 0;
		}
		if ( nextBtn ) {
			nextBtn.disabled = track.scrollLeft >= maxScroll;
		}
	}

	function initSlider( slider ) {
		var track = slider.querySelector( '[data-bw-slider-track]' );
		if ( ! track ) {
			return;
		}

		var prevBtn = slider.querySelector( '[data-bw-slider-prev]' );
		var nextBtn = slider.querySelector( '[data-bw-slider-next]' );

		if ( prevBtn ) {
			prevBtn.addEventListener( 'click', function () {
				scrollByPage( track, -1 );
			} );
		}
		if ( nextBtn ) {
			nextBtn.addEventListener( 'click', function () {
				scrollByPage( track, 1 );
			} );
		}

		track.addEventListener( 'scroll', function () {
			window.requestAnimationFrame( function () {
				updateButtonState( slider, track );
			} );
		}, { passive: true } );

		updateButtonState( slider, track );
	}

	function initAllSliders() {
		document.querySelectorAll( '[data-bw-slider]' ).forEach( initSlider );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initAllSliders );
	} else {
		initAllSliders();
	}

	// Neu ins DOM eingefügte Slider (z.B. nach einem AJAX-Refresh) erneut
	// initialisieren, gleiches Prinzip wie bodywings:grid-updated beim
	// Produktfilter.
	document.addEventListener( 'bodywings:grid-updated', initAllSliders );
} )();
