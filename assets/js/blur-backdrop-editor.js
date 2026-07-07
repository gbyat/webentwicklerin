/**
 * Blur backdrop editor preview for image blocks with the blur-backdrop style.
 *
 * Mirrors frontend PHP output: blur layer, CSS variables, padding insets.
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

(function () {
	'use strict';

	var BLUR_CLASS = 'is-style-blur-backdrop';
	var BLUR_LAYER_CLASS = 'featured-image-blur-backdrop';
	var ROOT_SELECTOR = '.editor-styles-wrapper .' + BLUR_CLASS;
	var FIT_STYLES = 'max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;object-position:center center';

	function debounce(fn, wait) {
		var timeoutId;

		return function () {
			var context = this;
			var args = arguments;

			window.clearTimeout(timeoutId);
			timeoutId = window.setTimeout(function () {
				fn.apply(context, args);
			}, wait);
		};
	}

	function parsePadding(styles) {
		var insets = { top: '0', right: '0', bottom: '0', left: '0' };
		var sides = ['top', 'right', 'bottom', 'left'];
		var remaining = styles || '';

		sides.forEach(function (side) {
			var pattern = new RegExp('\\bpadding-' + side + '\\s*:\\s*([^;]+)', 'i');
			var match = remaining.match(pattern);

			if (match) {
				insets[side] = match[1].trim();
				remaining = remaining.replace(pattern, '');
			}
		});

		var shorthand = remaining.match(/\bpadding\s*:\s*([^;]+)/i);

		if (shorthand) {
			var parts = shorthand[1].trim().split(/\s+/);

			if (parts.length === 1) {
				insets = { top: parts[0], right: parts[0], bottom: parts[0], left: parts[0] };
			} else if (parts.length === 2) {
				insets = { top: parts[0], right: parts[1], bottom: parts[0], left: parts[1] };
			} else if (parts.length === 3) {
				insets = { top: parts[0], right: parts[1], bottom: parts[2], left: parts[1] };
			} else if (parts.length >= 4) {
				insets = { top: parts[0], right: parts[1], bottom: parts[2], left: parts[3] };
			}

			remaining = remaining.replace(/\bpadding\s*:[^;]+;?/i, '');
		}

		remaining = remaining.replace(/;+/g, ';').replace(/^;|;$/g, '').trim();

		return { insets: insets, styles: remaining };
	}

	function insetProperties(insets) {
		return (
			'--featured-image-inset-top:' + insets.top + ';' +
			'--featured-image-inset-right:' + insets.right + ';' +
			'--featured-image-inset-bottom:' + insets.bottom + ';' +
			'--featured-image-inset-left:' + insets.left + ';'
		);
	}

	function figureHasAspectRatio(container) {
		var style = container.getAttribute('style') || '';

		return /aspect-ratio/i.test(style);
	}

	function isCoverContainer(container) {
		return container.classList.contains('wp-block-cover');
	}

	function getContainerImage(container) {
		if (isCoverContainer(container)) {
			return container.querySelector('.wp-block-cover__image-background');
		}

		return container.querySelector('img');
	}

	function fitImage(img, alwaysFit) {
		var current = img.getAttribute('style') || '';
		var cleaned = current
			.replace(/\bposition\s*:\s*[^;]+;?/gi, '')
			.replace(/\btop\s*:\s*[^;]+;?/gi, '')
			.replace(/\bright\s*:\s*[^;]+;?/gi, '')
			.replace(/\bbottom\s*:\s*[^;]+;?/gi, '')
			.replace(/\bleft\s*:\s*[^;]+;?/gi, '')
			.replace(/\bwidth\s*:\s*[^;]+;?/gi, '')
			.replace(/\bheight\s*:\s*[^;]+;?/gi, '')
			.replace(/\bmax-width\s*:\s*[^;]+;?/gi, '')
			.replace(/\bmax-height\s*:\s*[^;]+;?/gi, '')
			.replace(/\bobject-fit\s*:[^;]+;?/gi, '')
			.replace(/\bobject-position\s*:[^;]+;?/gi, '')
			.replace(/\baspect-ratio\s*:[^;]+;?/gi, '')
			.replace(/;+/g, ';')
			.replace(/^;|;$/g, '')
			.trim();

		var fitStyles = alwaysFit
			? 'position:relative;top:auto;right:auto;bottom:auto;left:auto;max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;object-position:center center'
			: 'max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain';

		img.setAttribute('style', cleaned ? cleaned + ';' + fitStyles : fitStyles);
	}

	function getImageSrc(img) {
		return img.currentSrc || img.getAttribute('src') || '';
	}

	function resolveContainer(root) {
		if (root.matches('figure, .wp-block-cover')) {
			return root;
		}

		var nestedFigure = root.querySelector('figure');

		if (nestedFigure && nestedFigure.querySelector('img')) {
			return nestedFigure;
		}

		if (root.classList.contains('wp-block-cover')) {
			return root;
		}

		return root.querySelector('img') ? root : null;
	}

	function collectContainers() {
		var containers = [];
		var seen = new Set();

		document.querySelectorAll(ROOT_SELECTOR).forEach(function (root) {
			var galleryHasStyle = root.classList.contains('wp-block-gallery');
			var containersToProcess = [];

			if (galleryHasStyle) {
				root.querySelectorAll('figure').forEach(function (figure) {
					if (figure.querySelector('img')) {
						containersToProcess.push(figure);
					}
				});
			} else {
				var container = resolveContainer(root);

				if (container) {
					containersToProcess.push(container);
				}
			}

			containersToProcess.forEach(function (container) {
				if (!container.classList.contains(BLUR_CLASS) && !galleryHasStyle && !root.classList.contains(BLUR_CLASS)) {
					return;
				}

				if (!galleryHasStyle && !container.classList.contains(BLUR_CLASS) && root.classList.contains(BLUR_CLASS)) {
					container.classList.add(BLUR_CLASS);
				}

				if (!seen.has(container)) {
					seen.add(container);
					containers.push(container);
				}
			});
		});

		return containers;
	}

	function processContainer(container) {
		var img = getContainerImage(container);

		if (!img) {
			return;
		}

		var src = getImageSrc(img);

		if (!src) {
			return;
		}

		var inlineStyle = container.getAttribute('style') || '';
		var paddingParsed = parsePadding(inlineStyle);
		var customProperties =
			insetProperties(paddingParsed.insets) +
			"--featured-image-url:url('" + src.replace(/'/g, "\\'") + "');";

		container.setAttribute(
			'style',
			paddingParsed.styles ? paddingParsed.styles + ';' + customProperties : customProperties
		);

		if (!container.querySelector('.' + BLUR_LAYER_CLASS)) {
			var blurLayer = document.createElement('span');

			blurLayer.className = BLUR_LAYER_CLASS;
			blurLayer.setAttribute('aria-hidden', 'true');
			container.insertBefore(blurLayer, container.firstChild);
		}

		if (isCoverContainer(container) || figureHasAspectRatio(container)) {
			fitImage(img, isCoverContainer(container));
		}

		if (isCoverContainer(container)) {
			img.removeAttribute('width');
			img.removeAttribute('height');
		}
	}

	function processAllContainers() {
		collectContainers().forEach(processContainer);
	}

	function init() {
		var wrapper = document.querySelector('.editor-styles-wrapper');

		if (!wrapper) {
			return;
		}

		var scheduleUpdate = debounce(processAllContainers, 80);

		processAllContainers();

		var observer = new MutationObserver(scheduleUpdate);

		observer.observe(wrapper, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: ['class', 'style', 'src', 'srcset'],
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
