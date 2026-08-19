( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var PanelBody = wp.components.PanelBody;
	var ToggleControl = wp.components.ToggleControl;
	var Button = wp.components.Button;

	registerBlockType( 'bw/hero-small', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-hero-small' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Hintergrundbild (optional)', 'bodywings' ), initialOpen: true },
						el(
							MediaUploadCheck,
							{},
							el( MediaUpload, {
								onSelect: function ( media ) {
									setAttributes( { backgroundImageId: media.id, backgroundImageUrl: media.url } );
								},
								allowedTypes: [ 'image' ],
								value: attributes.backgroundImageId,
								render: function ( obj ) {
									return el( Button, { variant: 'secondary', onClick: obj.open }, attributes.backgroundImageUrl ? __( 'Bild ändern', 'bodywings' ) : __( 'Bild auswählen', 'bodywings' ) );
								},
							} )
						),
						attributes.backgroundImageUrl
							? el( Button, { isDestructive: true, variant: 'link', onClick: function () { setAttributes( { backgroundImageId: 0, backgroundImageUrl: '' } ); } }, __( 'Bild entfernen', 'bodywings' ) )
							: null
					),
					el(
						PanelBody,
						{ title: __( 'Layout', 'bodywings' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Als Haupt-Überschrift (H1) der Seite verwenden', 'bodywings' ),
							checked: !! attributes.asH1,
							onChange: function ( v ) { setAttributes( { asH1: v } ); },
						} )
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner bw-hero-small__inner' },
						el( RichText, {
							tagName: attributes.asH1 ? 'h1' : 'h2',
							className: 'bw-hero-small__heading',
							placeholder: __( 'Headline …', 'bodywings' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); },
							allowedFormats: [],
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'bw-hero-small__subheading',
							placeholder: __( 'Subline (optional) …', 'bodywings' ),
							value: attributes.subheading,
							onChange: function ( v ) { setAttributes( { subheading: v } ); },
						} )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
