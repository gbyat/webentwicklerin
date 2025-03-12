<?php

/**
 * Hier sind einige effektive Methoden, um Plugin-Konflikte schneller zu identifizieren:
 */

add_filter('template_include', function ($template) {
    if (is_user_logged_in() && current_user_can('manage_options')) {
        $debug_info = array(
            'Final Template' => $template,
            'Template Hierarchy' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
            'Active Filters' => array(
                'template_include' => has_filter('template_include'),
                'archive_template' => has_filter('archive_template'),
                'category_template' => has_filter('category_template'),
                'tag_template' => has_filter('tag_template')
            ),
            'Active Plugins Filters' => array()
        );

        // Überprüfe Plugin-Filter
        global $wp_filter;
        $template_filters = array('template_include', 'archive_template', 'category_template', 'tag_template');
        foreach ($template_filters as $filter) {
            if (isset($wp_filter[$filter])) {
                foreach ($wp_filter[$filter] as $priority => $callbacks) {
                    foreach ($callbacks as $callback) {
                        if (is_array($callback['function'])) {
                            if (is_object($callback['function'][0])) {
                                $debug_info['Active Plugins Filters'][$filter][] = get_class($callback['function'][0]) . '->' . $callback['function'][1];
                            } else {
                                $debug_info['Active Plugins Filters'][$filter][] = $callback['function'][0] . '::' . $callback['function'][1];
                            }
                        } else {
                            $debug_info['Active Plugins Filters'][$filter][] = $callback['function'];
                        }
                    }
                }
            }
        }

        error_log('Template Debug: ' . print_r($debug_info, true));
    }
    return $template;
}, 999);

/** 
 * Plugin-Isolation Test Erstellen Sie eine Funktion zum schnellen Testen: 
 */

function test_template_without_plugins($plugins_to_test = array())
{
    if (!is_user_logged_in() || !current_user_can('manage_options')) return;

    if (isset($_GET['test_templates'])) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $active_plugins = get_option('active_plugins');

        // Wenn keine spezifischen Plugins angegeben, alle deaktivieren
        if (empty($plugins_to_test)) {
            update_option('temporary_plugins_backup', $active_plugins);
            update_option('active_plugins', array());
        } else {
            // Nur bestimmte Plugins aktiv lassen
            $new_active_plugins = array_intersect($active_plugins, $plugins_to_test);
            update_option('temporary_plugins_backup', $active_plugins);
            update_option('active_plugins', $new_active_plugins);
        }

        error_log('Templates werden ohne Plugins getestet');
    }

    // Plugins wiederherstellen
    if (isset($_GET['restore_plugins'])) {
        $backed_up_plugins = get_option('temporary_plugins_backup');
        if ($backed_up_plugins) {
            update_option('active_plugins', $backed_up_plugins);
            delete_option('temporary_plugins_backup');
            error_log('Plugins wurden wiederhergestellt');
        }
    }
}
add_action('init', 'test_template_without_plugins');

/**
 * Performance Impact Logging Um zu sehen, welche Plugins die Template-Verarbeitung verlangsamen:
 */

function log_template_processing_time()
{
    if (!is_user_logged_in() || !current_user_can('manage_options')) return;

    static $start_time;

    add_action('template_redirect', function () use (&$start_time) {
        $start_time = microtime(true);
    }, -999);

    add_action('template_include', function ($template) use (&$start_time) {
        $end_time = microtime(true);
        $duration = ($end_time - $start_time) * 1000; // in Millisekunden

        error_log(sprintf(
            'Template Processing Time: %.2fms | Template: %s',
            $duration,
            basename($template)
        ));

        return $template;
    }, 999);
}
add_action('init', 'log_template_processing_time');

/**
 * Plugin Load Order Check Um zu sehen, in welcher Reihenfolge Plugins geladen werden:
 */
function check_plugin_load_order()
{
    if (!is_user_logged_in() || !current_user_can('manage_options')) return;

    add_action('plugins_loaded', function () {
        global $wp_filter;
        $loaded_plugins = array();

        foreach ($wp_filter as $tag => $priorities) {
            foreach ($priorities as $callbacks) {
                foreach ($callbacks as $callback) {
                    if (is_array($callback['function']) && is_object($callback['function'][0])) {
                        $class = get_class($callback['function'][0]);
                        if (!in_array($class, $loaded_plugins)) {
                            $loaded_plugins[] = $class;
                        }
                    }
                }
            }
        }

        error_log('Plugin Load Order: ' . print_r($loaded_plugins, true));
    }, 999);
}
add_action('init', 'check_plugin_load_order');
