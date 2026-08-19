( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;

	var TEMPLATE = [
		[ 'bw/faq-item', {} ],
		[ 'bw/faq-item', {} ],
		[ 'bw/faq-item', {} ],
	];

	registerBlockType( 'bw/faq', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-faq' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			var innerBlocksProps = useInnerBlocksProps(
				{ className: 'bw-faq__list' },
				{ allowedBlocks: [ 'bw/faq-item' ], template: TEMPLATE }
			);

			return el(
				bw.Fragment,
				{},
				el( InspectorControls, {}, bw.BgColorControl( attributes.bgColor, setAttributes ) ),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner bw-faq__inner' },
						el( RichText, {
							tagName: 'h2',
							className: 'bw-faq__heading',
							placeholder: __( 'Überschrift …', 'bodywings' ),
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
