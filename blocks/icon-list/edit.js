( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var PanelBody = wp.components.PanelBody;
	var ButtonGroup = wp.components.ButtonGroup;
	var Button = wp.components.Button;

	var TEMPLATE = [
		[ 'bw/icon-list-item', {} ],
		[ 'bw/icon-list-item', {} ],
		[ 'bw/icon-list-item', {} ],
	];

	registerBlockType( 'bw/icon-list', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var columns = attributes.columns || 3;
			var blockProps = useBlockProps( { className: 'bw-block bw-icon-list' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			var innerBlocksProps = useInnerBlocksProps(
				{ className: 'bw-icon-list__grid bw-icon-list__grid--' + columns },
				{ allowedBlocks: [ 'bw/icon-list-item' ], template: TEMPLATE, templateInsertUpdatesSelection: false }
			);

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Spalten', 'bodywings' ), initialOpen: true },
						el(
							ButtonGroup,
							{},
							[ 2, 3, 4 ].map( function ( value ) {
								return el( Button, {
									key: value,
									variant: columns === value ? 'primary' : 'secondary',
									onClick: function () { setAttributes( { columns: value } ); },
								}, String( value ) );
							} )
						)
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner' },
						el( RichText, {
							tagName: 'h2',
							className: 'bw-icon-list__heading',
							placeholder: __( 'Überschrift (optional) …', 'bodywings' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); },
							allowedFormats: [],
						} ),
						el( 'div', innerBlocksProps )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
