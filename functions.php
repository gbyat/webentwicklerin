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
// Generated icons
require_once get_theme_file_path('inc/icons.php');
// Register custom blocks
require_once get_theme_file_path('inc/register-blocks.php');

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
    echo sprintf(wp_kses_post('<span id="topofpage" class="screen-reader-text" aria-label="%s"></span>'), esc_html__('Anchor link to top of page', 'webentwicklerin'));
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


/** svg files saeubern - für verwendung als inline SVG vorbereiten */
function webethm_svg_inline_replacer($service)
{
    // Sanitize filename to prevent path traversal
    $service = sanitize_file_name($service);
    $service = basename($service, '.svg');

    $path = get_template_directory() . '/assets/svg';
    $filepath = trailingslashit($path) . $service . '.svg';

    // Verify the file is within the allowed directory
    if (strpos(realpath($filepath), realpath($path)) !== 0) {
        return '';
    }

    if (!file_exists($filepath)) {
        return '';
    }

    $svg = file_get_contents($filepath);
    $svg = preg_replace('/<\?xml.*\?>/', '', $svg);
    $svg = preg_replace('/<\!DOCTYPE[^>]*>/', '', $svg);
    $svg = preg_replace('/<title[^>]*>([\s\S]*?)<\/title[^>]*>/i', '', $svg);
    $svg = preg_replace('/<!--(.|\s)*?-->/i', '', $svg);
    $svg = preg_replace('/<desc[^>]*>([\s\S]*?)<\/desc[^>]*>/i', '', $svg);
    return trim($svg);
}

/** 
 * Get icon library from generated icons.php
 * (Backwards compatible wrapper)
 */
function webethm_icon_library()
{
    if (function_exists('webethm_get_icons')) {
        return webethm_get_icons();
    }
    return [];
}

add_action('wp_footer', function () {
    $icons = webethm_icon_library();

    if (!is_array($icons) || !isset($icons['arrow-up'])) {
        return;
    }

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

    printf(
        '<div id="gototop" class="animated hidden"><a class="icon-arrow-up" href="#topofpage" aria-label="%s">%s</a></div>',
        esc_attr__('Go to top of page', 'webentwicklerin'),
        wp_kses($icons['arrow-up'], $allowed_svg_tags)
    );
});


add_filter('wp_img_tag_add_loading_attr', function ($value, $image) {
    if (false !== strpos($image, 'wp-post-image')) {
        return false;
    }
    return true;
}, 10, 2);
