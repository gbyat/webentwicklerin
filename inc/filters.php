<?php

/**
 * Filters
 *
 * @package webethm
 * @since 1.0.0
 */

/**
 * Show '(No title)' if a post has no title.
 *
 * @since 1.0.0
 */
add_filter(
	'the_title',
	function ($title) {
		if (! is_admin() && empty($title)) {
			$title = _x('(No title)', 'Used if post or pages has no title', 'webentwicklerin');
		}

		return $title;
	}
);

/**
 * Replace the default [...] excerpt more with an elipsis.
 *
 * @since 1.0.0
 */
add_filter(
	'excerpt_more',
	function ($more) {
		return '&hellip;';
	}
);


add_filter('do_redirect_guess_404_permalink', '__return_false');
