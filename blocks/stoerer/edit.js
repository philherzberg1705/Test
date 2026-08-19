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
	var SelectControl = wp.components.SelectControl;

	var ICONS = [
		{ label: __( 'Stern', 'bodywings' ), value: 'star' },
		{ label: __( 'Herz', 'bodywings' ), value: 'heart' },
		{ label: __( 'Häkchen', 'bodywings' ), value: 'check' },
		{ label: __( 'Lieferwagen', 'bodywings' ), value: 'truck' },
		{ label: __( 'Globus', 'bodywings' ), value: 'globe' },
		{ label: __( 'Kein Icon', 'bodywings' ), value: 'none' },
	];

	registerBlockType( 'bw/stoerer', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-stoerer' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor || 'terrakotta' ) );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Icon & Link', 'bodywings' ), initialOpen: true },
						el( SelectControl, { label: __( 'Icon', 'bodywings' ), value: attributes.icon, options: ICONS, onChange: function ( v ) { setAttributes( { icon: v } ); } } ),
						el( TextControl, { label: __( 'Link-Text (optional)', 'bodywings' ), value: attributes.linkText, onChange: function ( v ) { setAttributes( { linkText: v } ); } } ),
						el( TextControl, { label: __( 'Link-URL', 'bodywings' ), value: attributes.linkUrl, onChange: function ( v ) { setAttributes( { linkUrl: v } ); } } )
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner bw-stoerer__inner' },
						el( RichText, {
							tagName: 'span',
							className: 'bw-stoerer__text',
							placeholder: __( 'Hinweistext …', 'bodywings' ),
							value: attributes.text,
							onChange: function ( v ) { setAttributes( { text: v } ); },
							allowedFormats: [ 'core/bold' ],
						} ),
						attributes.linkText
							? el( 'span', { className: 'bw-stoerer__link' }, attributes.linkText )
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
