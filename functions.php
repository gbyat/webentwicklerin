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
// Theme update checker
require_once get_theme_file_path('inc/theme-updater.php');
// Navigation breakpoint control
require_once get_theme_file_path('inc/navigation-breakpoint.php');
// Force font-display: swap for Font Library fonts (user Theme JSON layer only)
require_once get_theme_file_path('inc/font-display-swap.php');
// Accessibility helpers and labels.
require_once get_theme_file_path('inc/a11y.php');

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
        'webethm-init',
        get_theme_file_uri('assets/js/theme-scripts.min.js'),
        [],
        wp_get_theme()->get('Version'),
        true
    );

    if (!is_admin()) {
        wp_dequeue_script('jquery');
        wp_deregister_script('jquery');
    }
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

    wp_enqueue_script(
        'webentwicklerin-navigation-breakpoint-control',
        get_theme_file_uri('assets/js/navigation-breakpoint-control.js'),
        ['wp-blocks', 'wp-block-editor', 'wp-components', 'wp-compose', 'wp-element', 'wp-hooks', 'wp-i18n'],
        wp_get_theme()->get('Version'),
        true
    );

    wp_set_script_translations(
        'webentwicklerin-navigation-breakpoint-control',
        'webentwicklerin',
        get_theme_file_path('languages')
    );
});


add_filter('wp_img_tag_add_loading_attr', function ($value, $image) {
    if (false !== strpos($image, 'wp-post-image')) {
        return false;
    }
    return true;
}, 10, 2);
