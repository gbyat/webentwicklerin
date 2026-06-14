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
	var FIT_STYLES = 'max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain';

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

	function figureHasAspectRatio(figure) {
		var style = figure.getAttribute('style') || '';

		return /aspect-ratio/i.test(style);
	}

	function fitImage(img) {
		var current = img.getAttribute('style') || '';
		var cleaned = current
			.replace(/\bwidth\s*:\s*[^;]+;?/gi, '')
			.replace(/\bheight\s*:\s*[^;]+;?/gi, '')
			.replace(/\bobject-fit\s*:[^;]+;?/gi, '')
			.replace(/\baspect-ratio\s*:[^;]+;?/gi, '')
			.replace(/;+/g, ';')
			.replace(/^;|;$/g, '')
			.trim();

		img.setAttribute('style', cleaned ? cleaned + ';' + FIT_STYLES : FIT_STYLES);
	}

	function getImageSrc(img) {
		return img.currentSrc || img.getAttribute('src') || '';
	}

	function resolveFigure(root) {
		if (root.matches('figure')) {
			return root;
		}

		var nestedFigure = root.querySelector('figure');

		if (nestedFigure && nestedFigure.querySelector('img')) {
			return nestedFigure;
		}

		return root.querySelector('img') ? root : null;
	}

	function collectFigures() {
		var figures = [];
		var seen = new Set();

		document.querySelectorAll(ROOT_SELECTOR).forEach(function (root) {
			var galleryHasStyle = root.classList.contains('wp-block-gallery');
			var figuresToProcess = [];

			if (galleryHasStyle) {
				root.querySelectorAll('figure').forEach(function (figure) {
					if (figure.querySelector('img')) {
						figuresToProcess.push(figure);
					}
				});
			} else {
				var figure = resolveFigure(root);

				if (figure) {
					figuresToProcess.push(figure);
				}
			}

			figuresToProcess.forEach(function (figure) {
				if (!figure.classList.contains(BLUR_CLASS) && !galleryHasStyle && !root.classList.contains(BLUR_CLASS)) {
					return;
				}

				if (!galleryHasStyle && !figure.classList.contains(BLUR_CLASS) && root.classList.contains(BLUR_CLASS)) {
					figure.classList.add(BLUR_CLASS);
				}

				if (!seen.has(figure)) {
					seen.add(figure);
					figures.push(figure);
				}
			});
		});

		return figures;
	}

	function processFigure(figure) {
		var img = figure.querySelector('img');

		if (!img) {
			return;
		}

		var src = getImageSrc(img);

		if (!src) {
			return;
		}

		var inlineStyle = figure.getAttribute('style') || '';
		var paddingParsed = parsePadding(inlineStyle);
		var customProperties =
			insetProperties(paddingParsed.insets) +
			"--featured-image-url:url('" + src.replace(/'/g, "\\'") + "');";

		figure.setAttribute(
			'style',
			paddingParsed.styles ? paddingParsed.styles + ';' + customProperties : customProperties
		);

		if (!figure.querySelector('.' + BLUR_LAYER_CLASS)) {
			var blurLayer = document.createElement('span');

			blurLayer.className = BLUR_LAYER_CLASS;
			blurLayer.setAttribute('aria-hidden', 'true');
			figure.insertBefore(blurLayer, figure.firstChild);
		}

		if (figureHasAspectRatio(figure)) {
			fitImage(img);
		}
	}

	function processAllFigures() {
		collectFigures().forEach(processFigure);
	}

	function init() {
		var wrapper = document.querySelector('.editor-styles-wrapper');

		if (!wrapper) {
			return;
		}

		var scheduleUpdate = debounce(processAllFigures, 80);

		processAllFigures();

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
