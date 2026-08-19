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

	registerBlockType( 'bw/scroll-image-reveal', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-scroll-reveal' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Bild', 'bodywings' ), initialOpen: true },
						el(
							MediaUploadCheck,
							{},
							el( MediaUpload, {
								onSelect: function ( media ) {
									setAttributes( { imageId: media.id, imageUrl: media.url, imageAlt: media.alt || '' } );
								},
								allowedTypes: [ 'image' ],
								value: attributes.imageId,
								render: function ( obj ) {
									return el( Button, { variant: 'secondary', onClick: obj.open }, attributes.imageUrl ? __( 'Bild ändern', 'bodywings' ) : __( 'Bild auswählen', 'bodywings' ) );
								},
							} )
						)
					),
					el(
						PanelBody,
						{ title: __( 'Layout', 'bodywings' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Überschrift als H1 verwenden', 'bodywings' ),
							checked: !! attributes.asH1,
							onChange: function ( v ) { setAttributes( { asH1: v } ); },
						} )
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-scroll-reveal__stage', style: { position: 'static', height: 'auto' } },
						el( 'div', { className: 'bw-scroll-reveal__media' },
							attributes.imageUrl
								? el( 'img', { className: 'bw-scroll-reveal__image', src: attributes.imageUrl, alt: attributes.imageAlt } )
								: el(
									MediaUploadCheck,
									{},
									el( MediaUpload, {
										onSelect: function ( media ) {
											setAttributes( { imageId: media.id, imageUrl: media.url, imageAlt: media.alt || '' } );
										},
										allowedTypes: [ 'image' ],
										render: function ( obj ) {
											return el( Button, { variant: 'secondary', onClick: obj.open }, __( 'Bild auswählen', 'bodywings' ) );
										},
									} )
								),
							attributes.imageUrl ? el( 'div', { className: 'bw-scroll-reveal__scrim' } ) : null,
							el( RichText, {
								tagName: attributes.asH1 ? 'h1' : 'h2',
								className: 'bw-scroll-reveal__heading',
								placeholder: __( 'Headline …', 'bodywings' ),
								value: attributes.heading,
								onChange: function ( v ) { setAttributes( { heading: v } ); },
								allowedFormats: [],
							} )
						),
						el( 'div', { className: 'bw-block__inner' },
							el( RichText, {
								tagName: 'div',
								multiline: 'p',
								className: 'bw-scroll-reveal__text',
								placeholder: __( 'Info-Text …', 'bodywings' ),
								value: attributes.text,
								onChange: function ( v ) { setAttributes( { text: v } ); },
							} )
						),
						el( 'p', { className: 'bw-block__editor-note', style: { opacity: 0.6, fontStyle: 'italic', textAlign: 'center' } }, __( 'Der Scroll-Effekt (Bild wächst, Text blendet ein) läuft im Frontend.', 'bodywings' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
