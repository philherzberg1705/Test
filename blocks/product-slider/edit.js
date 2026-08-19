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
	var SelectControl = wp.components.SelectControl;
	var RangeControl = wp.components.RangeControl;
	var Spinner = wp.components.Spinner;

	var SOURCES = [
		{ label: __( 'Neueste Produkte', 'bodywings' ), value: 'recent' },
		{ label: __( 'Hervorgehobene Produkte', 'bodywings' ), value: 'featured' },
		{ label: __( 'Im Angebot', 'bodywings' ), value: 'onsale' },
		{ label: __( 'Bestimmte Kategorie', 'bodywings' ), value: 'category' },
	];

	registerBlockType( 'bw/product-slider', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-product-slider' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			var categories = useSelect( function ( select ) {
				if ( 'category' !== attributes.source ) {
					return null;
				}
				return select( 'core' ).getEntityRecords( 'taxonomy', 'product_cat', { per_page: -1, hide_empty: true } );
			}, [ attributes.source ] );

			var categoryOptions = [ { label: __( 'Bitte wählen …', 'bodywings' ), value: 0 } ].concat(
				( categories || [] ).map( function ( term ) {
					return { label: term.name, value: term.id };
				} )
			);

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Produktauswahl', 'bodywings' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Quelle', 'bodywings' ),
							value: attributes.source,
							options: SOURCES,
							onChange: function ( v ) { setAttributes( { source: v } ); },
						} ),
						'category' === attributes.source
							? ( null === categories
								? el( Spinner, {} )
								: el( SelectControl, {
									label: __( 'Kategorie', 'bodywings' ),
									value: attributes.categoryId,
									options: categoryOptions,
									onChange: function ( v ) { setAttributes( { categoryId: parseInt( v, 10 ) } ); },
								} ) )
							: null,
						el( RangeControl, {
							label: __( 'Anzahl Produkte', 'bodywings' ),
							value: attributes.count,
							onChange: function ( v ) { setAttributes( { count: v } ); },
							min: 2,
							max: 20,
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
							className: 'bw-product-slider__heading',
							placeholder: __( 'Überschrift (optional) …', 'bodywings' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); },
							allowedFormats: [],
						} ),
						el( 'p', { className: 'bw-block__editor-note', style: { opacity: 0.6, fontStyle: 'italic' } }, __( 'Die tatsächlichen Produkte erscheinen im Frontend.', 'bodywings' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
