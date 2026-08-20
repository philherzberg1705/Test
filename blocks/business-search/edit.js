( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var PanelBody = wp.components.PanelBody;
	var Notice = wp.components.Notice;

	var searchData = window.bodywingsBusinessSearchEditorData || { roleConfigured: false };

	registerBlockType( 'bw/business-search', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-business-search' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Geschäftssuche', 'bodywings' ), initialOpen: true },
						searchData.roleConfigured
							? el( 'p', {}, __( 'Zeigt automatisch alle Geschäftspartner:innen der unter BODYWINGS → Geschäftssuche konfigurierten Rolle.', 'bodywings' ) )
							: el( Notice, { status: 'warning', isDismissible: false },
								__( 'Noch keine Rolle für die Geschäftssuche festgelegt. Unter BODYWINGS → Geschäftssuche konfigurieren, sonst bleibt dieser Bereich im Frontend leer.', 'bodywings' ) )
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner bw-business-search__inner' },
						el( RichText, {
							tagName: 'h2',
							className: 'bw-business-search__heading',
							placeholder: __( 'Geschäftssuche', 'bodywings' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); },
							allowedFormats: [],
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'bw-business-search__text',
							placeholder: __( 'Text (optional) …', 'bodywings' ),
							value: attributes.text,
							onChange: function ( v ) { setAttributes( { text: v } ); },
						} ),
						el( 'p', { className: 'bw-block__editor-note', style: { opacity: 0.6, fontStyle: 'italic' } },
							__( 'Karte, Liste und Suche erscheinen im Frontend.', 'bodywings' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
