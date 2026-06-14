<?php

/**
 * Register block pattern categories.
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_pattern_category/
 *
 * @package webentwicklerin
 * @since   1.0.0
 */

/**
 * Register theme block pattern categories on init.
 *
 * Categories must be registered before patterns reference them in their
 * Categories header. Theme patterns from /patterns are registered at
 * init priority 10, so this runs one step earlier.
 *
 * @since 1.0.0
 *
 * @return void
 */
function webentwicklerin_register_block_pattern_categories() {
	register_block_pattern_category(
		'webentwicklerin',
		array(
			'label' => __( 'Webentwicklerin', 'webentwicklerin' ),
		)
	);
}
add_action( 'init', 'webentwicklerin_register_block_pattern_categories', 9 );
