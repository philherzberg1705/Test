( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;

	registerBlockType( 'bw/cta-banner', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-cta-banner' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor || 'green' ) );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Button', 'bodywings' ), initialOpen: true },
						el( TextControl, { label: __( 'Button-Text', 'bodywings' ), value: attributes.buttonText, onChange: function ( v ) { setAttributes( { buttonText: v } ); } } ),
						el( TextControl, { label: __( 'Button-Link', 'bodywings' ), value: attributes.buttonUrl, onChange: function ( v ) { setAttributes( { buttonUrl: v } ); } } )
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner bw-cta-banner__inner' },
						el( 'div', { className: 'bw-cta-banner__text' },
							el( RichText, {
								tagName: 'h2',
								className: 'bw-cta-banner__heading',
								placeholder: __( 'Überschrift …', 'bodywings' ),
								value: attributes.heading,
								onChange: function ( v ) { setAttributes( { heading: v } ); },
								allowedFormats: [],
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'bw-cta-banner__body',
								placeholder: __( 'Text (optional) …', 'bodywings' ),
								value: attributes.text,
								onChange: function ( v ) { setAttributes( { text: v } ); },
							} )
						),
						attributes.buttonText
							? el( 'span', { className: 'bw-btn bw-cta-banner__button' }, attributes.buttonText )
							: null
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
