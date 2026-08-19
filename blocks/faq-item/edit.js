( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;

	registerBlockType( 'bw/faq-item', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-faq__item bw-faq__item--editor' } );

			return el(
				'div',
				blockProps,
				el( RichText, {
					tagName: 'p',
					className: 'bw-faq__question',
					placeholder: __( 'Frage …', 'bodywings' ),
					value: attributes.question,
					onChange: function ( v ) { setAttributes( { question: v } ); },
					allowedFormats: [],
				} ),
				el( RichText, {
					tagName: 'div',
					multiline: 'p',
					className: 'bw-faq__answer',
					placeholder: __( 'Antwort …', 'bodywings' ),
					value: attributes.answer,
					onChange: function ( v ) { setAttributes( { answer: v } ); },
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
