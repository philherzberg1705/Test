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
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;
	var ButtonGroup = wp.components.ButtonGroup;
	var Button = wp.components.Button;

	registerBlockType( 'bw/image-text', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var position = attributes.imagePosition || 'left';
			var blockProps = useBlockProps( { className: 'bw-block bw-image-text bw-image-text--' + position } );
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
						),
						el(
							ButtonGroup,
							{},
							[ 'left', 'right' ].map( function ( value ) {
								return el( Button, {
									key: value,
									variant: position === value ? 'primary' : 'secondary',
									onClick: function () { setAttributes( { imagePosition: value } ); },
								}, 'left' === value ? __( 'Bild links', 'bodywings' ) : __( 'Bild rechts', 'bodywings' ) );
							} )
						)
					),
					el(
						PanelBody,
						{ title: __( 'Button (optional)', 'bodywings' ), initialOpen: false },
						el( TextControl, { label: __( 'Text', 'bodywings' ), value: attributes.buttonText, onChange: function ( v ) { setAttributes( { buttonText: v } ); } } ),
						el( TextControl, { label: __( 'Link', 'bodywings' ), value: attributes.buttonUrl, onChange: function ( v ) { setAttributes( { buttonUrl: v } ); } } ),
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
					el( 'div', { className: 'bw-block__inner bw-image-text__inner' },
						el( 'div', { className: 'bw-image-text__media' },
							attributes.imageUrl
								? el( 'img', { src: attributes.imageUrl, alt: attributes.imageAlt } )
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
								)
						),
						el( 'div', { className: 'bw-image-text__content' },
							el( RichText, {
								tagName: attributes.asH1 ? 'h1' : 'h2',
								className: 'bw-image-text__heading',
								placeholder: __( 'Überschrift …', 'bodywings' ),
								value: attributes.heading,
								onChange: function ( v ) { setAttributes( { heading: v } ); },
								allowedFormats: [],
							} ),
							el( RichText, {
								tagName: 'div',
								multiline: 'p',
								className: 'bw-image-text__text',
								placeholder: __( 'Text …', 'bodywings' ),
								value: attributes.text,
								onChange: function ( v ) { setAttributes( { text: v } ); },
							} ),
							attributes.buttonText
								? el( 'span', { className: 'bw-btn' }, attributes.buttonText )
								: null
						)
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
