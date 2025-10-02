<?php

/**
 * Register Custom Blocks
 *
 * @package webentwicklerin
 * @since 2.0.0
 */

/**
 * Auto-register all blocks in the blocks directory
 */
function webethm_register_blocks()
{
    $blocks_dir = get_template_directory() . '/blocks';

    if (!is_dir($blocks_dir)) {
        return;
    }

    $block_folders = glob($blocks_dir . '/*', GLOB_ONLYDIR);

    foreach ($block_folders as $block_folder) {
        $block_json = $block_folder . '/block.json';

        if (file_exists($block_json)) {
            register_block_type($block_folder);
        }
    }
}
add_action('init', 'webethm_register_blocks');

/**
 * Enqueue block editor assets
 */
function webethm_enqueue_block_editor_assets()
{
    // Icons are already enqueued via inc/icons.php
    // Additional editor scripts can be added here
}
add_action('enqueue_block_editor_assets', 'webethm_enqueue_block_editor_assets');
