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
		[ 'bw/team-member', {} ],
		[ 'bw/team-member', {} ],
		[ 'bw/team-member', {} ],
	];

	registerBlockType( 'bw/team-slider', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-team-slider' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			var innerBlocksProps = useInnerBlocksProps(
				{ className: 'bw-team-slider__track' },
				{ allowedBlocks: [ 'bw/team-member' ], template: TEMPLATE, orientation: 'horizontal' }
			);

			return el(
				bw.Fragment,
				{},
				el( InspectorControls, {}, bw.BgColorControl( attributes.bgColor, setAttributes ) ),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner' },
						el( RichText, {
							tagName: 'h2',
							className: 'bw-team-slider__heading',
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
