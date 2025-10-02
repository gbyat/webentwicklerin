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
	// Validate block attributes
	if (!isset($block->attributes) || !isset($block->attributes['icon'])) {
		return $content;
	}

	$atts = $block->attributes;
	$icons = webethm_icon_library();

	if (!is_array($icons)) {
		return $content;
	}

	$icon = sanitize_key(strtolower($atts['icon']));

	if (!array_key_exists($icon, $icons)) {
		return $content;
	}

	if ($icon === 'menu') {
		$allowed_svg_tags = array(
			'svg' => array(
				'class' => true,
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'role' => true,
				'xmlns' => true,
				'xmlns:xlink' => true,
				'width' => true,
				'height' => true,
				'viewbox' => true,
				'id' => true,
				'version' => true,
				'xml:space' => true,
				'preserveaspectratio' => true,
			),
			'g' => array('fill' => true, 'transform' => true),
			'title' => array('title' => true),
			'path' => array(
				'd' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			),
			'polygon' => array('points' => true, 'fill' => true, 'stroke' => true),
			'polyline' => array('points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true),
			'circle' => array('cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true),
			'rect' => array('x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true, 'stroke' => true),
			'line' => array('x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true),
			'ellipse' => array('cx' => true, 'cy' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true),
		);

		return preg_replace(
			'/\<svg width(.*?)\<\/svg\>/',
			wp_kses($icons[$icon], $allowed_svg_tags),
			$content
		);
	}

	return $content;
}, 10, 3);
