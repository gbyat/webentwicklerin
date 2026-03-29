<?php
/**
 * Force font-display: swap for fonts on the user Theme JSON layer (Font Library).
 *
 * Theme fonts from theme.json already set fontDisplay in the theme file; no filter needed
 * there. wp_print_font_faces() has no hook; installed fonts default to fallback in core.
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets fontDisplay to swap on each fontFace entry, for every origin key.
 *
 * @param array $font_families Typography fontFamilies from Theme JSON settings.
 * @return array
 */
function webentwicklerin_font_families_force_display_swap( $font_families ) {
	if ( empty( $font_families ) || ! is_array( $font_families ) ) {
		return $font_families;
	}

	foreach ( $font_families as $origin => $families ) {
		if ( ! is_array( $families ) ) {
			continue;
		}
		foreach ( $families as $i => $family ) {
			if ( empty( $family['fontFace'] ) || ! is_array( $family['fontFace'] ) ) {
				continue;
			}
			foreach ( $family['fontFace'] as $j => $face ) {
				if ( ! is_array( $face ) ) {
					continue;
				}
				$font_families[ $origin ][ $i ]['fontFace'][ $j ]['fontDisplay'] = 'swap';
			}
		}
	}

	return $font_families;
}

/**
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data object.
 * @return WP_Theme_JSON_Data
 */
function webentwicklerin_filter_theme_json_font_display_swap( $theme_json ) {
	if ( ! is_object( $theme_json ) || ! method_exists( $theme_json, 'get_data' ) || ! method_exists( $theme_json, 'update_with' ) ) {
		return $theme_json;
	}

	$data = $theme_json->get_data();
	if ( empty( $data['settings']['typography']['fontFamilies'] ) || ! is_array( $data['settings']['typography']['fontFamilies'] ) ) {
		return $theme_json;
	}

	$families = webentwicklerin_font_families_force_display_swap( $data['settings']['typography']['fontFamilies'] );

	return $theme_json->update_with(
		array(
			'version'  => isset( $data['version'] ) ? $data['version'] : 3,
			'settings' => array(
				'typography' => array(
					'fontFamilies' => $families,
				),
			),
		)
	);
}

add_filter( 'wp_theme_json_data_user', 'webentwicklerin_filter_theme_json_font_display_swap', 20 );
