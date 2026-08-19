/**
 * Produktkarte: Varianten-Bildwechsel (§8.1) rein clientseitig, kein
 * Reload, keine AJAX-Anfrage nötig — alle Variationsbilder sind bereits
 * serverseitig als data-Attribute in der Karte vorhanden.
 */
( function () {
	'use strict';

	function swapImage( img, newSrc ) {
		if ( ! newSrc || img.src === newSrc ) {
			return;
		}

		img.classList.add( 'is-swapping' );

		var onLoaded = function () {
			img.classList.remove( 'is-swapping' );
			img.removeEventListener( 'load', onLoaded );
		};

		img.addEventListener( 'load', onLoaded );
		img.src = newSrc;
	}

	function onSwatchClick( event ) {
		var button = event.currentTarget;
		var card = button.closest( '[data-bw-product-card]' );
		if ( ! card ) {
			return;
		}

		var image = card.querySelector( '[data-bw-card-image="primary"]' );
		if ( image ) {
			swapImage( image, button.getAttribute( 'data-bw-variant-image' ) );
		}

		card.querySelectorAll( '.bw-swatch' ).forEach( function ( swatch ) {
			swatch.classList.remove( 'is-active' );
			swatch.setAttribute( 'aria-pressed', 'false' );
		} );

		button.classList.add( 'is-active' );
		button.setAttribute( 'aria-pressed', 'true' );
	}

	function initProductCards() {
		document.querySelectorAll( '.bw-swatch' ).forEach( function ( button ) {
			button.addEventListener( 'click', onSwatchClick );
		} );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'product-card', initProductCards );
	}
} )();
