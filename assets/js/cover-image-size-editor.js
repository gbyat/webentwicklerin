/**
 * Cover block image size control for templates and site content.
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

(function () {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var addFilter = wp.hooks.addFilter;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var useSelect = wp.data.useSelect;
	var __ = wp.i18n.__;

	/**
	 * Inspector control for Cover background image size.
	 *
	 * @param {Object} props Block edit props.
	 * @return {?Object} React element.
	 */
	function CoverImageSizeControl(props) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		if ('image' !== attributes.backgroundType) {
			return null;
		}

		if (!attributes.useFeaturedImage && !attributes.id) {
			return null;
		}

		var imageSizes = useSelect(function (select) {
			var settings = select('core').getSettings();

			return settings && settings.imageSizes ? settings.imageSizes : [];
		}, []);

		var media = useSelect(
			function (select) {
				if (!attributes.id) {
					return null;
				}

				return select('core').getMedia(attributes.id);
			},
			[attributes.id]
		);

		if (!imageSizes.length) {
			return null;
		}

		var options = imageSizes.map(function (size) {
			return {
				label: size.name,
				value: size.slug,
			};
		});

		function onChange(sizeSlug) {
			var next = {
				sizeSlug: sizeSlug,
			};

			if (media) {
				if (
					media.media_details &&
					media.media_details.sizes &&
					media.media_details.sizes[sizeSlug] &&
					media.media_details.sizes[sizeSlug].source_url
				) {
					next.url = media.media_details.sizes[sizeSlug].source_url;
				} else if ('full' === sizeSlug && media.source_url) {
					next.url = media.source_url;
				}
			}

			setAttributes(next);
		}

		return el(
			InspectorControls,
			null,
			el(
				PanelBody,
				{
					title: __('Image size', 'webentwicklerin'),
					initialOpen: false,
				},
				el(SelectControl, {
					label: __('Resolution', 'webentwicklerin'),
					value: attributes.sizeSlug || 'full',
					options: options,
					onChange: onChange,
					help: attributes.useFeaturedImage
						? __(
								'Applies to the featured image when this template is viewed.',
								'webentwicklerin'
						  )
						: __(
								'Choose which registered image size loads on the front end.',
								'webentwicklerin'
						  ),
				})
			)
		);
	}

	/**
	 * Wrap the Cover block edit component with the image size control.
	 *
	 * @param {Object} BlockEdit Original block edit component.
	 * @return {Object} Wrapped block edit component.
	 */
	function withCoverImageSize(BlockEdit) {
		return function (props) {
			if ('core/cover' !== props.name) {
				return el(BlockEdit, props);
			}

			return el(
				Fragment,
				null,
				el(BlockEdit, props),
				el(CoverImageSizeControl, props)
			);
		};
	}

	addFilter('editor.BlockEdit', 'webentwicklerin/cover-image-size', withCoverImageSize);
})();
