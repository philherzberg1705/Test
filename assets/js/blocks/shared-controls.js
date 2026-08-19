/**
 * Geteilte Editor-Bausteine für alle BODYWINGS-Blöcke (§5/§33) — ohne
 * Build-Step, daher wp.element.createElement statt JSX. Jedes Block-edit.js
 * lädt dieses Skript als Abhängigkeit (siehe block.json "editorScript") und
 * greift über window.bwBlocks darauf zu, damit die Hintergrundfarben-Logik
 * (§2.2/§5) nicht zwölfmal dupliziert wird.
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;

	var BG_OPTIONS = [
		{ label: __( 'Keine (Seitenhintergrund)', 'bodywings' ), value: '' },
		{ label: __( 'Grün', 'bodywings' ), value: 'green' },
		{ label: __( 'Beige', 'bodywings' ), value: 'beige' },
		{ label: __( 'Terrakotta', 'bodywings' ), value: 'terrakotta' },
		{ label: __( 'Grau', 'bodywings' ), value: 'grey' },
	];

	/**
	 * Inspector-Panel mit der Hintergrundfarben-Auswahl (§2.2: Font-Farbe
	 * wird automatisch passend gesetzt, hier nichts weiter zu tun).
	 *
	 * @param {string}   bgColor  Aktueller Wert von attributes.bgColor.
	 * @param {Function} onChange setAttributes-kompatibler Setter.
	 */
	function BgColorControl( bgColor, onChange ) {
		return el(
			InspectorControls,
			{},
			el(
				PanelBody,
				{ title: __( 'Hintergrundfarbe', 'bodywings' ), initialOpen: true },
				el( SelectControl, {
					label: __( 'Farbe', 'bodywings' ),
					value: bgColor || '',
					options: BG_OPTIONS,
					onChange: function ( value ) {
						onChange( { bgColor: value } );
					},
				} )
			)
		);
	}

	/**
	 * data-bw-bg im Editor-Vorschau-Wrapper spiegeln, damit die Auswahl
	 * sofort sichtbar ist (Kontrastfarbe kommt automatisch aus base.css).
	 */
	function bgPreviewProps( bgColor ) {
		return bgColor ? { 'data-bw-bg': bgColor } : {};
	}

	window.bwBlocks = {
		el: el,
		Fragment: wp.element.Fragment,
		__: __,
		BgColorControl: BgColorControl,
		bgPreviewProps: bgPreviewProps,
	};
} )( window.wp );
