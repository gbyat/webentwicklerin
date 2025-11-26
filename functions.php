<?php

/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package webethm
 * @since 1.0.0
 */

/**
 * The theme version.
 *
 * @since 1.0.0
 */
// Filters.
require_once get_theme_file_path('inc/filters.php');
// Block styles
require_once get_theme_file_path('inc/block-styles.php');
// Update menu links on permalink changes
require_once get_theme_file_path('inc/update-menu-links.php');

// remove the_block_template_skip_link to validate w3c
remove_action('wp_footer', 'the_block_template_skip_link');


/**
 * Add theme support for block styles and editor style.
 *
 * @since 1.0.0
 *
 * @return void
 */

add_action('after_setup_theme', function () {
    load_theme_textdomain('webentwicklerin', get_template_directory() . '/languages');
    remove_theme_support('core-block-patterns');
});


add_action('wp_body_open', function () {
    echo sprintf(wp_kses_post('<span id="topofpage" class="screen-reader-text">%s</span>'), esc_html__('Anchor link to top of page', 'webentwicklerin'));
});


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'frontend-style',
        get_theme_file_uri('assets/css/style.min.css'),
        [],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'webethm-init',
        get_theme_file_uri('assets/js/theme-scripts.min.js'),
        [],
        wp_get_theme()->get('Version'),
        true
    );

    // Dequeue jQuery if not needed (improve performance)
    if (!is_admin() && !is_customize_preview()) {
        wp_dequeue_script('jquery');
        wp_deregister_script('jquery');
    }
});


add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style(
        'admin-style',
        get_theme_file_uri('assets/css/admin-style.css'),
        [],
        wp_get_theme()->get('Version')
    );
});


add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'editor-style',
        get_theme_file_uri('assets/css/editor-style.min.css'),
        [],
        wp_get_theme()->get('Version')
    );
});


/**
 * Simple "Go to top" link in the footer using a UTF-8 arrow.
 *
 * @since 2.0.0
 */
add_action('wp_footer', function () {
    printf(
        '<div id="gototop" class="animated hidden"><a href="#topofpage" class="gototop-link" aria-label="%s">%s</a></div>',
        esc_attr__('Go to top of page', 'webentwicklerin'),
        '&#8593;'
    );
});


add_filter('wp_img_tag_add_loading_attr', function ($value, $image) {
    if (false !== strpos($image, 'wp-post-image')) {
        return false;
    }
    return true;
}, 10, 2);
