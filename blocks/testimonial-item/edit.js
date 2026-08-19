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
	var RangeControl = wp.components.RangeControl;
	var Button = wp.components.Button;

	registerBlockType( 'bw/testimonial-item', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-testimonial-slider__item' } );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Bewertung', 'bodywings' ), initialOpen: true },
						el( RangeControl, {
							label: __( 'Sterne (0 = keine anzeigen)', 'bodywings' ),
							value: attributes.rating,
							onChange: function ( v ) { setAttributes( { rating: v } ); },
							min: 0,
							max: 5,
						} ),
						el(
							MediaUploadCheck,
							{},
							el( MediaUpload, {
								onSelect: function ( media ) { setAttributes( { avatarId: media.id, avatarUrl: media.url } ); },
								allowedTypes: [ 'image' ],
								value: attributes.avatarId,
								render: function ( obj ) {
									return el( Button, { variant: 'secondary', onClick: obj.open }, attributes.avatarUrl ? __( 'Foto ändern', 'bodywings' ) : __( 'Foto auswählen (optional)', 'bodywings' ) );
								},
							} )
						)
					)
				),
				el(
					'div',
					blockProps,
					el( RichText, {
						tagName: 'blockquote',
						className: 'bw-testimonial-slider__quote',
						placeholder: __( 'Zitat …', 'bodywings' ),
						value: attributes.quote,
						onChange: function ( v ) { setAttributes( { quote: v } ); },
					} ),
					el( 'div', { className: 'bw-testimonial-slider__author' },
						attributes.avatarUrl ? el( 'img', { className: 'bw-testimonial-slider__avatar', src: attributes.avatarUrl, alt: '' } ) : null,
						el( 'div', {},
							el( RichText, {
								tagName: 'p',
								className: 'bw-testimonial-slider__name',
								placeholder: __( 'Name …', 'bodywings' ),
								value: attributes.name,
								onChange: function ( v ) { setAttributes( { name: v } ); },
								allowedFormats: [],
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'bw-testimonial-slider__role',
								placeholder: __( 'Rolle/Unternehmen …', 'bodywings' ),
								value: attributes.role,
								onChange: function ( v ) { setAttributes( { role: v } ); },
								allowedFormats: [],
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
