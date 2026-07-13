<?php

/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package webentwicklerin
 * @since 1.0.0
 */

/**
 * The theme version.
 *
 * @since 1.0.0
 */
// Filters.
require_once get_theme_file_path('inc/filters.php');
// Mobile viewport meta (sticky/fixed layout on iOS).
require_once get_theme_file_path('inc/viewport.php');
// Block styles
require_once get_theme_file_path('inc/block-styles.php');
// Blur backdrop block style helpers (featured image, image, gallery, cover).
require_once get_theme_file_path('inc/blur-backdrop.php');
// Query Loop block style helpers (random order).
require_once get_theme_file_path('inc/query-loop.php');
// Update menu links on permalink changes
require_once get_theme_file_path('inc/update-menu-links.php');
// Theme update checker
require_once get_theme_file_path('inc/theme-updater.php');
// Force font-display: swap for Font Library fonts (user Theme JSON layer only)
require_once get_theme_file_path('inc/font-display-swap.php');
// Preload theme-bundled fonts resolved from theme.json (not Font Library fonts).
require_once get_theme_file_path('inc/font-preload.php');
// Accessibility helpers and labels.
require_once get_theme_file_path('inc/a11y.php');
// Theme pattern categories (patterns live in /patterns).
require_once get_theme_file_path('inc/register-patterns.php');

/**
 * Theme setup: i18n and pattern support.
 *
 * @since 1.0.0
 *
 * @return void
 */
add_action('init', function () {
    load_theme_textdomain('webentwicklerin', get_template_directory() . '/languages');
    remove_theme_support('core-block-patterns');
});


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'frontend-style',
        get_theme_file_uri('assets/css/style.min.css'),
        [],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'webentwicklerin-init',
        get_theme_file_uri('assets/js/theme-scripts.min.js'),
        [],
        wp_get_theme()->get('Version'),
        true
    );
});


/**
 * Remove Dashicons on the front end for guests to reduce render-blocking CSS.
 * Logged-in users keep Dashicons (e.g. admin bar). Re-enable if icons break.
 *
 * @since 2.0.0
 */
add_action(
    'wp_enqueue_scripts',
    function () {
        if (is_user_logged_in()) {
            return;
        }
        wp_dequeue_style('dashicons');
    },
    100
);


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


add_filter('wp_img_tag_add_loading_attr', function ($value, $image) {
    if (false !== strpos($image, 'wp-post-image')) {
        return false;
    }
    return true;
}, 10, 2);
