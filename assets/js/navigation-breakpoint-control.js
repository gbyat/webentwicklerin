( function ( wp ) {
	if (
		! wp ||
		! wp.hooks ||
		! wp.compose ||
		! wp.element ||
		! wp.blockEditor ||
		! wp.components ||
		! wp.i18n
	) {
		return;
	}

	var addFilter = wp.hooks.addFilter;
	var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
	var createElement = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var NumberControl = wp.components.__experimentalNumberControl;
	var __ = wp.i18n.__;
	var ATTRIBUTE_NAME = 'webentwicklerinBreakpoint';
	var ALIGN_ATTRIBUTE_NAME = 'webentwicklerinMobileIconAlign';
	var DEFAULT_BREAKPOINT = 600;
	var DEFAULT_ALIGNMENT = 'inherit';
	var EDITOR_PREVIEW_STYLE_ID = 'webentwicklerin-navigation-alignment-preview';

	function getOverlayMenuMode( attributes ) {
		if ( ! attributes || typeof attributes.overlayMenu === 'undefined' ) {
			return 'mobile';
		}

		return attributes.overlayMenu;
	}

	function sanitizeValue( value ) {
		var parsed = parseInt( value, 10 );

		if ( Number.isNaN( parsed ) ) {
			return DEFAULT_BREAKPOINT;
		}

		if ( parsed < 320 ) {
			return 320;
		}

		if ( parsed > 1920 ) {
			return 1920;
		}

		return parsed;
	}

	function addNavigationBreakpointAttribute( settings, blockName ) {
		if ( blockName !== 'core/navigation' ) {
			return settings;
		}

		settings.attributes = Object.assign( {}, settings.attributes, {
			webentwicklerinBreakpoint: {
				type: 'number',
				default: DEFAULT_BREAKPOINT,
			},
			webentwicklerinMobileIconAlign: {
				type: 'string',
				default: DEFAULT_ALIGNMENT,
			},
		} );

		return settings;
	}

	function sanitizeAlignment( value ) {
		if ( value === 'inherit' || value === 'left' || value === 'center' || value === 'right' ) {
			return value;
		}

		return DEFAULT_ALIGNMENT;
	}

	function ensureEditorPreviewStyles() {
		if ( ! document || document.getElementById( EDITOR_PREVIEW_STYLE_ID ) ) {
			return;
		}

		var style = document.createElement( 'style' );
		style.id = EDITOR_PREVIEW_STYLE_ID;
		style.textContent =
			'.block-editor-block-list__block.webentwicklerin-mobile-icon-align-left nav.wp-block-navigation,' +
			'.block-editor-block-list__block.webentwicklerin-mobile-icon-align-center nav.wp-block-navigation,' +
			'.block-editor-block-list__block.webentwicklerin-mobile-icon-align-right nav.wp-block-navigation{width:100%!important;}' +
			'.block-editor-block-list__block.webentwicklerin-mobile-icon-align-left nav.wp-block-navigation .wp-block-navigation__responsive-container-open{margin-left:0!important;margin-right:auto!important;}' +
			'.block-editor-block-list__block.webentwicklerin-mobile-icon-align-center nav.wp-block-navigation .wp-block-navigation__responsive-container-open{margin-left:auto!important;margin-right:auto!important;}' +
			'.block-editor-block-list__block.webentwicklerin-mobile-icon-align-right nav.wp-block-navigation .wp-block-navigation__responsive-container-open{margin-left:auto!important;margin-right:0!important;}';

		document.head.appendChild( style );
	}

	function createBreakpointControl( value, onChange ) {
		var label = __( 'Responsive navigation breakpoint (px)', 'webentwicklerin' );
		var help = __( 'Defines when the menu switches to the mobile toggle.', 'webentwicklerin' );

		if ( NumberControl ) {
			return createElement( NumberControl, {
				label: label,
				help: help,
				value: value,
				min: 320,
				max: 1920,
				step: 1,
				onChange: onChange,
			} );
		}

		return createElement( TextControl, {
			label: label,
			help: help,
			type: 'number',
			value: value,
			min: 320,
			max: 1920,
			step: 1,
			onChange: onChange,
		} );
	}

	function createAlignmentControl( value, onChange ) {
		return createElement( SelectControl, {
			label: __( 'Mobile menu icon alignment', 'webentwicklerin' ),
			help: __( 'Aligns the visible mobile menu icon independently from the menu alignment.', 'webentwicklerin' ),
			value: value,
			options: [
				{
					label: __( 'Inherit (native behavior)', 'webentwicklerin' ),
					value: 'inherit',
				},
				{
					label: __( 'Left', 'webentwicklerin' ),
					value: 'left',
				},
				{
					label: __( 'Center', 'webentwicklerin' ),
					value: 'center',
				},
				{
					label: __( 'Right', 'webentwicklerin' ),
					value: 'right',
				},
			],
			onChange: onChange,
		} );
	}

	var withNavigationBreakpointControl = createHigherOrderComponent(
		function ( BlockEdit ) {
			return function ( props ) {
				if ( props.name !== 'core/navigation' ) {
					return createElement( BlockEdit, props );
				}

				var currentValue = props.attributes[ ATTRIBUTE_NAME ];
				var safeValue = Number.isFinite( currentValue ) ? currentValue : DEFAULT_BREAKPOINT;
				var currentAlignment = props.attributes[ ALIGN_ATTRIBUTE_NAME ];
				var safeAlignment = sanitizeAlignment( currentAlignment );
				var overlayMode = getOverlayMenuMode( props.attributes );
				var hasMobileOverlay = overlayMode === 'mobile';
				var hasToggleIcon = overlayMode === 'mobile' || overlayMode === 'always';

				function onChangeBreakpoint( nextValue ) {
					props.setAttributes( {
						webentwicklerinBreakpoint: sanitizeValue( nextValue ),
					} );
				}

				function onChangeAlignment( nextValue ) {
					props.setAttributes( {
						webentwicklerinMobileIconAlign: sanitizeAlignment( nextValue ),
					} );
				}

				return createElement(
					Fragment,
					null,
					createElement( BlockEdit, props ),
					createElement(
						InspectorControls,
						null,
						createElement(
							PanelBody,
							{
								title: __( 'Responsive Settings', 'webentwicklerin' ),
								initialOpen: true,
							},
							hasMobileOverlay
								? createBreakpointControl( safeValue, onChangeBreakpoint )
								: null,
							hasToggleIcon
								? createAlignmentControl( safeAlignment, onChangeAlignment )
								: createElement( 'p', null, __( 'Available when Overlay menu is set to Mobile or Always.', 'webentwicklerin' ) )
						)
					)
				);
			};
		},
		'withNavigationBreakpointControl'
	);

	var withNavigationAlignmentPreview = createHigherOrderComponent(
		function ( BlockListBlock ) {
			return function ( props ) {
				if ( props.name !== 'core/navigation' ) {
					return createElement( BlockListBlock, props );
				}

				ensureEditorPreviewStyles();

				var attributes = props.attributes || {};
				var overlayMode = getOverlayMenuMode( attributes );
				var hasToggleIcon = overlayMode === 'mobile' || overlayMode === 'always';
				var alignment = sanitizeAlignment( attributes[ ALIGN_ATTRIBUTE_NAME ] );
				var alignmentClass = hasToggleIcon && alignment !== 'inherit'
					? 'webentwicklerin-mobile-icon-align-' + alignment
					: '';
				var className = props.className ? props.className + ' ' + alignmentClass : alignmentClass;

				return createElement(
					BlockListBlock,
					Object.assign( {}, props, {
						className: className,
					} )
				);
			};
		},
		'withNavigationAlignmentPreview'
	);

	addFilter(
		'blocks.registerBlockType',
		'webentwicklerin/navigation-breakpoint-attribute',
		addNavigationBreakpointAttribute
	);

	addFilter(
		'editor.BlockEdit',
		'webentwicklerin/navigation-breakpoint-control',
		withNavigationBreakpointControl
	);

	addFilter(
		'editor.BlockListBlock',
		'webentwicklerin/navigation-alignment-preview',
		withNavigationAlignmentPreview
	);
} )( window.wp );
