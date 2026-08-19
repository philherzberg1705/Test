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

	var ICONS = [
		{ label: __( 'Häkchen', 'bodywings' ), value: 'check' },
		{ label: __( 'Stern', 'bodywings' ), value: 'star' },
		{ label: __( 'Herz', 'bodywings' ), value: 'heart' },
		{ label: __( 'Lieferwagen', 'bodywings' ), value: 'truck' },
		{ label: __( 'Globus', 'bodywings' ), value: 'globe' },
		{ label: __( 'Tasche', 'bodywings' ), value: 'bag' },
	];

	registerBlockType( 'bw/icon-list-item', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { className: 'bw-icon-list__item' } );

			return el(
				bw.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Icon', 'bodywings' ), initialOpen: true },
						el( SelectControl, { label: __( 'Icon', 'bodywings' ), value: attributes.icon, options: ICONS, onChange: function ( v ) { setAttributes( { icon: v } ); } } )
					)
				),
				el(
					'div',
					blockProps,
					el( 'div', { className: 'bw-icon bw-icon-list__item-icon-preview', style: { fontSize: '11px', opacity: 0.6 } }, '[' + attributes.icon + ']' ),
					el( RichText, {
						tagName: 'h3',
						className: 'bw-icon-list__item-title',
						placeholder: __( 'Titel …', 'bodywings' ),
						value: attributes.title,
						onChange: function ( v ) { setAttributes( { title: v } ); },
						allowedFormats: [],
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'bw-icon-list__item-text',
						placeholder: __( 'Text (optional) …', 'bodywings' ),
						value: attributes.text,
						onChange: function ( v ) { setAttributes( { text: v } ); },
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp, window.bwBlocks );
