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





/** Navi mobile icon ersetzen */
add_filter('render_block_core/navigation', function ($content, $parsed_block, $block) {
	$atts = $block->attributes;
	$icons = webethm_icon_library();
	$icon = strtolower($atts['icon']);
	if (!is_array($icons)) return $content;
	if (is_array($icons) && !array_key_exists($icon, $icons)) return $content;
	if ($icon  == 'menu') {
		//	$default ='<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 5v1.5h14V5H5zm0 7.8h14v-1.5H5v1.5zM5 19h14v-1.5H5V19z" /></svg>';
		return preg_replace('/\<svg width(.*?)\<\/svg\>/', htmlspecialchars_decode($icons[$icon]), $content);
	}
	return $content;
}, 10, 3);
