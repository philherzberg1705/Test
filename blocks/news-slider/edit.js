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
	var FormTokenField = wp.components.FormTokenField;
	var Spinner = wp.components.Spinner;

	var SOURCES = [
		{ label: __( 'Neueste Beiträge', 'bodywings' ), value: 'recent' },
		{ label: __( 'Bestimmte Kategorie', 'bodywings' ), value: 'category' },
		{ label: __( 'Manuelle Auswahl', 'bodywings' ), value: 'manual' },
	];

	registerBlockType( 'bw/news-slider', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-news-slider' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			var categories = useSelect( function ( select ) {
				if ( 'category' !== attributes.source ) {
					return null;
				}
				return select( 'core' ).getEntityRecords( 'taxonomy', 'category', { per_page: -1, hide_empty: true } );
			}, [ attributes.source ] );

			var categoryOptions = [ { label: __( 'Bitte wählen …', 'bodywings' ), value: 0 } ].concat(
				( categories || [] ).map( function ( term ) {
					return { label: term.name, value: term.id };
				} )
			);

			// Für die manuelle Auswahl reicht ein clientseitig durchsuchbarer
			// Pool der letzten 50 Beiträge — ein Server-Suchfeld wäre für die
			// paar Redakteur:innen-Klicks unnötiger Aufwand (§35).
			var posts = useSelect( function ( select ) {
				if ( 'manual' !== attributes.source ) {
					return null;
				}
				return select( 'core' ).getEntityRecords( 'postType', 'post', { per_page: 50, orderby: 'date', order: 'desc' } );
			}, [ attributes.source ] );

			var postsById = {};
			var postsByTitle = {};
			( posts || [] ).forEach( function ( post ) {
				var title = post.title.rendered || __( '(ohne Titel)', 'bodywings' );
				postsById[ post.id ] = title;
				postsByTitle[ title ] = post.id;
			} );

			var selectedTitles = ( attributes.postIds || [] ).map( function ( id ) {
				return postsById[ id ] || null;
			} ).filter( Boolean );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Beitragsauswahl', 'bodywings' ), initialOpen: true },
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
						'manual' === attributes.source
							? ( null === posts
								? el( Spinner, {} )
								: el( FormTokenField, {
									label: __( 'Beiträge (sucht in den letzten 50)', 'bodywings' ),
									value: selectedTitles,
									suggestions: Object.keys( postsByTitle ),
									onChange: function ( tokens ) {
										var ids = tokens.map( function ( token ) { return postsByTitle[ token ]; } ).filter( Boolean );
										setAttributes( { postIds: ids } );
									},
								} ) )
							: null,
						'manual' !== attributes.source
							? el( RangeControl, {
								label: __( 'Anzahl Beiträge', 'bodywings' ),
								value: attributes.count,
								onChange: function ( v ) { setAttributes( { count: v } ); },
								min: 3,
								max: 12,
								help: __( 'Es werden immer mindestens 3 Karten angezeigt.', 'bodywings' ),
							} )
							: null
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner' },
						el( RichText, {
							tagName: 'h2',
							className: 'bw-news-slider__heading',
							placeholder: __( 'Überschrift (optional) …', 'bodywings' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); },
							allowedFormats: [],
						} ),
						el( 'p', { className: 'bw-block__editor-note', style: { opacity: 0.6, fontStyle: 'italic' } }, __( 'Die tatsächlichen Beiträge und die abschließende „Alle Neuigkeiten“-Karte erscheinen im Frontend.', 'bodywings' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
