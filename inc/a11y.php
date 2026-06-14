<?php

/**
 * Accessibility helpers and filters.
 *
 * @package webentwicklerin
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
 * Ensure the first <main> is a working skip-link target on every template.
 *
 * Templates edited in the Site Editor or without an anchor attribute may omit
 * id="site-content"; the skip link would then not reach main content.
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
		static $main_target_set = false;

		if ($main_target_set) {
			return $block_content;
		}

		if ('core/group' !== ($block['blockName'] ?? '')) {
			return $block_content;
		}

		if (empty($block['attrs']['tagName']) || 'main' !== $block['attrs']['tagName']) {
			return $block_content;
		}

		$main_target_set = true;

		if (! preg_match('/<main[^>]*\bid=["\']site-content["\']/i', $block_content)) {
			if (preg_match('/<main[^>]*\bid=/i', $block_content)) {
				$block_content = preg_replace(
					'/(<main[^>]*\s)id=(["\'])[^"\']*\2/i',
					'$1id=$2site-content$2',
					$block_content,
					1
				);
			} else {
				$block_content = preg_replace('/<main\b/i', '<main id="site-content"', $block_content, 1);
			}
		}

		if (false === stripos($block_content, 'tabindex=')) {
			$block_content = preg_replace(
				'/<main\b([^>]*)>/i',
				'<main$1 tabindex="-1">',
				$block_content,
				1
			);
		}

		return $block_content;
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

/**
 * Add an accessible name to scroll-to-top buttons on the front end.
 *
 * core/button does not support ariaLabel in saved markup; adding it in the
 * pattern breaks block validation in the editor.
 *
 * @since 2.0.0
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
add_filter(
	'render_block_core/button',
	function ($block_content, $block) {
		if ( empty( $block['attrs']['url'] ) || '#topofpage' !== $block['attrs']['url'] ) {
			return $block_content;
		}

		if ( false !== stripos( $block_content, 'aria-label=' ) ) {
			return $block_content;
		}

		return preg_replace(
			'/(<a\b)/',
			'$1 aria-label="' . esc_attr__( 'Go to top of page', 'webentwicklerin' ) . '"',
			$block_content,
			1
		);
	},
	10,
	2
);
