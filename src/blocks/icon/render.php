<?php

/**
 * Icon Block - Server-side rendering
 *
 * @package webentwicklerin
 * @since 2.0.0
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

// Get icons
$icons = function_exists('webethm_get_icons') ? webethm_get_icons() : [];

// Get attributes with defaults
$icon_name = isset($attributes['iconName']) ? $attributes['iconName'] : 'arrow-up';
$icon_color = isset($attributes['customIconColor']) ? $attributes['customIconColor'] : (isset($attributes['iconColor']) ? $attributes['iconColor'] : '');
$background_color = isset($attributes['customBackgroundColor']) ? $attributes['customBackgroundColor'] : (isset($attributes['backgroundColor']) ? $attributes['backgroundColor'] : '');
$hover_icon_color = isset($attributes['customHoverIconColor']) ? $attributes['customHoverIconColor'] : (isset($attributes['hoverIconColor']) ? $attributes['hoverIconColor'] : '');
$hover_background_color = isset($attributes['customHoverBackgroundColor']) ? $attributes['customHoverBackgroundColor'] : (isset($attributes['hoverBackgroundColor']) ? $attributes['hoverBackgroundColor'] : '');
$width = isset($attributes['width']) ? $attributes['width'] : '24px';
$height = isset($attributes['height']) ? $attributes['height'] : '24px';

// Get the icon SVG
$current_icon = isset($icons[$icon_name]) ? $icons[$icon_name] : '';

if (empty($current_icon)) {
    return;
}

// Build inline styles
$style_attr = '';
$styles = [];

if (!empty($icon_color)) {
    $styles[] = '--icon-color: ' . esc_attr($icon_color);
}
if (!empty($background_color)) {
    $styles[] = '--icon-bg: ' . esc_attr($background_color);
}
if (!empty($hover_icon_color)) {
    $styles[] = '--icon-hover-color: ' . esc_attr($hover_icon_color);
}
if (!empty($hover_background_color)) {
    $styles[] = '--icon-hover-bg: ' . esc_attr($hover_background_color);
}
if (!empty($width)) {
    $styles[] = 'width: ' . esc_attr($width);
}
if (!empty($height)) {
    $styles[] = 'height: ' . esc_attr($height);
}

if (!empty($styles)) {
    $style_attr = ' style="' . implode('; ', $styles) . '"';
}

// Get block wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes([
    'style' => !empty($styles) ? implode('; ', $styles) : null,
]);

// Define allowed SVG tags for output
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
?>
<div <?php echo $wrapper_attributes; ?>>
    <div class="wp-block-webentwicklerin-icon__inner">
        <?php echo wp_kses($current_icon, $allowed_svg_tags); ?>
    </div>
</div>