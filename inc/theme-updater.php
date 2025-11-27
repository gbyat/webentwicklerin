<?php

/**
 * Theme Update Checker
 *
 * Checks GitHub Releases for theme updates and displays them in WordPress Dashboard.
 *
 * @package webethm
 * @since 1.0.0
 */

namespace Webethm\ThemeUpdater;

/**
 * GitHub repository information
 */
const GITHUB_USER = 'gbyat';
const GITHUB_REPO = 'webentwicklerin';
const GITHUB_API_URL = 'https://api.github.com/repos/' . GITHUB_USER . '/' . GITHUB_REPO . '/releases/latest';

/**
 * Check for theme updates from GitHub
 *
 * @return array|false Update information or false if no update available
 */
function check_for_updates()
{
    $current_version = wp_get_theme()->get('Version');
    $transient_key = 'webentwicklerin_update_check';
    $cache_time = 12 * \HOUR_IN_SECONDS; // Cache for 12 hours

    // Check cache first
    $cached = get_transient($transient_key);
    if (false !== $cached) {
        if (version_compare($cached['version'], $current_version, '>')) {
            return $cached;
        }
        return false;
    }

    // Fetch latest release from GitHub
    $response = wp_remote_get(
        GITHUB_API_URL,
        array(
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress-Theme-Update-Checker',
            ),
            'timeout' => 10,
        )
    );

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $release = json_decode($body, true);

    if (!isset($release['tag_name']) || !isset($release['zipball_url'])) {
        return false;
    }

    // Extract version from tag (remove 'v' prefix if present)
    $latest_version = ltrim($release['tag_name'], 'v');

    // Try to find the release ZIP asset, fallback to GitHub archive ZIP
    $package_url = $release['zipball_url']; // Default: GitHub source archive

    // Check if there's a release ZIP asset (created by GitHub Actions)
    // Look for webentwicklerin.zip (without version number)
    if (isset($release['assets']) && is_array($release['assets'])) {
        foreach ($release['assets'] as $asset) {
            if (isset($asset['name']) && $asset['name'] === 'webentwicklerin.zip') {
                $package_url = $asset['browser_download_url'];
                break;
            }
        }
    }

    // Prepare update info
    $update_info = array(
        'version' => $latest_version,
        'package' => $package_url,
        'url' => $release['html_url'],
        'name' => $release['name'] ?? $release['tag_name'],
        'body' => $release['body'] ?? '',
        'published_at' => $release['published_at'] ?? '',
    );

    // Cache the result
    set_transient($transient_key, $update_info, $cache_time);

    // Check if update is available
    if (version_compare($latest_version, $current_version, '>')) {
        return $update_info;
    }

    return false;
}

/**
 * Add update check to theme transients
 *
 * @param object $transients Theme update transients
 * @return object Modified transients
 */
function theme_update_transient($transients)
{
    $update = check_for_updates();
    if ($update) {
        $theme_slug = get_template();
        if (!isset($transients->response)) {
            $transients->response = array();
        }
        $transients->response[$theme_slug] = array(
            'theme' => $theme_slug,
            'new_version' => $update['version'],
            'url' => $update['url'],
            'package' => $update['package'],
        );
    }
    return $transients;
}
add_filter('pre_set_site_transient_update_themes', __NAMESPACE__ . '\theme_update_transient');

/**
 * Add update information to theme details
 *
 * @param array $response Theme update response
 * @param string $theme Theme slug
 * @param array $theme_data Theme data
 * @return array Modified response
 */
function theme_update_details($response, $theme, $theme_data)
{
    if (get_template() !== $theme) {
        return $response;
    }

    $update = check_for_updates();
    if ($update) {
        $response['name'] = $theme_data['Name'];
        $response['slug'] = $theme;
        $response['version'] = $update['version'];
        $response['author'] = $theme_data['Author'];
        $response['homepage'] = $theme_data['ThemeURI'];
        $response['download_link'] = $update['package'];
        $response['sections']['changelog'] = wp_kses_post($update['body']);
        $response['banners'] = array();
        $response['icons'] = array();
    }

    return $response;
}
add_filter('themes_api', __NAMESPACE__ . '\theme_update_details', 10, 3);

/**
 * Display update notification in admin
 */
function admin_update_notice()
{
    if (!current_user_can('update_themes')) {
        return;
    }

    $update = check_for_updates();
    if (!$update) {
        return;
    }

    $current_version = wp_get_theme()->get('Version');
    $update_url = admin_url('themes.php');

    printf(
        '<div class="notice notice-warning is-dismissible"><p><strong>%s</strong> %s <a href="%s">%s</a></p></div>',
        esc_html__('Theme Update Available', 'webentwicklerin'),
        sprintf(
            esc_html__('Version %s is available. You are currently running version %s.', 'webentwicklerin'),
            esc_html($update['version']),
            esc_html($current_version)
        ),
        esc_url($update_url),
        esc_html__('Update now', 'webentwicklerin')
    );
}
add_action('admin_notices', __NAMESPACE__ . '\admin_update_notice');
