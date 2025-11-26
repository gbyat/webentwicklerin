<?php

/**
 * Update Menu Links on Permalink Changes
 *
 * Automatically updates navigation menu links when post/page permalinks change
 *
 * @package webentwicklerin
 * @since 2.0.0
 * @author webentwicklerin, Gabriele Laesser
 * @author URI https://webentwicklerin.at
 */

namespace Webentwicklerin\Theme\MenuLinks;

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Update navigation menu links when a post's permalink changes
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post_after Post object after update.
 * @param WP_Post $post_before Post object before update.
 */
function update_menu_links_on_permalink_change($post_id, $post_after, $post_before)
{
    // Only process published posts/pages
    if ($post_after->post_status !== 'publish') {
        return;
    }

    // Get old and new permalinks
    $old_url = get_permalink($post_before);
    $new_url = get_permalink($post_after);

    // If permalink hasn't changed, nothing to do
    if ($old_url === $new_url) {
        return;
    }

    // Update classic navigation menus
    update_classic_menu_links($old_url, $new_url);

    // Update block-based navigation menus
    update_block_navigation_links($old_url, $new_url);
}
add_action('post_updated', __NAMESPACE__ . '\update_menu_links_on_permalink_change', 10, 3);

/**
 * Update links in classic WordPress menus
 *
 * @param string $old_url The old URL to find.
 * @param string $new_url The new URL to replace with.
 */
function update_classic_menu_links($old_url, $new_url)
{
    // Get all menu items
    $menu_items = get_posts(array(
        'post_type'      => 'nav_menu_item',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));

    if (empty($menu_items)) {
        return;
    }

    $updated_count = 0;

    foreach ($menu_items as $menu_item) {
        $menu_item_url = get_post_meta($menu_item->ID, '_menu_item_url', true);

        // Check if this menu item uses the old URL
        if ($menu_item_url === $old_url) {
            update_post_meta($menu_item->ID, '_menu_item_url', $new_url);
            $updated_count++;
        }
    }

    if ($updated_count > 0) {
        error_log(sprintf(
            'Updated %d classic menu link(s) from %s to %s',
            $updated_count,
            $old_url,
            $new_url
        ));
    }
}

/**
 * Update links in block-based navigation menus
 *
 * @param string $old_url The old URL to find.
 * @param string $new_url The new URL to replace with.
 */
function update_block_navigation_links($old_url, $new_url)
{
    // Get all navigation blocks
    $navigation_posts = get_posts(array(
        'post_type'      => 'wp_navigation',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));

    if (empty($navigation_posts)) {
        return;
    }

    $updated_count = 0;

    foreach ($navigation_posts as $nav_post) {
        $content = $nav_post->post_content;
        $original_content = $content;

        // Replace the old URL with the new URL in the block content
        // This handles both the url attribute and any href attributes
        $content = str_replace(
            array(
                '"url":"' . esc_url($old_url) . '"',
                '"href":"' . esc_url($old_url) . '"',
                'href="' . esc_attr($old_url) . '"',
            ),
            array(
                '"url":"' . esc_url($new_url) . '"',
                '"href":"' . esc_url($new_url) . '"',
                'href="' . esc_attr($new_url) . '"',
            ),
            $content
        );

        // If content changed, update the post
        if ($content !== $original_content) {
            wp_update_post(array(
                'ID'           => $nav_post->ID,
                'post_content' => $content,
            ));
            $updated_count++;
        }
    }

    if ($updated_count > 0) {
        error_log(sprintf(
            'Updated %d block navigation menu(s) from %s to %s',
            $updated_count,
            $old_url,
            $new_url
        ));
    }
}

/**
 * Update menu links when a post is trashed (to handle parent changes)
 * This catches cases where moving a page changes its URL structure
 *
 * @param int $post_id Post ID.
 */
function update_menu_links_on_parent_change($post_id)
{
    $post = get_post($post_id);

    if (! $post || $post->post_status !== 'publish') {
        return;
    }

    // Get the old permalink from cache/transient if available
    $old_url = get_transient('permalink_before_update_' . $post_id);

    if ($old_url) {
        $new_url = get_permalink($post_id);

        if ($old_url !== $new_url) {
            update_classic_menu_links($old_url, $new_url);
            update_block_navigation_links($old_url, $new_url);
        }

        delete_transient('permalink_before_update_' . $post_id);
    }
}
add_action('save_post', __NAMESPACE__ . '\update_menu_links_on_parent_change', 20);

/**
 * Store the current permalink before a post is updated
 * This allows us to compare old vs new permalink
 *
 * @param int $post_id Post ID.
 */
function store_permalink_before_update($post_id)
{
    $post = get_post($post_id);

    if (! $post || ! in_array($post->post_type, array('post', 'page'), true)) {
        return;
    }

    $current_url = get_permalink($post_id);
    set_transient('permalink_before_update_' . $post_id, $current_url, \HOUR_IN_SECONDS);
}
add_action('pre_post_update', __NAMESPACE__ . '\store_permalink_before_update');
