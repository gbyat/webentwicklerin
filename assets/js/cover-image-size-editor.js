/**
 * Cover block image size control for templates and site content.
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

(function () {
	'use strict';

	if (
		!window.wp ||
		!wp.element ||
		!wp.hooks ||
		!wp.blockEditor ||
		!wp.components ||
		!wp.i18n
	) {
		return;
	}

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var addFilter = wp.hooks.addFilter;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var __ = wp.i18n.__;
	var imageSizes =
		window.webentwicklerinCoverImageSize &&
		window.webentwicklerinCoverImageSize.imageSizes
			? window.webentwicklerinCoverImageSize.imageSizes
			: [];

	/**
	 * Inspector control for Cover background image size.
	 *
	 * @param {Object} props Block edit props.
	 * @return {?Object} React element.
	 */
	function CoverImageSizeControl(props) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;
		var attachmentId = attributes.id || 0;
		var isImageBackground = 'image' === attributes.backgroundType;
		var hasImageSource = !!attributes.useFeaturedImage || !!attachmentId;

		if (!isImageBackground || !hasImageSource || !imageSizes.length) {
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

			if (attachmentId && wp.data && wp.data.select) {
				var media = wp.data.select('core').getMedia(attachmentId);

				if (
					media &&
					media.media_details &&
					media.media_details.sizes &&
					media.media_details.sizes[sizeSlug] &&
					media.media_details.sizes[sizeSlug].source_url
				) {
					next.url = media.media_details.sizes[sizeSlug].source_url;
				} else if (media && 'full' === sizeSlug && media.source_url) {
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
