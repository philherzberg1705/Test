/**
 * Media-Uploader für Attribut-Bilder (§9), Admin-only, läuft nur auf den
 * WooCommerce-Attribut-Term-Screens (siehe wp_enqueue_media()-Bedingung
 * in inc/woocommerce/attribute-images.php).
 */
( function ( $ ) {
	'use strict';

	function initPicker( $picker ) {
		var frame;
		var $preview = $picker.find( '.bodywings-attribute-image-picker__preview' );
		var $input = $picker.find( '.bodywings-attribute-image-picker__input' );
		var $selectBtn = $picker.find( '.bodywings-attribute-image-picker__select' );
		var $removeBtn = $picker.find( '.bodywings-attribute-image-picker__remove' );

		$selectBtn.on( 'click', function ( event ) {
			event.preventDefault();

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: 'Bild auswählen',
				multiple: false,
				library: { type: 'image' },
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				var previewUrl = ( attachment.sizes && attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.url;

				$input.val( attachment.id );
				$preview.attr( 'src', previewUrl ).show();
				$removeBtn.show();
			} );

			frame.open();
		} );

		$removeBtn.on( 'click', function ( event ) {
			event.preventDefault();
			$input.val( '' );
			$preview.hide().attr( 'src', '' );
			$removeBtn.hide();
		} );
	}

	$( function () {
		$( '.bodywings-attribute-image-picker' ).each( function () {
			initPicker( $( this ) );
		} );
	} );
} )( jQuery );
