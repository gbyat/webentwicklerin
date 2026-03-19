<?php
/**
 * Navigation breakpoint controls.
 *
 * Adds a configurable responsive breakpoint to the Navigation block and
 * applies it on the frontend via block render filtering.
 *
 * @package webentwicklerin
 */

namespace Webentwicklerin\NavigationBreakpoint;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers server-side attributes for the Navigation block.
 *
 * @return void
 */
function register_navigation_attributes() {
	if ( ! class_exists( '\WP_Block_Type_Registry' ) ) {
		return;
	}

	$registry_class   = '\WP_Block_Type_Registry';
	$navigation_block = $registry_class::get_instance()->get_registered( 'core/navigation' );

	if ( ! $navigation_block ) {
		return;
	}

	$navigation_block->attributes['webentwicklerinBreakpoint'] = array(
		'type'    => 'number',
		'default' => 600,
	);

	$navigation_block->attributes['webentwicklerinMobileIconAlign'] = array(
		'type'    => 'string',
		'default' => 'inherit',
	);
}

/**
 * Returns a safe breakpoint value.
 *
 * @param mixed $value Breakpoint value.
 * @return int
 */
function sanitize_breakpoint( $value ) {
	$breakpoint = absint( $value );

	if ( $breakpoint < 320 ) {
		return 320;
	}

	if ( $breakpoint > 1920 ) {
		return 1920;
	}

	return $breakpoint;
}

/**
 * Returns a safe mobile icon alignment value.
 *
 * @param mixed $value Alignment value.
 * @return string
 */
function sanitize_mobile_icon_alignment( $value ) {
	$allowed_values = array( 'inherit', 'left', 'center', 'right' );

	if ( is_string( $value ) && in_array( $value, $allowed_values, true ) ) {
		return $value;
	}

	return 'inherit';
}

/**
 * Returns CSS declarations for mobile icon alignment.
 *
 * @param string $alignment Alignment value.
 * @return array
 */
function get_mobile_icon_alignment_css( $alignment ) {
	if ( 'left' === $alignment ) {
		return 'margin-left:0!important;margin-right:auto!important;';
	}

	if ( 'center' === $alignment ) {
		return 'margin-left:auto!important;margin-right:auto!important;';
	}

	if ( 'right' !== $alignment ) {
		return '';
	}

	return 'margin-left:auto!important;margin-right:0!important;';
}

/**
 * Returns the overlay menu mode used by the Navigation block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function get_overlay_menu_mode( $attributes ) {
	if ( empty( $attributes['overlayMenu'] ) ) {
		// Core defaults to mobile overlay when the setting is not explicitly saved.
		return 'mobile';
	}

	return (string) $attributes['overlayMenu'];
}

/**
 * Adds custom responsive breakpoint and icon alignment CSS to Navigation block output.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Parsed block data.
 * @return string
 */
function render_navigation_with_breakpoint( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || 'core/navigation' !== $block['blockName'] ) {
		return $block_content;
	}

	$attributes = isset( $block['attrs'] ) ? $block['attrs'] : array();
	$has_custom_breakpoint = isset( $attributes['webentwicklerinBreakpoint'] );
	$has_custom_alignment  = isset( $attributes['webentwicklerinMobileIconAlign'] );
	$alignment             = sanitize_mobile_icon_alignment(
		$has_custom_alignment ? $attributes['webentwicklerinMobileIconAlign'] : 'inherit'
	);
	$has_effective_alignment = $has_custom_alignment && 'inherit' !== $alignment;
	$overlay_mode          = get_overlay_menu_mode( $attributes );
	$has_mobile_overlay    = 'mobile' === $overlay_mode;
	$has_toggle_icon       = $has_mobile_overlay || 'always' === $overlay_mode;
	$breakpoint            = sanitize_breakpoint(
		$has_custom_breakpoint ? $attributes['webentwicklerinBreakpoint'] : 600
	);

	// Keep core behavior untouched when no custom value is saved.
	if ( ( ! $has_mobile_overlay && ! $has_toggle_icon ) || ( ! $has_custom_breakpoint && ! $has_effective_alignment ) ) {
		return $block_content;
	}

	if ( ! class_exists( '\WP_HTML_Tag_Processor' ) ) {
		return $block_content;
	}

	$processor_class = '\WP_HTML_Tag_Processor';
	$processor       = new $processor_class( $block_content );
	if ( ! $processor->next_tag( 'nav' ) ) {
		return $block_content;
	}

	$nav_id = $processor->get_attribute( 'id' );
	if ( empty( $nav_id ) ) {
		$nav_id = 'nav-' . wp_unique_id();
		$processor->set_attribute( 'id', $nav_id );
	}

	$updated_content  = $processor->get_updated_html();
	$escaped_nav_id   = esc_attr( $nav_id );
	$css_rules        = '';

	if ( $has_mobile_overlay && $has_custom_breakpoint ) {
		$max_breakpoint = max( 0, $breakpoint - 1 );
		$css_rules .= sprintf(
			'@media (max-width:%1$dpx){#%2$s .wp-block-navigation__responsive-container-open:not(.always-shown){display:flex!important;}#%2$s .wp-block-navigation__responsive-container:not(.is-menu-open):not(.hidden-by-default){display:none!important;}}@media (min-width:%3$dpx){#%2$s .wp-block-navigation__responsive-container-open:not(.always-shown){display:none!important;}#%2$s .wp-block-navigation__responsive-container:not(.is-menu-open):not(.hidden-by-default){display:block!important;}}',
			$max_breakpoint,
			$escaped_nav_id,
			$breakpoint
		);
	}

	if ( $has_toggle_icon && $has_effective_alignment ) {
		$button_css    = get_mobile_icon_alignment_css( $alignment );
		$alignment_rule = sprintf(
			'#%1$s{width:100%%!important;}#%1$s .wp-block-navigation__responsive-container-open{%2$s}',
			$escaped_nav_id,
			$button_css
		);

		if ( $has_mobile_overlay ) {
			$max_breakpoint = max( 0, $breakpoint - 1 );
			$css_rules .= sprintf(
				'@media (max-width:%1$dpx){%2$s}',
				$max_breakpoint,
				$alignment_rule
			);
		} else {
			$css_rules .= $alignment_rule;
		}
	}

	return '<style>' . $css_rules . '</style>' . $updated_content;
}

add_action( 'init', __NAMESPACE__ . '\register_navigation_attributes', 100 );
add_filter( 'render_block', __NAMESPACE__ . '\render_navigation_with_breakpoint', 10, 2 );
