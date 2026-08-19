( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var useSelect = wp.data.useSelect;
	var PanelBody = wp.components.PanelBody;
	var FormTokenField = wp.components.FormTokenField;
	var RangeControl = wp.components.RangeControl;
	var Spinner = wp.components.Spinner;

	registerBlockType( 'bw/category-accordion', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-category-accordion' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			var terms = useSelect( function ( select ) {
				return select( 'core' ).getEntityRecords( 'taxonomy', 'product_cat', { per_page: -1, hide_empty: false } );
			}, [] );

			var byName = {};
			var byId = {};
			( terms || [] ).forEach( function ( term ) {
				byName[ term.name ] = term.id;
				byId[ term.id ] = term.name;
			} );

			var selectedNames = ( attributes.categoryIds || [] ).map( function ( id ) {
				return byId[ id ] || null;
			} ).filter( Boolean );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Kategorien', 'bodywings' ), initialOpen: true },
						null === terms
							? el( Spinner, {} )
							: el( FormTokenField, {
								label: __( 'Bestimmte Kategorien (leer = alle Hauptkategorien)', 'bodywings' ),
								value: selectedNames,
								suggestions: Object.keys( byName ),
								onChange: function ( tokens ) {
									var ids = tokens.map( function ( token ) { return byName[ token ]; } ).filter( Boolean );
									setAttributes( { categoryIds: ids } );
								},
							} ),
						el( RangeControl, {
							label: __( 'Maximale Anzahl', 'bodywings' ),
							value: attributes.count,
							onChange: function ( v ) { setAttributes( { count: v } ); },
							min: 2,
							max: 10,
							help: __( 'Mindestens 2 Kategorien, sonst ergibt ein Akkordeon keinen Sinn.', 'bodywings' ),
						} )
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner' },
						el( RichText, {
							tagName: 'h2',
							className: 'bw-category-accordion__heading',
							placeholder: __( 'Überschrift (optional) …', 'bodywings' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); },
							allowedFormats: [],
						} ),
						el( 'p', { className: 'bw-block__editor-note', style: { opacity: 0.6, fontStyle: 'italic' } }, __( 'Das Akkordeon (erste Kategorie offen, Bild+Beschreibung, Scroll-Steuerung) erscheint im Frontend.', 'bodywings' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
