( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;

	registerBlockType( 'bw/newsletter-cta', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-newsletter-block' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor || 'beige' ) );

			return el(
				bw.Fragment,
				{},
				el( InspectorControls, {}, bw.BgColorControl( attributes.bgColor, setAttributes ) ),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner bw-newsletter-block__inner' },
						el( 'div', { className: 'bw-newsletter' },
							el( RichText, {
								tagName: 'h2',
								className: 'bw-newsletter__title',
								placeholder: __( 'Newsletter', 'bodywings' ),
								value: attributes.heading,
								onChange: function ( v ) { setAttributes( { heading: v } ); },
								allowedFormats: [],
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'bw-newsletter__intro',
								placeholder: __( 'Neuigkeiten und Angebote direkt in dein Postfach.', 'bodywings' ),
								value: attributes.intro,
								onChange: function ( v ) { setAttributes( { intro: v } ); },
							} ),
							el( 'p', { className: 'bw-newsletter-block__editor-note', style: { opacity: 0.6, fontStyle: 'italic' } }, __( 'Das eigentliche Formular erscheint im Frontend (identisch mit dem Footer-Newsletter).', 'bodywings' ) )
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
