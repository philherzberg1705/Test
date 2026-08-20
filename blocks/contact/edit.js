( function ( wp, bw ) {
	'use strict';

	var el = bw.el;
	var __ = bw.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var Notice = wp.components.Notice;

	var cf7Data = window.bodywingsCf7EditorData || { active: false, forms: [] };

	registerBlockType( 'bw/contact', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-block bw-contact-block' } );
			Object.assign( blockProps, bw.bgPreviewProps( attributes.bgColor ) );

			var formOptions = [ { label: __( 'Bitte wählen …', 'bodywings' ), value: 0 } ].concat(
				cf7Data.forms.map( function ( form ) {
					return { label: form.title, value: form.id };
				} )
			);

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Formular', 'bodywings' ), initialOpen: true },
						cf7Data.active
							? el( SelectControl, {
								label: __( 'Contact-Form-7-Formular', 'bodywings' ),
								value: attributes.formId,
								options: formOptions,
								onChange: function ( v ) { setAttributes( { formId: parseInt( v, 10 ) } ); },
							} )
							: el( Notice, { status: 'warning', isDismissible: false },
								__( 'Contact Form 7 ist nicht aktiv. Bitte das Plugin installieren und aktivieren, damit dieser Block im Frontend ein Formular anzeigt.', 'bodywings' ) )
					),
					bw.BgColorControl( attributes.bgColor, setAttributes )
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-block__inner bw-contact-block__inner' },
						el( RichText, {
							tagName: 'h2',
							className: 'bw-contact-block__heading',
							placeholder: __( 'Kontakt', 'bodywings' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); },
							allowedFormats: [],
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'bw-contact-block__text',
							placeholder: __( 'Text (optional) …', 'bodywings' ),
							value: attributes.text,
							onChange: function ( v ) { setAttributes( { text: v } ); },
						} ),
						el( 'p', { className: 'bw-block__editor-note', style: { opacity: 0.6, fontStyle: 'italic' } },
							cf7Data.active
								? __( 'Das ausgewählte Formular erscheint im Frontend.', 'bodywings' )
								: __( 'Ohne aktives Contact Form 7 bleibt dieser Bereich im Frontend leer.', 'bodywings' ) )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
