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
	var Button = wp.components.Button;

	registerBlockType( 'bw/team-member', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-team-slider__item' } );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Link (optional)', 'bodywings' ), initialOpen: true },
						el( TextControl, { label: __( 'Profil-/Social-Link', 'bodywings' ), value: attributes.linkUrl, onChange: function ( v ) { setAttributes( { linkUrl: v } ); } } )
					)
				),
				el(
					'div',
					blockProps,
					el( MediaUploadCheck, {},
						el( MediaUpload, {
							onSelect: function ( media ) { setAttributes( { photoId: media.id, photoUrl: media.url } ); },
							allowedTypes: [ 'image' ],
							value: attributes.photoId,
							render: function ( obj ) {
								return attributes.photoUrl
									? el( 'div', { className: 'bw-team-slider__photo', onClick: obj.open, role: 'button', tabIndex: 0 }, el( 'img', { src: attributes.photoUrl, alt: '' } ) )
									: el( Button, { variant: 'secondary', onClick: obj.open }, __( 'Foto auswählen', 'bodywings' ) );
							},
						} )
					),
					el( RichText, {
						tagName: 'p',
						className: 'bw-team-slider__name',
						placeholder: __( 'Name …', 'bodywings' ),
						value: attributes.name,
						onChange: function ( v ) { setAttributes( { name: v } ); },
						allowedFormats: [],
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'bw-team-slider__role',
						placeholder: __( 'Rolle …', 'bodywings' ),
						value: attributes.role,
						onChange: function ( v ) { setAttributes( { role: v } ); },
						allowedFormats: [],
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
