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
// block styles
require_once get_theme_file_path('inc/block-styles.php');


// Debugging-Funktionen nur laden wenn WP_DEBUG und WP_DEBUG_LOG aktiv sind
if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
    $debug_file = get_template_directory() . '/tmp/debugging.php';
    if (file_exists($debug_file)) {
        require_once $debug_file;
    }
}

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
    printf('<span id="topofpage" class="screen-reader-text" aria-label="%s"></span>', __('Anchor link to top of page', 'webentwicklerin'));
});


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'frontend-style',
        get_theme_file_uri('assets/css/style.min.css'),
        [],
        ''
    );

    wp_enqueue_script(
        'webethm-init',
        get_theme_file_uri('assets/js/theme-scripts.min.js'),
        [],
        '',
        true
    );
});


add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style(
        'admin-style',
        get_theme_file_uri('assets/css/admin-style.css'),
        [],
        ''
    );
});


add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'editor-style',
        get_theme_file_uri('assets/css/editor-style.min.css'),
        [],
        ''
    );
});


/** svg files saeubern - für verwendung als inline SVG vorbereiten */
function webethm_svg_inline_replacer($service)
{
    $path = get_template_directory() . '/assets/svg';
    $svg = file_get_contents(trailingslashit($path) . $service . '.svg');
    $svg = preg_replace('/<\?xml.*\?>/', '', $svg);
    $svg = preg_replace('/<\!DOCTYPE[^>]*>/', '', $svg);
    $svg = preg_replace('/<title[^>]*>([\s\S]*?)<\/title[^>]*>/i', '', $svg);
    $svg = preg_replace('/<!--(.|\s)*?-->/i', '', $svg);
    $svg = preg_replace('/<desc[^>]*>([\s\S]*?)<\/desc[^>]*>/i', '', $svg);
    return trim($svg);
}

/** svg files auslesen und in library fuer inline-SVGs speichern */
function webethm_icon_library()
{

    $path = get_template_directory() . '/assets/svg';
    $lib = 'webethm_icon_library';
    $icons = get_transient($lib);

    if (!is_array($icons) || false == $icons) {
        $iconrry = [];
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        $url = wp_nonce_url('plugins.php');
        if (false === ($creds = request_filesystem_credentials($url, '', false, false, null))) {
            return ('error'); // stop processing here
        }
        if (WP_Filesystem($creds)) {
            global $wp_filesystem;
            if (!is_dir($path)) return;
            $dh  = opendir($path);
            if (!$dh) return;
            while (false !== ($filename = readdir($dh))) {
                if (strpos($filename, '.svg')) {
                    $service = str_replace('.svg', '', $filename);
                    $iconrry[$service] = htmlspecialchars(webethm_svg_inline_replacer($service));
                }
            }
        }

        set_transient($lib, $iconrry, 6 * HOUR_IN_SECONDS);
        return get_transient($lib);
    }

    return $icons;
}
add_action('init', 'webethm_icon_library');

add_action('wp_footer', function () {
    $icons = webethm_icon_library();
    $html = sprintf('<div id="gototop" class="animated hidden"><a class="icon-arrow-up" href="#topofpage" aria-label="%s">%s</a></div>', __('Go to top of page', 'webentwicklerin'), htmlspecialchars_decode($icons['arrow-up']));
    echo $html;
});


add_filter('wp_img_tag_add_loading_attr', function ($value, $image) {
    if (false !== strpos($image, 'wp-post-image')) {
        return false;
    }
    return true;
}, 10, 2);
