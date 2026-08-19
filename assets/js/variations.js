/**
 * Progressive Erweiterung der nativen WooCommerce-Variations-<select>-
 * Felder zu Bild-Swatches (§9/§12) — die eigentliche Varianten-Logik
 * (Preis/Bild/Verfügbarkeit ohne Reload) bleibt vollständig WooCommerce
 * (wc-add-to-cart-variation), wir hängen uns nur an dessen jQuery-Events
 * (§31: vorhandene WooCommerce-Funktion nutzen statt nachzubauen).
 */
( function ( $ ) {
	'use strict';

	if ( ! $ ) {
		return;
	}

	function taxonomyFromSelectName( name ) {
		return name ? name.replace( /^attribute_/, '' ) : '';
	}

	function buildSwatches( $select, images ) {
		var $group = $( '<div class="bw-swatch-group" role="group"></div>' );

		$select.find( 'option' ).each( function () {
			var $option = $( this );
			var value = $option.attr( 'value' );

			if ( ! value ) {
				return;
			}

			var imageUrl = images[ value ];
			var $swatch = $( '<button type="button" class="bw-swatch"></button>' )
				.attr( 'aria-label', $option.text() )
				.attr( 'aria-pressed', $option.is( ':selected' ) ? 'true' : 'false' );

			if ( $option.is( ':selected' ) ) {
				$swatch.addClass( 'is-active' );
			}

			if ( imageUrl ) {
				$swatch.css( 'background-image', 'url(' + imageUrl + ')' );
			}

			$swatch.on( 'click', function () {
				$select.val( value ).trigger( 'change' );

				$group.find( '.bw-swatch' ).removeClass( 'is-active' ).attr( 'aria-pressed', 'false' );
				$swatch.addClass( 'is-active' ).attr( 'aria-pressed', 'true' );
			} );

			$group.append( $swatch );
		} );

		$select.after( $group ).addClass( 'bw-visually-hidden' );
	}

	function enhanceVariationForm( $form ) {
		var images = ( window.bodywingsProductData && window.bodywingsProductData.attributeImages ) || {};

		$form.find( 'table.variations select' ).each( function () {
			var $select = $( this );
			var taxonomy = taxonomyFromSelectName( $select.attr( 'name' ) );

			if ( images[ taxonomy ] ) {
				buildSwatches( $select, images[ taxonomy ] );
			}
		} );

		// Bildwechsel animieren — WC tauscht das Bild bereits selbst aus,
		// wir ergänzen nur einen kurzen Fade als visuelles Feedback (§12/§23).
		$form.on( 'found_variation', function () {
			var $image = $( '.woocommerce-product-gallery__image' ).first().find( 'img' );
			$image.addClass( 'bw-fade' );
			window.setTimeout( function () {
				$image.removeClass( 'bw-fade' );
			}, 280 );
		} );
	}

	function initVariations() {
		$( '.variations_form' ).each( function () {
			enhanceVariationForm( $( this ) );
		} );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'variations', initVariations );
	}
} )( window.jQuery );
