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
<<<<<<< HEAD
// hooks

// remove the_block_template_skip_link to validate w3c
remove_action('wp_footer', 'the_block_template_skip_link');


define('THEME_VERSION', wp_get_theme()->get('Version'));
define('THEME_NAME', 'webentwicklerin');
=======


define('THEME_VERSION', wp_get_theme()->get('Version'));
define('THEME_NAME', 'aspexion');
>>>>>>> 3a47a477aeb1151ea1a0714ac89098cdd0bcae86

/**
 * Add theme support for block styles and editor style.
 *
 * @since 1.0.0
 *
 * @return void
 */

add_action('after_setup_theme', function () {
    load_theme_textdomain(THEME_NAME, get_template_directory() . '/languages');
    remove_theme_support('core-block-patterns');
});


add_action('wp_body_open', function () {
    printf('<span id="topofpage" class="screen-reader-text">%s</span>', __('Anchor link to top of page', THEME_NAME));
});


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'frontend-style',
        get_theme_file_uri('assets/css/style.min.css'),
        [],
        THEME_VERSION
    );

    wp_enqueue_script(
        'webethm-init',
        get_stylesheet_directory_uri() . '/assets/js/scripts-init.js',
        [],
        THEME_VERSION,
        true
    );
});


add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style(
        'admin-style',
        get_theme_file_uri('assets/css/admin-style.css'),
        [],
        THEME_VERSION
    );
});


add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'editor-style',
        get_theme_file_uri('assets/css/editor-style.min.css'),
        [],
        THEME_VERSION
    );
});


add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }

    $wp_admin_bar->add_node(array(
        'parent'    => 'site-name',
        'id'        => 'plugins',
        'title'     => 'Plugins',
        'href'      => admin_url('plugins.php'),
    ));
}, 100);


add_action('init', 'webthm_block_styles');
function webthm_block_styles()
{

    /**
     * The wp_enqueue_block_style() function allows us to enqueue a stylesheet
     * for a specific block. These will only get loaded when the block is rendered
     * (both in the editor and on the front end), improving performance
     * and reducing the amount of data requested by visitors.
     *
     * See https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/ for more info.
     */

    register_block_style(
        'core/list',
        array(
            'name'         => 'colored-bullets',
<<<<<<< HEAD
            'label'        => __('Colored Bullets', 'webentwicklerin'),
=======
            'label'        => __('Colored Bullets', 'aspexion'),
>>>>>>> 3a47a477aeb1151ea1a0714ac89098cdd0bcae86

            'inline_style' => '
            ul.is-style-colored-bullets {
                list-style: none;
            }

            ul.is-style-colored-bullets > li::before {
                content: "\2022";
                color: var(--wp--preset--color--accent);
                font-weight: bold;
                display: inline-block;
                width: 0.75em;
                margin-left: -0.75em;
            }
            ul.is-style-colored-bullets ul li::before {
                content: "\29BF";
                color: var(--wp--preset--color--accent);
                font-weight: bold;
                display: inline-block;
                width: 0.75em;
                margin-left: -0.75em;
            }
            ul.is-style-colored-bullets > li > div,
            ul.is-style-colored-bullets ul li > div{
                display: inline-block;
            }',
        )
    );
}


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

        set_transient($lib, $iconrry, 1 * HOUR_IN_SECONDS);
        return get_transient($lib);
    }

    return $icons;
}
add_action('init', 'webethm_icon_library');

add_action('wp_footer', function () {
    $icons = webethm_icon_library();
    $html = sprintf('<div id="gototop" class="animated hidden"><a class="icon-arrow-up" href="#topofpage"><span class="screen-reader-text">%s</span>%s</a></div>', __('Go to top of page', THEME_NAME), htmlspecialchars_decode($icons['arrow-up']));
    echo $html;
});
<<<<<<< HEAD

if (defined('SMTP_server')) {
    add_action('phpmailer_init', 'mx_phpmailer_smtp');
    function mx_phpmailer_smtp($phpmailer)
    {
        $phpmailer->isSMTP();
        $phpmailer->Host = SMTP_server;
        $phpmailer->SMTPAuth = SMTP_AUTH;
        $phpmailer->Port = SMTP_PORT;
        $phpmailer->Username = SMTP_username;
        $phpmailer->Password = SMTP_password;
        $phpmailer->SMTPSecure = SMTP_SECURE;
        $phpmailer->From = SMTP_FROM;
        $phpmailer->FromName = SMTP_NAME;
    }
}

add_filter('wp_img_tag_add_loading_attr', function ($value, $image) {
    if (false !== strpos($image, 'wp-post-image')) {
        return false;
    }
    return true;
}, 10, 2);
=======


add_filter('wp_img_tag_add_loading_attr', function ($value, $image) {
    if (false !== strpos($image, 'wp-post-image')) {
        return false;
    }
    return true;
}, 10, 2);


function specs_number_format_i18n($number, $decimals = 0)
{
    global $wp_locale;

    if (!is_numeric($number)) $formatted = $number;
    else {
        if (isset($wp_locale)) {
            $formatted = number_format($number, absint($decimals), $wp_locale->number_format['decimal_point'], $wp_locale->number_format['thousands_sep']);
            //    $formatted = number_format( $number, absint( $decimals ), $wp_locale->number_format['decimal_point'], '' );
        } else {
            $formatted = number_format($number, absint($decimals));
        }
    }
    /**
     * Filters the number formatted based on the locale.
     *
     * @since 2.8.0
     * @since 4.9.0 The `$number` and `$decimals` parameters were added.
     *
     * @param string $formatted Converted number in string format.
     * @param float  $number    The number to convert based on locale.
     * @param int    $decimals  Precision of the number of decimal places.
     */
    return apply_filters('specs_number_format_i18n', $formatted, $number, $decimals);
}


function mx_login_stylesheet()
{
    wp_enqueue_style('custom-login', get_template_directory_uri() . '/assets/css/style-login.css');
}
add_action('login_enqueue_scripts', 'mx_login_stylesheet');


// add_action('phpmailer_init', 'mx_phpmailer_smtp');
function mx_phpmailer_smtp($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host = SMTP_server;
    $phpmailer->SMTPAuth = SMTP_AUTH;
    $phpmailer->Port = SMTP_PORT;
    $phpmailer->Username = SMTP_username;
    $phpmailer->Password = SMTP_password;
    $phpmailer->SMTPSecure = SMTP_SECURE;
    $phpmailer->From = SMTP_FROM;
    $phpmailer->FromName = SMTP_NAME;
}
>>>>>>> 3a47a477aeb1151ea1a0714ac89098cdd0bcae86
