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
	var RangeControl = wp.components.RangeControl;
	var Button = wp.components.Button;

	function imagePicker( label, imageUrl, onSelect ) {
		return el(
			MediaUploadCheck,
			{},
			el( MediaUpload, {
				onSelect: onSelect,
				allowedTypes: [ 'image' ],
				render: function ( obj ) {
					return el( Button, { variant: 'secondary', onClick: obj.open }, imageUrl ? __( 'Ändern', 'bodywings' ) : label );
				},
			} )
		);
	}

	registerBlockType( 'bw/before-after', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-before-after' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			var hasImages = attributes.beforeImageUrl && attributes.afterImageUrl;

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Vorher-Bild', 'bodywings' ), initialOpen: true },
						imagePicker( __( 'Bild auswählen', 'bodywings' ), attributes.beforeImageUrl, function ( media ) {
							setAttributes( { beforeImageId: media.id, beforeImageUrl: media.url, beforeImageAlt: media.alt || '' } );
						} ),
						el( TextControl, { label: __( 'Beschriftung', 'bodywings' ), value: attributes.beforeLabel, onChange: function ( v ) { setAttributes( { beforeLabel: v } ); } } )
					),
					el(
						PanelBody,
						{ title: __( 'Nachher-Bild', 'bodywings' ), initialOpen: true },
						imagePicker( __( 'Bild auswählen', 'bodywings' ), attributes.afterImageUrl, function ( media ) {
							setAttributes( { afterImageId: media.id, afterImageUrl: media.url, afterImageAlt: media.alt || '' } );
						} ),
						el( TextControl, { label: __( 'Beschriftung', 'bodywings' ), value: attributes.afterLabel, onChange: function ( v ) { setAttributes( { afterLabel: v } ); } } )
					),
					el(
						PanelBody,
						{ title: __( 'Startposition', 'bodywings' ), initialOpen: false },
						el( RangeControl, {
							label: __( 'Regler-Startposition (%)', 'bodywings' ),
							value: attributes.startPosition,
							onChange: function ( v ) { setAttributes( { startPosition: v } ); },
							min: 0,
							max: 100,
						} )
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner' },
						el( RichText, {
							tagName: 'h2',
							className: 'bw-before-after__heading',
							placeholder: __( 'Überschrift (optional) …', 'bodywings' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); },
							allowedFormats: [],
						} ),
						hasImages
							? el( 'div', { className: 'bw-before-after__frame', style: { '--bw-ba-position': attributes.startPosition + '%' } },
								el( 'div', { className: 'bw-before-after__stage' },
									el( 'img', { className: 'bw-before-after__img bw-before-after__img--after', src: attributes.afterImageUrl, alt: '' } ),
									el( 'div', { className: 'bw-before-after__before-wrap' },
										el( 'img', { className: 'bw-before-after__img bw-before-after__img--before', src: attributes.beforeImageUrl, alt: '' } )
									),
									attributes.beforeLabel ? el( 'span', { className: 'bw-before-after__label bw-before-after__label--before' }, attributes.beforeLabel ) : null,
									attributes.afterLabel ? el( 'span', { className: 'bw-before-after__label bw-before-after__label--after' }, attributes.afterLabel ) : null,
									el( 'div', { className: 'bw-before-after__handle' } )
								)
							)
							: el( 'p', { className: 'bw-block__editor-note', style: { opacity: 0.6, fontStyle: 'italic' } }, __( 'Bitte in der Seitenleiste ein Vorher- und ein Nachher-Bild auswählen.', 'bodywings' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
