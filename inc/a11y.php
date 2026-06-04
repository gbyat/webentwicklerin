<?php

/**
 * Accessibility helpers and filters.
 *
 * @package webethm
 * @since 2.0.0
 */

/**
 * Output a skip link and top anchor for assistive technology users.
 *
 * Uses theme .skip-link styles (visible on :focus) instead of screen-reader-text
 * alone, which is not reliably styled on the block theme front end.
 *
 * @since 2.0.0
 *
 * @return void
 */
add_action('wp_body_open', function () {
	echo sprintf(
		'<a class="skip-link" href="#site-content">%s</a>',
		esc_html__('Skip to main content', 'webentwicklerin')
	);
	echo sprintf(
		wp_kses_post('<span id="topofpage" class="screen-reader-text">%s</span>'),
		esc_html__('Anchor link to top of page', 'webentwicklerin')
	);
});

/**
 * Output a footer "go to top" control with an accessible label.
 *
 * @since 2.0.0
 *
 * @return void
 */
add_action('wp_footer', function () {
	printf(
		'<div id="gototop" class="animated hidden"><a href="#topofpage" class="gototop-link" aria-label="%s">%s</a></div>',
		esc_attr__('Go to top of page', 'webentwicklerin'),
		'&#8593;'
	);
});

/**
 * Make the main content skip target programmatically focusable.
 *
 * @since 2.0.0
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
add_filter(
	'render_block',
	function ($block_content, $block) {
		if ('core/group' !== ($block['blockName'] ?? '')) {
			return $block_content;
		}

		if (empty($block['attrs']['anchor']) || 'site-content' !== $block['attrs']['anchor']) {
			return $block_content;
		}

		if (false !== stripos($block_content, 'tabindex=')) {
			return $block_content;
		}

		return preg_replace(
			'/<main\b([^>]*)>/i',
			'<main$1 tabindex="-1">',
			$block_content,
			1
		);
	},
	10,
	2
);

/**
 * Build a contextual label prefix for pagination accessibility text.
 *
 * @since 2.0.0
 *
 * @return string
 */
function webentwicklerin_get_pagination_context_label()
{
	if (is_home() || is_front_page()) {
		return __('Blog pagination', 'webentwicklerin');
	}

	if (is_category()) {
		return __('Category pagination', 'webentwicklerin');
	}

	if (is_tag()) {
		return __('Tag pagination', 'webentwicklerin');
	}

	if (is_tax()) {
		return __('Taxonomy pagination', 'webentwicklerin');
	}

	if (is_search()) {
		return __('Search results pagination', 'webentwicklerin');
	}

	if (is_post_type_archive()) {
		return __('Archive pagination', 'webentwicklerin');
	}

	if (is_archive()) {
		return __('Archive pagination', 'webentwicklerin');
	}

	return __('Pagination', 'webentwicklerin');
}

/**
 * Add an aria-label to the first link in rendered block markup.
 *
 * @since 2.0.0
 *
 * @param string $block_content Rendered block HTML.
 * @param string $label         Accessible label text.
 * @return string
 */
function webentwicklerin_add_aria_label_to_first_link($block_content, $label)
{
	if (false !== stripos($block_content, 'aria-label=')) {
		return $block_content;
	}

	return preg_replace(
		'/<a\b(?![^>]*\baria-label=)([^>]*)>/i',
		'<a$1 aria-label="' . esc_attr($label) . '">',
		$block_content,
		1
	);
}

/**
 * Improve accessibility labels for Query Loop pagination links.
 *
 * Keeps visible pagination unchanged and adds contextual aria-labels only.
 *
 * @since 2.0.0
 */
add_filter(
	'render_block_core/query-pagination-previous',
	function ($block_content) {
		$context = webentwicklerin_get_pagination_context_label();

		return webentwicklerin_add_aria_label_to_first_link(
			$block_content,
			sprintf(
				/* translators: %s: Pagination context, e.g. Blog pagination. */
				__('%s, previous page', 'webentwicklerin'),
				$context
			)
		);
	},
	10,
	1
);

add_filter(
	'render_block_core/query-pagination-next',
	function ($block_content) {
		$context = webentwicklerin_get_pagination_context_label();

		return webentwicklerin_add_aria_label_to_first_link(
			$block_content,
			sprintf(
				/* translators: %s: Pagination context, e.g. Blog pagination. */
				__('%s, next page', 'webentwicklerin'),
				$context
			)
		);
	},
	10,
	1
);

add_filter(
	'render_block_core/query-pagination-numbers',
	function ($block_content) {
		$context = webentwicklerin_get_pagination_context_label();

		return preg_replace_callback(
			'/<a\b([^>]*\bclass=(["\'])[^"\']*\bpage-numbers\b[^"\']*\2[^>]*)>(\d+)<\/a>/i',
			function ($matches) use ($context) {
				$attributes = $matches[1];
				$page_number = $matches[3];

				if (false !== stripos($attributes, 'aria-label=')) {
					return $matches[0];
				}

				return sprintf(
					'<a%s aria-label="%s">%s</a>',
					$attributes,
					esc_attr(
						sprintf(
							/* translators: 1: Pagination context, 2: Page number. */
							__('%1$s, page %2$s', 'webentwicklerin'),
							$context,
							$page_number
						)
					),
					$page_number
				);
			},
			$block_content
		);
	},
	10,
	1
);
