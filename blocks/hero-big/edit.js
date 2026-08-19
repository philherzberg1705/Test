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

	registerBlockType( 'bw/hero-big', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( {
				className: 'bw-block bw-hero-big bw-hero-big--' + ( attributes.contentAlign || 'left' ),
				style: attributes.backgroundImageUrl
					? { backgroundImage: 'linear-gradient(rgb(26 26 26 / .35), rgb(26 26 26 / .35)), url(' + attributes.backgroundImageUrl + ')', backgroundSize: 'cover', backgroundPosition: 'center' }
					: {},
			} );

			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Hintergrundbild', 'bodywings' ), initialOpen: true },
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
									return el(
										Button,
										{ variant: 'secondary', onClick: obj.open },
										attributes.backgroundImageUrl ? __( 'Bild ändern', 'bodywings' ) : __( 'Bild auswählen', 'bodywings' )
									);
								},
							} )
						),
						attributes.backgroundImageUrl
							? el( Button, { isDestructive: true, variant: 'link', onClick: function () {
								setAttributes( { backgroundImageId: 0, backgroundImageUrl: '' } );
							} }, __( 'Bild entfernen', 'bodywings' ) )
							: null
					),
					el(
						PanelBody,
						{ title: __( 'Buttons', 'bodywings' ), initialOpen: false },
						el( TextControl, { label: __( 'Button 1 Text', 'bodywings' ), value: attributes.buttonText, onChange: function ( v ) { setAttributes( { buttonText: v } ); } } ),
						el( TextControl, { label: __( 'Button 1 Link', 'bodywings' ), value: attributes.buttonUrl, onChange: function ( v ) { setAttributes( { buttonUrl: v } ); } } ),
						el( TextControl, { label: __( 'Button 2 Text', 'bodywings' ), value: attributes.buttonText2, onChange: function ( v ) { setAttributes( { buttonText2: v } ); } } ),
						el( TextControl, { label: __( 'Button 2 Link', 'bodywings' ), value: attributes.buttonUrl2, onChange: function ( v ) { setAttributes( { buttonUrl2: v } ); } } )
					),
					el(
						PanelBody,
						{ title: __( 'Layout', 'bodywings' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Als Haupt-Überschrift (H1) der Seite verwenden', 'bodywings' ),
							help: __( 'Nur aktivieren, wenn diese Seite sonst keine eigene H1 hat (z.B. Startseite).', 'bodywings' ),
							checked: !! attributes.asH1,
							onChange: function ( v ) { setAttributes( { asH1: v } ); },
						} ),
						el(
							'p',
							{},
							__( 'Textausrichtung', 'bodywings' )
						),
						el(
							ButtonGroup,
							{},
							[ 'left', 'center' ].map( function ( value ) {
								return el( Button, {
									key: value,
									variant: attributes.contentAlign === value ? 'primary' : 'secondary',
									onClick: function () { setAttributes( { contentAlign: value } ); },
								}, 'left' === value ? __( 'Links', 'bodywings' ) : __( 'Zentriert', 'bodywings' ) );
							} )
						)
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner bw-hero-big__inner' },
						el( 'div', { className: 'bw-hero-big__content' },
							el( RichText, {
								tagName: attributes.asH1 ? 'h1' : 'h2',
								className: 'bw-hero-big__heading',
								placeholder: __( 'Headline …', 'bodywings' ),
								value: attributes.heading,
								onChange: function ( v ) { setAttributes( { heading: v } ); },
								allowedFormats: [],
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'bw-hero-big__subheading',
								placeholder: __( 'Subline (optional) …', 'bodywings' ),
								value: attributes.subheading,
								onChange: function ( v ) { setAttributes( { subheading: v } ); },
							} )
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
