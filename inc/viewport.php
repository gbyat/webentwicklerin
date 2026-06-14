<?php

/**
 * Custom viewport meta tag for mobile browsers.
 *
 * Block themes output a minimal viewport tag by default. Using
 * height=device-height reduces layout jumps on iOS when the address bar
 * shows or hides, which affects fixed and sticky elements.
 *
 * Note: minimum-scale=1 limits pinch-zoom; keep only if that trade-off is acceptable.
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

add_action( 'after_setup_theme', 'webentwicklerin_remove_default_viewport_meta', 20 );

/**
 * Remove the core block-theme viewport meta tag.
 *
 * @since 2.0.0
 *
 * @return void
 */
function webentwicklerin_remove_default_viewport_meta() {
	remove_action( 'wp_head', '_block_template_viewport_meta_tag', 0 );
}

add_action( 'wp_head', 'webentwicklerin_viewport_meta_tag', 0 );

/**
 * Output a viewport meta tag suited to fixed/sticky layout on mobile.
 *
 * @since 2.0.0
 *
 * @return void
 */
function webentwicklerin_viewport_meta_tag() {
	echo '<meta name="viewport" content="height=device-height, width=device-width, initial-scale=1, minimum-scale=1">' . "\n";
}
